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
//check if new user information is present
elseif (isset($_POST['name'])) {
    //check if username exists
    $qwr = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $qwr->bindParam(':username', $_POST['name']);
    $qwr->execute();
    $output = $qwr->fetch(PDO::FETCH_ASSOC);

    // if not in database put it in
    if (!isset($output['username'])){
        $password = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        $qwr = $pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $qwr->bindParam(':username', $_POST['name']);
        $qwr->bindParam(':password', $password);
        $qwr->execute();
        print('<html><head><link rel="stylesheet" href="../css/style.css"></head><body><a class="button" href="../login/">Login</a>');
    }
    //else print simple error
    else print("<h1>username exists, press your browsers back button</h1>");
}
//print registration form
else {
    print(<<<frm
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>kickstart register</title>
</head>
<body>
    <form name="register" action="./" method="post" onsubmit="return validatePass()">
        <input type="text" name="name" id="name" max="45" required placeholder="Username">
        <input type="password" name="pass" id="pass" max="45" required placeholder="Password">
        <input type="password" name="verpass" id="verpass" required placeholder="Verify Password">
        <input type="submit" value="register">
    </form>
    <script>
    function validatePass() {
        if (document.forms["register"]["verpass"].value != document.forms["register"]["pass"].value) {
            alert("Passwords are not the same");
            return false;
        }
    }
    </script>
</body>
</html>
frm
    );
}
?>
