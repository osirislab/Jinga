<?php
require("mysql_info.php");
require("utils.php");
$con = mysqli_connect($host,$admin,$dbpwd,$database) or die('Failed to connect'); 

if(isset($_POST['login'])) {
    login();
    unset($_POST['login']);
}
if(isset($_POST['register'])) {
    register();
    unset($_POST['register']);
}
if(isset($_SESSION['user'])) {
    header('Location: main.php'); 
    exit;
}
?>
<html>
    <head>
        <h1>Welcome to Jinga!</h1>
    </head>
    <body>
        <form id='login' method='post' accept-charset='UTF-8'>
            <fieldset>
                <legend>Login</legend>
                <input type='hidden' name='login' id='login' value='1'/>
                <label for='username' >UserName:</label>
                <input type='text' name='username' id='username'  maxlength="20" />
                <label for='password' >Password:</label>
                <input type='password' name='password' id='password' maxlength="20" />
                <input type='submit' name='Submit' value='Log In' />
            </fieldset>
        </form>
        <form id='register' method='post' accept-charset='UTF-8'> 
            <fieldset>
                <legend>Register</legend>
                    <input type='hidden' name='register' id='register' value='1'/>
                    <label for='username' >UserName:</label>
                    <input type='text' name='username' id='username'  maxlength="20" /><br>
                    <label for='password' >Password:</label>
                    <input type='password' name='password' id='password' maxlength="20" /><br>
                    <label for='password' >First Name:</label>
                    <input type='text' name='firstname' id='firstname' maxlength="20" /><br>
                    <label for='password' >Last Name:</label>
                    <input type='text' name='lastname' id='lastname' maxlength="20" /><br>
                    <input type='hidden' name='latitude' id='latitude' value="" />
                    <input type='hidden' name='longitude' id='longitude' value="" />
                    <input type='submit' name='Submit' value='Register' />
            </fieldset>
        </form>
    </body>
    <script>
        navigator.geolocation.getCurrentPosition(function(position) {  
            lat = position.coords.latitude;
            lon = position.coords.longitude;
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lon;
        });
    </script>
</html>