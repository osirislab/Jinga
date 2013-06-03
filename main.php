<?php
    session_start();
    if(!isset($_SESSION['user'])) {
        header('Location: logout.php');
    }
    require("mysql_info.php");
    require("utils.php");
    $con = mysqli_connect($host,$admin,$dbpwd,$database) or die('Failed to connect');

    if (isset($_POST['filter'])) {
        createFilter();
    }
    if(isset($_POST['create'])) {
        createNote();
    }
    if(isset($_POST['addfriend'])) {
        addFriend();
    }
    if(isset($_POST["request"])) {
        respondFriendRequest();
    }
    if(isset($_POST["upwd"])) {
        setPassword();
    }
    if(isset($_POST["uloc"])) {
        setLocation();
    }
    if(isset($_GET["logout"])) {
        logout();
    }

?>
<html>
    <head>
        <!--[if lt IE 9]>
        <script src="dist/html5shiv.js"></script>
        <![endif]-->
        <style>
            #map_canvas {
                width: 100%;
                height: 100%;
                background-color: #CCC;
            }
        </style>
        <script src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
        <script>
            var map;
            function initialize() {
                var map_canvas = document.getElementById('map_canvas');
                var map_options = {
                    center: new google.maps.LatLng(<?php curLocation() ?>),
                    zoom: 20,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                map = new google.maps.Map(map_canvas, map_options);
                var infoWindow = new google.maps.InfoWindow;
                center = addMarker(new google.maps.LatLng(<?php curLocation() ?>),"Me!");
                center.setIcon('http://maps.google.com/mapfiles/ms/icons/blue-dot.png');
                <?php getCurrentFilter(); ?> 
                <?php showCurrentFilters(); ?> 
                <?php placeMarkers2() ?>
                google.maps.event.addListener(map, "click", function(event) {
                    var lat = event.latLng.lat();
                    var lng = event.latLng.lng();
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('searchlat').value = lat;
                    document.getElementById('searchlng').value = lng;
                    document.getElementById('newlat').value = lat;
                    document.getElementById('newlng').value = lng;
                    document.getElementById('filterlat').value = lat;
                    document.getElementById('filterlng').value = lng;
                });
            }
                       
            function bindInfoWindow(marker, map, infoWindow, html) {
                google.maps.event.addListener(marker, 'click', function() {
                    infoWindow.setContent(html);
                    infoWindow.open(map, marker);
                });
            }

            function addMarker(location,text) {
                marker = new google.maps.Marker({
                position: location,
                map: map,
                title: text
                });
                return marker;
            }
            
            function doNothing() {}
            
            google.maps.event.addDomListener(window,'load',initialize);

        </script>
        <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script>
        $(document).ready(function() {
            $('.inputpanel').hide();
            option = 'create';
            showDiv(option);
        });
        function showDiv(option) {
            $('.inputpanel').hide();
            $('#' + option).show();
        }
        </script>
    </head>
    <body>
    <div id="map_canvas" style="float:left; width:49%;"></div>
    <div id="rightpanel" style="float:right; width:49%;">

        <div id="navbar" style="height:8%;width:100%;">
            <style>
                ul{list-style-type:none;margin:0;padding:0;}
                li{display:inline;}
            </style>
            <ul>
                <li><a href="javascript:showDiv('create')">Create Notes</a></li>
                <li><a href="javascript:showDiv('search')">Search Notes</a></li>
                <li><a href="javascript:showDiv('filters')">Filters</a></li>
                <li><a href="javascript:showDiv('friend')">Friends</a></li>
                <li><a href="javascript:showDiv('profile')">Profile</a></li>
                <li><a href="?logout=true">Logout</a></li>
            </ul>
            <hr>
        </div>
        <div class="inputpanel" id="create" style="height=100%;width=100%">
            <form id="create" method="post">
                <input type="hidden" name="create" value="true" />
                
                <label for='content'>Content:</label><br>
                <input type="text" name="content" maxlength="20"/><br>
                
                <label for='content'>URL (optional):</label><br>
                <input type="text" name="url" /><br>
                
                <label for='content'>Tags (separate tags using spaces):</label><br>
                <input type="text" name="tags" /><br>
                
                <label for='fromdate'>From:</label><br>
                <input type="text" id="fromdate" name="fromdate" /><br>
                
                <label for='todate'>To:</label><br>
                <input type="text" id="todate" name="todate" /><br><br>
                <label>Location (click on map to select location):</label><br>   
                <input type="text" name="latitude" id="latitude" readonly>
                <input type="text" name="longitude" id="longitude" readonly> <br>        
                <label>Radius:</label><br>
                <input type="number" name="radius" min="1" max="60" required>Kilometers<br><br>
                <input type="checkbox" id="comment" name="comment" value="Yes" />Allow Comments?<br>

                <input type="checkbox" id="repeat" name="repeat" value="Yes" />Repeat?<br>
                <div id="repeatsection" name="repeatsection" style="display:none">
                    <label>Until:</label><br>
                    <input type="text" id="repeatdate" name="repeatdate"/><br>
                    <select id="recurrence" name="recurrence">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div><br>
               <input type='submit' name='Submit' value='Create Note' />
            </form>
            <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
            <script src="jquery-ui-timepicker-addon.js"></script>
            <script>
                $("#fromdate").datetimepicker({dateFormat:"yy-mm-dd",timeFormat:"HH:mm:ss"});
                $("#todate").datetimepicker({dateFormat:"yy-mm-dd",timeFormat:"HH:mm:ss"});
                    $('#repeat').click(function() {
                        $('#repeatsection')[this.checked ? "show" : "hide"]();
                        $("#repeatdate").datepicker({dateFormat:"yy-mm-dd"});
                    });            
            </script>
        </div>
        <div class="inputpanel" id="search" style="height=100%;width=100%">
            <form id="searchnote" method="post">
                <input type="hidden" name="searchnote" value="true" />
                <b><i>Fill at least 1</i></b><br>
                <label>Date and Time (if none given, we assume current time):</label><br>
                <input type="text" name="searchtime" id="searchtime"><br>
                <label>Location (click on map to select location):</label><br>   
                <input type="text" name="searchlat" id="searchlat" readonly>
                <input type="text" name="searchlng" id="searchlng" readonly> <br>  
                <label>Tags (separate by space):</label><br>   
                <input type="text" name="tags" id="tags"> <br>
                <input type='submit' name='Submit' id="submit" value='Search Note'/>
            </form>
            <div id="searchresults"><?php searchNote(); ?></div>
            <script>
                $("#searchtime").datetimepicker({dateFormat:"yy-mm-dd",timeFormat:"HH:mm:ss"});  
            </script>
        </div>
        <div class="inputpanel" id="filters" style="height=100%;width=100%">
            <form id="filter" method="post">
                <input type="hidden" name="filter" value="true" />
                <label>Filter Name:</label><br>
                <input type="text" name="filtername" required><br>
               <label>Date and Time:</label><br>
                <input type="text" id="filterfromdate" name="filterfromdate" required/>
                <label> to </label>
                <input type="text" id="filtertodate" name="filtertodate" required/>
                <label> until </label>
                <input type="text" id="filteruntildate" name="filteruntildate" required/>
                <select id="filterrecurrence" name="filterrecurrence">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select><br>
                <label>Location (click on map to select location):</label><br>   
                <input type="text" name="filterlat" id="filterlat" readonly>
                <input type="text" name="filterlng" id="filterlng" readonly> 
                <label>Radius:</label>
                <input type="number" name="filterradius" id="filterradius" min="1" max="60" required>Kilometers<br>
                <label>Tags (separate by space):</label><br>   
                <input type="text" name="tags" id="tags"> <br>
                <input type='submit' name='Submit' value='Create Filter' /><hr>
            </form>
            <!--<p><b>Your current filters:</b></p>
            <div id="currentfilters"></div>-->
            <script>
                $("#filterfromdate").datetimepicker({dateFormat:"yy-mm-dd",timeFormat:"HH:mm:ss"});
                $("#filtertodate").datetimepicker({dateFormat:"yy-mm-dd",timeFormat:"HH:mm:ss"}); 
                $("#filteruntildate").datepicker({dateFormat:"yy-mm-dd"});                    
            </script>
        </div>
        <div class="inputpanel" id="friend" style="height=100%;width=100%">
            <form id="addfriend" method="post">
                <input type="hidden" name="addfriend" value="true" />
                <label>User ID:</label>
                <input type="text" name="userid">
                <input type='submit' name='Submit' value='Add Friend' />
            </form><hr>
            <div id="friendrequests" style="width:75%">
                <p><b>Your friend requests:</b></p>
                <?php showFriendRequests() ?>
            </div><hr>
            <div>            	
                <table border="1" style="width:100%" align="center">
                    <tr>
                        <th>Username</th>
                        <th>First name</th>
                        <th>Last name</th>
                    </tr>
                    <?php showFriends() ?>
                </table></div>
        </div>
        <div class="inputpanel" id="profile" style="height=100%;width=100%">
            <head>Hi, <?php getFirstname() ?>! The current time is <?php curTime() ?></head><br><br>
            <input type='button' id="password" value="Update Password">
            <input type='button' id="location" value="Update Location">
            <input type='button' id="time" value="Update Date and Time">
            <div id="updatepwd" name="updatepwd" >
                <form id="upwd" method="post">
                    <input type="hidden" name="upwd" value="true" />
                    <label for="oldpwd">Old Password:</label>
                    <input type="password" name="oldpwd" required/><br>
                    <label for="newpwd">New Password</label>
                    <input type="password" name="newpwd" required/><br>
                    <label for="renewpwd">Repeat New Password:</label>
                    <input type="password" name="renewpwd" required/><br>
                    <input type='submit' name='Submit' value='Update Password' />
                </form>
            </div>
            <div id="updatelocation" name="updatelocation" >
                <form id="uloc" method="post">
                    <input type="hidden" name="uloc" value="true" />
                    <label>Location (click on map to select location):</label><br>   
                    <input type="text" name="newlat" id="newlat" readonly>
                    <input type="text" name="newlng" id="newlng" readonly> <br>  
                    <input type='submit' name='Submit' value='Update Location' />
                </form>
            </div>
            <div id="updatetime" name="updatetime">
                <form id="utime" method="post">
                    <input type="hidden" name="uloc" value="true" />
                    <label>New Date and Time::</label><br> 
                    <input type="text" name="newtime" id="newtime" required/>
                    <input type='submit' name='Submit' value='Update Date and Time' />
                </form>                
            </div>
            <div id="notes" name="notes" style="overflow:auto">
                <p><b>Your Notes</b></p>
            	<table border="1" style="width:100%" align="center">
                    <tr>
                        <th>Content</th>
                        <th>URL</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Posted Time</th>
                        <th>Can Comment?</th>
                    </tr>
                    <?php getNoteTable() ?>
                </table>
            </div><br>
            <script>
            $('#updatepwd').hide();
            $('#updatelocation').hide();
            $('#updatetime').hide();
            $('#password').click(function(event) {  
                $('#updatepwd').toggle();
                $('#updatelocation').hide();
                $('#updatetime').hide();
            });
            $('#location').click(function(event) {        
                $('#updatelocation').toggle();
                $('#updatepwd').hide();
                $('#updatetime').hide();
            });
            $('#time').click(function(event) {        
                $('#updatetime').toggle();
                $('#updatepwd').hide();
                $('#updatelocation').hide();
            });
             $("#newtime").datetimepicker({dateFormat:"yy-mm-dd",timeFormat:"HH:mm:ss"}); 
            </script>
        </div>
    </div>
    </body>
</html>