<?php
session_start();
//database connection
$db = 'kickstart';
include_once("../lib/db.php");
//check if user 
if (isset($_POST['name'])) {
    //get first hit on user info
    $qwr = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $qwr->bindParam(':username', $_POST['name']);
    $qwr->execute();
    $output = $qwr->fetch(PDO::FETCH_ASSOC);

    //check if password is right
    if (password_verify($_POST['pass'], $output['password'])){
        //set login id
        $_SESSION['login'] = $output['Id'];
    }
    //if not print login form
    else {
        print(<<<frm
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>kickstart login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Wrong username or password</h2>
    <form action="./" method="post">
        <input type="text" name="name" id="name" required placeholder="Username">
        <input type="password" name="pass" id="pass" required placeholder="Password">
        <input type="submit" value="Log in">
    </form>
</body>
</html>
frm
        );
    }
}
//check if user is logged in
if (isset($_SESSION['login'])){
    
    //select the correct id for all foreign key values if they are not null or non existing else set them null just to be sure
    if (isset($_SESSION['keyboard'])){
        $qwr = $pdo->prepare('SELECT * FROM keyboard WHERE Layout = :layout');
        $qwr->bindParam(':layout', $_SESSION['keyboard']);
        $qwr->execute();
        $keyboard = $qwr->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $_SESSION['keyboard'] = null;
    }
    
    if (isset($_SESSION['lang'])){
        $qwr = $pdo->prepare('SELECT * FROM lang WHERE Language = :lang');
        $qwr->bindParam(':lang', $_SESSION['lang']);
        $qwr->execute();
        $language = $qwr->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $_SESSION['lang'] = null;
    }
    
    if (isset($_SESSION['locale'])){
        $qwr = $pdo->prepare('SELECT * FROM locale WHERE Locale = :locale');
        $qwr->bindParam(':locale', $_SESSION['locale']);
        $qwr->execute();
        $locale = $qwr->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $_SESSION['locale'] = null;
    }
    
    if (isset($_SESSION['Tag'])){
        $qwr = $pdo->prepare('SELECT * FROM tz_append WHERE Tag = :tag');
        $qwr->bindParam(':tag', $_SESSION['Tag']);
        $qwr->execute();
        $timezone = $qwr->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $_SESSION['Tag'] = null;
    }

    //check if user already has saved a record
    $qwr = $pdo->prepare('SELECT COUNT(user) FROM config WHERE user = :id');
    $qwr->bindParam(':id', $_SESSION['login']);
    $qwr->execute();
    $result = ($qwr->fetch(PDO::FETCH_NUM))[0];
    if($result > 0){
        //insert config into config table
        $qwr = $pdo->prepare('UPDATE config SET keyboard = :keyboard, lang = :lang, locale = :locale, tz_append = :tag, username = :username, fullusername = :fullusername, hostname = :hostname WHERE user = :id');
        $qwr->bindParam(':keyboard', $keyboard['Id']);
        $qwr->bindParam(':lang', $language['Id']);
        $qwr->bindParam(':locale', $locale['Id']);
        $qwr->bindParam(':tag', $timezone['Id']);
        $qwr->bindParam(':username', $_SESSION['username']);
        $qwr->bindParam(':fullusername', $_SESSION['fullusername']);
        $qwr->bindParam(':hostname', $_SESSION['hostname']);
        $qwr->bindParam(':id', $_SESSION['login']);
        $output = $qwr->execute();
        if ($output != false) {
            print('<h2>succes</h2>');
            if (isset($_SESSION['login']))print('<form action="../" method="post"><input type="submit" name="logout" value="logout"></form>');
        }
        else print('<h2>an error occured</h2>');
    }
    else{
        
        //insert config into config table
        $qwr = $pdo->prepare('INSERT INTO config (keyboard, lang, locale, tz_append, username, fullusername, hostname, user) VALUES (:keyboard, :lang, :locale, :tag, :username, :fullusername, :hostname, :user)');
        $qwr->bindParam(':keyboard', $keyboard['Id']);
        $qwr->bindParam(':lang', $language['Id']);
        $qwr->bindParam(':locale', $locale['Id']);
        $qwr->bindParam(':tag', $timezone['Id']);
        $qwr->bindParam(':username', $_SESSION['username']);
        $qwr->bindParam(':fullusername', $_SESSION['fullusername']);
        $qwr->bindParam(':hostname', $_SESSION['hostname']);
        $qwr->bindParam(':user', $_SESSION['login']);
        $output = $qwr->execute();
        if ($output != false) {
            print('<h2>succes</h2>');
            if (isset($_SESSION['login']))print('<form action="../" method="post"><input type="submit" name="logout" value="logout"></form>');
        }
        else print('<h2>an error occured</h2>');
    }
}
// if user is not logged in and did not send the wrong password print the login form
elseif (!isset($_POST['name'])) {
    print(<<<frm
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>kickstart login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <form action="./" method="post">
        <input type="text" name="name" id="name" required placeholder="Username">
        <input type="password" name="pass" id="pass" required placeholder="Password">
        <input type="submit" value="Log in">
    </form>
</body>
</html>
frm
    );
}