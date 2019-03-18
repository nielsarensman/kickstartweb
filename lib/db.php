<?php
//make all properties if they don't exist exept database
if (!isset($host)){
    $host = 'localhost';
}
if (!isset($user)){
    $user = 'root';
}
if (!isset($pass)){
    $pass = 'password';
}
if (!isset($charset)){
    $charset = 'utf8mb4';
}

if (!isset($options)){
    $options = [

    ];
}
//try the connection and trow exeption on failure
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}