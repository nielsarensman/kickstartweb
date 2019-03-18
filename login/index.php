<?php
session_start();
//database connection
$db = 'kickstart';
include_once('../lib/db.php');

//check if user is logged in
if (isset($_SESSION['login'])) {
    //bring the user back to the main page
    header("Location: " . "../");
}
//check if user information is present
elseif (isset($_POST['name'])) {
    //get first hit on user info
    $qwr = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $qwr->bindParam(':username', $_POST['name']);
    $qwr->execute();
    $output = $qwr->fetch(PDO::FETCH_ASSOC);

    //check if password is right
    if (password_verify($_POST['pass'], $output['password'])){
        //set login id and bring user to main page
        $_SESSION['login'] = $output['Id'];
        header("Location: " . "../");

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
//print login form
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
?>

