<?php
session_start();
//include password hasher
include_once("../lib/makepassword.php");

//fill session values if post exists
if (isset($_POST)){
    if (isset($_POST['username'])){
        $_SESSION['username'] = $_POST['username'];
    }
    if (isset($_POST['fullusername'])){
        $_SESSION['fullusername'] = $_POST['fullusername'];
    }
    if (isset($_POST['password'])){
        //check if password is not empty than hash it if it isn't
        if (strlen($_POST['password']) != 0){
            $_SESSION['password'] = makepassword($_POST['password']);
        }
    }
    if (isset($_POST['hostname'])){
        $_SESSION['hostname'] = $_POST['hostname'];
    }
    if (isset($_POST['keyboard'])){
        $_SESSION['keyboard'] = $_POST['keyboard'];
    }
    if (isset($_POST['lang'])){
        $_SESSION['lang'] = $_POST['lang'];
    }
    if (isset($_POST['locale'])){
        $_SESSION['locale'] = $_POST['locale'];
    }
    if (isset($_POST['Tag'])){
        $_SESSION['Tag'] = $_POST['Tag'];
    }
    if (isset($_POST['Continent'])){
        $_SESSION['continent'] = $_POST['Continent'];
    }
}
//if post doesn't exist redirect
else {
    header("Location: " . "../");
    exit("<div style='font-size:80vh;text-align:center;width:100%;white-space:nowrap;'>:-(</div>");
}
//print the links
print('
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Kickstart Generator</title>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
        <div>
            <a class="button" href="../save/">Save in database (overwrites previous config)</a>
            <a class="button" href="../download/">Download file</a>
        </div>
    </body>
    </html>
');
