<?php
/*
TODO:
- Put current location of each user in map
- Search
- Add friend

*/

   /* if (isset($_POST['searchnote'])) {
        searchNote();
    }*/

    function login() {
        if(empty($_POST['username'])) {
            echo "no user"; 
            return false;
        }
        
        if(empty($_POST['password'])) {
            echo "no password";
            return false;
        }
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if(!CheckLoginInDB($username,$password)) {
            return false;
        }
        
        session_start();
        
        $_SESSION['user'] = $username;
        $_SESSION['filter'] = 0;
        return true;
    }
    
    function register() {
        if(empty($_POST['username'])) {
            echo "no user"; 
            return false;
        }
        
        if(empty($_POST['password'])) {
            echo "no password";
            return false;
        }
        if(empty($_POST['firstname'])) {
            echo "no first name"; 
            return false;
        }
        
        if(empty($_POST['lastname'])) {
            echo "no last name";
            return false;
        }        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        
        global $con;
        $query = "SELECT username FROM users WHERE username = '".$username."'"; 
        $result = mysqli_query($con,$query);
        if (mysqli_num_rows($result) > 0) {
            echo ('Username already exists!');
            return false;
        }
        $query = "INSERT INTO users (username,password,firstname,lastname,latitude,longitude) value ('".$username."','".$password."','".$firstname."','".$lastname."',".$latitude.",".$longitude.")"; 
        $result = mysqli_query($con,$query);
        if(mysqli_affected_rows($con) > 0 ) {
            echo "You have successfully registered.";
        }
        return true;
    }
    
    function CheckLoginInDB($username,$password) {   
        global $con;
        $query = "SELECT firstname,lastname FROM users WHERE username = '".$username."' and password = '".$password."'"; 
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        if(!$result || mysqli_num_rows($result) <= 0) {
            echo("Error logging in. ".
                "The username or password does not match");
            return false;
        }
        return true;
    }
    
    function logout() {
        header('Location: logout.php');
        return true;
    }
    
    function curLocation() {
        global $con;
        $query = "SELECT * FROM users WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        while ($row = mysqli_fetch_assoc($result)){
            echo $row['latitude'].",".$row['longitude'];
        }
    }
    
    function curTime() {
        global $con;
        $query = "SELECT * FROM users WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        while ($row = mysqli_fetch_assoc($result)){
            echo $row['lastupdate'];
        }
    }
    
    function searchNote() {

        global $con;
        $datetime = date('Y-m-d H:i:s');
        $latitude = "";
        $longitude = "";
        $tags = "";
        if(!empty($_POST['searchtime'])) $datetime = $_POST['searchtime'];
        if(!empty($_POST['searchlat'])) $latitude = $_POST['searchlat'];
        if(!empty($_POST['searchlng'])) $longitude = $_POST['searchlng']; 
        if(!empty($_POST['tags'])) $tags = explode(" ",$_POST['tags']);
        $query = "SELECT username,content,postedtime FROM notes join users on users.userid = notes.userid WHERE eventid IN (SELECT eventid FROM events WHERE '".$datetime."' BETWEEN startdate and enddate)";
        if($latitude !== "") {
            $query .= " AND (".$latitude." BETWEEN (notes.latitude-(radius/111)) and (notes.latitude+(radius/111))) AND (".$longitude." BETWEEN (notes.longitude-(radius/1852)) and (notes.longitude+(radius/1852)))";
        }
        if($tags !== "") {
            $i = 0;
            $len = count($tags);
            $query .= " AND noteid IN (SELECT noteid FROM notes_tags WHERE tagid IN (select * from (";
            foreach($tags as $tag) {
                if($i !== ($len - 1)) $query .= "(SELECT tagid FROM tags WHERE tag LIKE '".$tag."') UNION ";
                else $query .= "(SELECT tagid FROM tags WHERE tag LIKE '".$tag."')) as t))";
                $i++;
            }
        }
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        $text = "";
        if(!$result || mysqli_num_rows($result) <= 0) {
            return false;
        }
        echo "<table border=\"1\"><th>Username</th><th>Content</th><th>Time Posted</th>";
        while ($row = mysqli_fetch_assoc($result)){
            echo "<tr><td>".$row['username']."</td><td>\"".$row['content']."\"</td><td>".$row['postedtime']."</td></tr>";
        }
        echo "</table>";
        return true;
    }
    
    function createNote() {
        global $con;

        if(empty($_POST['content'])) {
            echo "no content"; 
            return false;
        }
        if(empty($_POST['fromdate'])) {
            echo "no starting date"; 
            return false;
        }
        if(empty($_POST['todate'])) {
            echo "no ending date"; 
            return false;
        }
        $user = $_SESSION['user'];
        $content = $_POST['content'];
        $url = $_POST['url'];
        $tags = ""; 
        if(!empty($_POST['tags'])) $tags = explode(" ",$_POST['tags']);
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $radius = $_POST['radius'];
        $fromdate = $_POST['fromdate'];
        $todate = $_POST['todate'];
        $repeatdate = '';
        $recurrence = '';
        $comment = 0;
        $repeat = false;
        
        if(isset($_POST['comment']) && $_POST['comment'] == 'Yes') {
            $comment = 1;
        }

        if(isset($_POST['repeat']) && $_POST['repeat'] == 'Yes') {
            $repeat = true;
            $repeatdate = $_POST['repeatdate']; 
            $recurrence = $_POST['recurrence'];
        }

        // Insert event
        $query1 = "INSERT INTO events (refid,startdate,enddate) value (0, '".$fromdate."', '".$todate."')";
        $result = mysqli_query($con,$query1);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        // Get id of event
        $eventid = mysqli_insert_id($con);
        $query2 = "UPDATE events SET refid = eventid where eventid = ".$eventid;
        $result = mysqli_query($con,$query2);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        // Insert note
        $query3 = "INSERT INTO notes (userid,content,url,latitude,longitude,radius,eventid,boolcomment)
                    values ((select userid from users where username = '".$user."'),
                    '$content','".$url."',".$latitude.", ".$longitude.", ".$radius.",".$eventid.",".$comment.")";
        $result = mysqli_query($con,$query3);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        // Get id of note
        $noteid = mysqli_insert_id($con);
        // If repeat, insert recurring events
        if($repeat) {
            $query4 = "UPDATE events SET refid = eventid where eventid = ".$eventid;
            $result = mysqli_query($con,$query4);
            if (!$result) die('Invalid query: ' . mysqli_error($con));
            $newfromdate = $fromdate;
            $newtodate = $todate;
            $repetition = '';
            if($recurrence == 'daily') {
                $repetition = 'days';
            }
            if($recurrence == 'weekly') {
                $repetition = 'weeks';
            }
            if($recurrence == 'monthly') {
                $repetition = 'months';
            }
            if($recurrence == 'yearly') {
                $repetition = 'years';
            }
            while(strtotime($newtodate) <= strtotime($repeatdate)) {
                $newfromdate = date('Y-m-d H:i:s',strtotime($newfromdate.' + 1'.$repetition));
                $newtodate = date('Y-m-d H:i:s',strtotime($newtodate.' + 1'.$repetition));
                $query = "INSERT INTO events (refid,startdate,enddate) value (".$eventid.", '".$newfromdate."', '".$newtodate."')";
                $result = mysqli_query($con,$query);
                if (!$result) die('Invalid query: ' . mysqli_error($con));
            }
        }

        // Insert tags
        if($tags !== "") {
            foreach($tags as $tag) {
                $tagquery = "INSERT IGNORE INTO tags (tag) value ('".$tag."')";
                $result = mysqli_query($con,$tagquery);
                if (!$result) die('Invalid query: ' . mysqli_error($con));
                $tagnotequery = "INSERT into notes_tags (noteid,tagid) values (".$noteid.",(select tagid from tags where tag = '".$tag."'))";
                $result = mysqli_query($con,$tagnotequery);
                if (!$result) die('Invalid query: ' . mysqli_error($con));
            }
        }

        return true;
    }

    function createFilter() {
        global $con;
        if(empty($_POST['filtername'])) {
            echo "no filter name"; 
            return false;
        }
        if(empty($_POST['filterfromdate'])) {
            echo "no filterfromdate"; 
            return false;
        }
        if(empty($_POST['filtertodate'])) {
            echo "no filtertodate"; 
            return false;
        }
        if(empty($_POST['filteruntildate'])) {
            echo "no filteruntildate"; 
            return false;
        }
        $user = $_SESSION['user'];
        $filtername = $_POST['filtername'];
        $filterfromdate = $_POST['filterfromdate'];
        $filtertodate = $_POST['filtertodate'];
        $filteruntildate = $_POST['filteruntildate'];
        $filterrecurrence = $_POST['filterrecurrence'];
        $filterlat = $_POST['filterlat'];
        $filterlng = $_POST['filterlng'];
        $filterradius = $_POST['filterradius'];
        $tags = "";
        if(!empty($_POST['tags'])) $tags = explode(" ",$_POST['tags']);
        
        // Insert filter event
        $query1 = "INSERT INTO filter_events (refid,startdate,enddate) value (0, '".$filterfromdate."', '".$filtertodate."')";
        $result = mysqli_query($con,$query1);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        // Get id of filter event
        $feid = mysqli_insert_id($con);
                
        // Insert filter
        $query3 = "INSERT INTO filters (userid,status,latitude,longitude,radius,feid)
                    values ((select userid from users where username = '".$user."'),
                    '".$filtername."',".$filterlat.", ".$filterlng.", ".$filterradius.",".$feid.")";
        $result = mysqli_query($con,$query3);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        // Get id of filter
        $filterid = mysqli_insert_id($con);
        
        $query4 = "UPDATE filter_events SET refid = feid where feid = ".$feid;
        $result = mysqli_query($con,$query4);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        $newfromdate = $filterfromdate;
        $newtodate = $filtertodate;
        $repetition = '';
        if($filterrecurrence == 'daily') {
            $repetition = 'days';
        }
        if($filterrecurrence == 'weekly') {
            $repetition = 'weeks';
        }
        if($filterrecurrence == 'monthly') {
            $repetition = 'months';
        }
        if($filterrecurrence == 'yearly') {
            $repetition = 'years';
        }
        while(strtotime($newtodate) <= strtotime($filteruntildate)) {
            $newfromdate = date('Y-m-d H:i:s',strtotime($newfromdate.' + 1'.$repetition));
            $newtodate = date('Y-m-d H:i:s',strtotime($newtodate.' + 1'.$repetition));
            $query = "INSERT INTO filter_events (refid,startdate,enddate) value (".$feid.", '".$newfromdate."', '".$newtodate."')";
            $result = mysqli_query($con,$query);
            if (!$result) die('Invalid query: ' . mysqli_error($con));
        }
        
        // Insert tags
        if($tags !== "") {
            foreach($tags as $tag) {
                $tagquery = "INSERT IGNORE INTO tags (tag) value ('".$tag."')";
                $result = mysqli_query($con,$tagquery);
                if (!$result) die('Invalid query: ' . mysqli_error($con));
                $tagnotequery = "INSERT into filter_tags (filterid,tagid) values (".$filterid.",(select tagid from tags where tag = '".$tag."'))";
                $result = mysqli_query($con,$tagnotequery);
                if (!$result) die('Invalid query: ' . mysqli_error($con));
            }
        }
        
        return true;
    }
   
    function getFirstName() {
        global $con;
        $query = "SELECT firstname FROM users WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        while ($row = mysqli_fetch_assoc($result)){
            echo $row['firstname'];
        }
    }
    
    function getLastName() {
        global $con;
        $query = "SELECT lastname FROM users WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        while ($row = mysqli_fetch_assoc($result)){
            echo $row['lastname'];
        }
    }
    
    function setFirstName() {
        global $con;
        $firstname = $_POST['firstname'];
        $query = "UPDATE users SET firstname = '".$firstname."' WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
    }
    
    function setLastName() {
        global $con;
        $lastname = $_POST['lastname'];
        $query = "UPDATE users SET lastname = '".$lastname."' WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
    }
    
    function setPassword() {
        global $con;
        $oldpassword = $_POST['oldpwd'];
        $newpassword = $_POST['newpwd'];
        $renewpassword = $_POST['renewpwd'];
        $query = "SELECT password FROM users WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        $row = mysqli_fetch_assoc($result);
        if($newpassword !== $renewpassword) {
            echo "Passwords don't match";
            return false;
        }
        if($oldpassword !== $row['password']) {
            echo "Passwords don't match";
            return false;
        }
        
        $query = "UPDATE users SET password = '".$newpassword."' WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        return true;
    }
    
    function setLocation() {
        global $con;
        if(empty($_POST['newlat'])) {
            echo "no content"; 
            return false;
        }
        if(empty($_POST['newlng'])) {
            echo "no content"; 
            return false;
        }
        $latitude = $_POST['newlat'];
        $longitude = $_POST['newlng'];
        $query = "UPDATE users SET latitude = ".$latitude.", longitude = ".$longitude." WHERE username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        return true;
    }
    
    function setTime() {
        global $con;
        $user = $_SESSION['user'];
        $time = $_POST['newtime'];
        $query = "UPDATE users SET lastupdate = '".$time."' WHERE username`= '".user."'";
    }
    
    function getNoteTable() {
        global $con;
        $user = $_SESSION['user'];
        $query = "SELECT * FROM notes WHERE userid = (SELECT userid FROM users WHERE username = '".$user."')";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        while ($row = mysqli_fetch_assoc($result)){
            echo "<tr><td>".$row['content']."</td><td><a href=\"".$row['url']."\">".$row['url']."</td><td>".$row['latitude']."</td><td>".$row['longitude']."</td><td>".$row['postedtime']."</td><td>".convertBoolComment($row['boolcomment'])."</tr>";
        }
    }
    
    function convertBoolComment($boolean) {
        if($boolean == 1) return "Yes";
        else return "No";
    }
    
    function placeMarkers() {
        global $con;
        $query = "SELECT * FROM notes";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        while ($row = mysqli_fetch_assoc($result)){
            $subquery = "SELECT username FROM users WHERE userid = \"".$row['userid']."\"";
            $subresult = mysqli_query($con,$subquery);
            if (!$subresult) die('Invalid query: ' . mysqli_error($con));
            $subrow = mysqli_fetch_assoc($subresult);
            echo "var title = \"&quot".$row['content']."&quot<br><a href='".$row['url']."'>".$row['url']."</a><br>- <i>".$subrow['username']."</i>\";
            var point = new google.maps.LatLng( 
                parseFloat(".$row['latitude']."), 
                parseFloat(".$row['longitude'].")); 
            var maker = addMarker(point,\"".$row['content']."\");   
            bindInfoWindow(marker,map,infoWindow,title);";
        }
    }
    
    function insertComment() {
        global $con;
        $userid = $row['userid'];
        $noteid = $row['noteid'];
        $comment = $row['comment'];
        $query = "INSERT INTO comments (userid,noteid,textcomment) value (".$userid.",".$noteid.",'".$comment."')";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
    }
    
    function addFriend() {
        global $con;
        if(empty($_POST['userid'])) {
            echo "no userid"; 
            return false;
        }
        $friend = $_POST['userid'];
        $query = "SELECT userid FROM users WHERE username = \"".$_SESSION['user']."\"";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        $row = mysqli_fetch_assoc($result);
        $userid = $row['userid'];
        $query = "SELECT userid FROM users WHERE username = \"".$friend."\"";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        $row = mysqli_fetch_assoc($result);
        $friendid = $row['userid'];
        $query = "SELECT * FROM friendship WHERE userid = ".$userid." and friendid = ".$friendid."";
        $result = mysqli_query($con,$query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if($row['status'] === 0) echo "You have already sent a request!";
            elseif($row['status'] === 1) echo "You are already friends with this person!";
            //return false;
        }
        $query = "INSERT INTO friendship (userid,friendid,status) value (".$userid.",".$friendid.",0)";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        echo "<script>alert(\"Friend request added.\")</script>";
        //return true;
    }
    
    function showFriendRequests() {
        global $con;
        $query = "SELECT userid FROM users WHERE username = \"".$_SESSION['user']."\"";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        $row = mysqli_fetch_assoc($result);
        $friendid = $row['userid'];
        $query = "SELECT userid,username,firstname,lastname FROM users WHERE userid IN (SELECT userid FROM friendship WHERE friendid = ".$friendid." and status = 0)";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        if(!$result || mysqli_num_rows($result) <= 0) {
            echo "You have no friend requests at this time.";
            return false;
        }
        echo "<form id=\"request\" method=\"post\"><input type=\"hidden\" name=\"request\" value=\"true\" />";
        while ($row = mysqli_fetch_assoc($result)){
            echo "&bull; ".$row['firstname'].' '.$row['lastname'].' ('.$row['username'].") <input type=\"submit\" id=\"ignore_".$row['userid']."\" name=\"ignore_".$row['userid']."\" value=\"Ignore\" style=\"float: right;\"> <input type=\"submit\" id=\"accept_".$row['userid']."\" name=\"accept_".$row['userid']."\" value=\"Accept\" style=\"float: right;\"><br><br>";
        }
        echo "</form>";
        return true;
    }
    
    function respondFriendRequest() {
        global $con;
        foreach($_POST as $key => $value) {
            if(startsWith($key,'accept_')) {
                if(isset($_POST[$key])) {
                    $friendid = intval(substr($key, strlen('accept_')));
                    $queryupdate = "UPDATE friendship SET status = 1 WHERE userid = ".$friendid." and friendid = (SELECT userid FROM users WHERE username LIKE \"".$_SESSION['user']."\")";
                    $result = mysqli_query($con,$queryupdate);
                    if (!$result) die('Invalid query: ' . mysqli_error($con));
                    
                    $queryinsert = "INSERT INTO friendship (userid,friendid,status) value ((SELECT userid FROM users WHERE username LIKE \"".$_SESSION['user']."\"),".$friendid.",1)";
                    $result = mysqli_query($con,$queryinsert);
                    if (!$result) die('Invalid query: ' . mysqli_error($con));
                    return true;
                }
            }
            if(startsWith($key,'ignore_')) {
                if(isset($_POST[$key])) {
                    $friendid = intval(substr($key, strlen('ignore_')));
                    $querydelete = "DELETE FROM friendship WHERE userid = ".$friendid." and friendid =  (SELECT userid FROM users WHERE username LIKE \"".$_SESSION['user']."\")";
                    $result = mysqli_query($con,$querydelete);
                    if (!$result) die('Invalid query: ' . mysqli_error($con));
                    if(!$result || mysqli_num_rows($result) <= 0) {
                        return false;
                    }
                    return true;
                }
            }
        }
    }
    
    function showFriends() {
        global $con;
        $query = "select friend.username, friend.firstname, friend.lastname from friendship f join users user on f.userid = user.userid join users friend on f.friendid = friend.userid where status = 1 and user.username = '".$_SESSION['user']."'";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        if(!$result || mysqli_num_rows($result) <= 0) {
            return false;
        }
       while ($row = mysqli_fetch_assoc($result)){
            echo "<tr><td>".$row['username']."</td><td>".$row['firstname']."</td><td>".$row['lastname']."</td></tr>";
        }   
    }
    
    function startsWith($haystack, $needle) {
        return !strncmp($haystack, $needle, strlen($needle));
    }
    
    function showCurrentFilters() {
        global $con;
        $query = "SELECT userid FROM users WHERE username = \"".$_SESSION['user']."\"";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        $row = mysqli_fetch_assoc($result);
        $userid = $row['userid'];
        $query = "SELECT * FROM filters WHERE userid = ".$userid;
        if($_SESSION['filter'] != 0) {
            $query .= " AND filterid = ".$_SESSION['filter'];
        }
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        if(!$result || mysqli_num_rows($result) <= 0) {
            echo "alert(\"You have no filters.\")";
            return false;
        }
        while ($row = mysqli_fetch_assoc($result)){
            echo "var filterOptions".$row['filterid']." = {clickable:false, strokeColor: \"#FF0000\",strokeOpacity: 0.8,strokeWeight: 2,fillColor: \"#FF0000\",fillOpacity: 0.35, map: map,center: new google.maps.LatLng(".$row['latitude'].", ".$row['longitude']."),radius: ".($row['radius']*1000)."};filterCircle".$row['filterid']." = new google.maps.Circle(filterOptions".$row['filterid'].");";
        }
        return true;
    }
    
    function getCurrentFilter() {
     global $con;
        $query = "SELECT userid FROM users WHERE username = \"".$_SESSION['user']."\"";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        $row = mysqli_fetch_assoc($result);
        $userid = $row['userid'];
        $query = "SELECT * FROM filters JOIN users ON filters.userid = users.userid JOIN filter_events ON filters.feid = filter_events.feid WHERE users.userid = ".$userid." AND (users.latitude BETWEEN filters.latitude-(radius/111) and filters.latitude+(radius/111)) AND (users.longitude BETWEEN filters.longitude-(radius/1.852) and filters.longitude+(radius/1.852))";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        if(!$result || mysqli_num_rows($result) <= 0) {
            return false;
        }
        while ($row = mysqli_fetch_assoc($result)){
            $_SESSION['filter'] = $row['filterid'];
        }
        return true;        
    }

    function userFilter() {
        $filterid = $_SESSION['filter'];
        
    }
    
    function placeMarkers2() {
        global $con;
        $filterid = $_SESSION['filter'];
        $query = "";
        if($filterid > 0) {
            $query = "SELECT notes.latitude,notes.longitude, notes.url, notes.content, notes.userid FROM 
                    notes join events on notes.eventid = events.eventid,
                    filters join filter_events ON filters.feid = filter_events.feid 
                    where notes.latitude between filters.latitude -(filters.radius/111) 
                    and filters.latitude+(filters.radius/111) 
                    and notes.longitude between filters.longitude-(filters.radius/1.852) 
                    and filters.longitude+(filters.radius/1.852) 
                    and filters.latitude between notes.latitude-(notes.radius/111) 
                    and notes.latitude+(notes.radius/111) 
                    and filters.longitude between notes.longitude-(notes.radius/1.852) 
                    and notes.longitude+(notes.radius/1.852) 
                    and ((events.startdate between filter_events.startdate and filter_events.enddate) 
                    or (events.enddate between filter_events.startdate and filter_events.enddate) 
                    or (filter_events.startdate between events.startdate and events.enddate) 
                    or (filter_events.enddate between events.startdate and events.enddate)) and filterid = ".$filterid;
            $tags = array();
            $tagquery = "SELECT * FROM filters join filter_tags on filters.filterid = filter_tags.filterid join tags on filter_tags.tagid = tags.tagid where filters.filterid = ".$filterid;
            $tagresult = mysqli_query($con,$tagquery);
            if (!$tagresult) die('Invalid query: ' . mysqli_error($con));
            while ($row = mysqli_fetch_assoc($tagresult)){
                $tags[] = $row['tag'];
            }
            if(!empty($tags)) {
                $i = 0;
                $len = count($tags);
                $query .= " AND noteid IN (SELECT noteid FROM notes_tags WHERE tagid IN (select * from (";
                foreach($tags as $tag) {
                    if($i !== ($len - 1)) $query .= "(SELECT tagid FROM tags WHERE tag LIKE '".$tag."') UNION ";
                    else $query .= "(SELECT tagid FROM tags WHERE tag LIKE '".$tag."')) as t))";
                    $i++;
                }
            }
        }
        else $query = "SELECT * FROM notes";
        $result = mysqli_query($con,$query);
        if (!$result) die('Invalid query: ' . mysqli_error($con));
        while ($row = mysqli_fetch_assoc($result)){
            $subquery = "SELECT username FROM users WHERE userid = \"".$row['userid']."\"";
            $subresult = mysqli_query($con,$subquery);
            if (!$subresult) die('Invalid query: ' . mysqli_error($con));
            $subrow = mysqli_fetch_assoc($subresult);
            echo "var title = \"&quot".$row['content']."&quot<br><a href='".$row['url']."'>".$row['url']."</a><br>- <i>".$subrow['username']."</i>\";
            var point = new google.maps.LatLng( 
                parseFloat(".$row['latitude']."), 
                parseFloat(".$row['longitude'].")); 
            addMarker(point,\"".$row['content']."\");   
            bindInfoWindow(marker,map,infoWindow,title);";
        }
    }
?>







