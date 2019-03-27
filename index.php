<?php
session_start();
//database connection
$db = 'kickstart';
include_once('./lib/db.php');
//if person clicked logout destroy session and refresh the page so the session is gone
if(isset($_POST['logout'])) {
    session_destroy();
    print('<html><head><meta http-equiv="refresh" content="0">');
};

//fetch all continents
$qwr = $pdo->prepare('SELECT * FROM tz_prefix');
$qwr->execute([]);
$continents = $qwr->fetchAll(PDO::FETCH_ASSOC);

//fetch all keyboard layouts
$qwr = $pdo->prepare('SELECT * FROM keyboard');
$qwr->execute([]);
$keyboard = $qwr->fetchAll(PDO::FETCH_ASSOC);

//fetch all languages
$qwr = $pdo->prepare('SELECT * FROM lang');
$qwr->execute([]);
$lang = $qwr->fetchAll(PDO::FETCH_ASSOC);

//fetch all locales
$qwr = $pdo->prepare('SELECT * FROM locale');
$qwr->execute([]);
$locale = $qwr->fetchAll(PDO::FETCH_ASSOC);

//build the form
print('
<html>
<head>
<title>Kickstart web generator</title>
<link rel="stylesheet" href="./css/style.css">
</head>
<body>
<fieldset>
    <legend>Kickstart</legend>
    <form action="./dbOrFile/" method="post" name="kickstart">
        <div class="forminputcontainer">
            <label for="username">Username</label>
            <input type="text" name="username" id="username">
        </div>
        <div class="forminputcontainer">
            <label for="fullusername">Full username</label>
            <input type="text" name="fullusername" id="fullusername">
        </div>
        <div class="forminputcontainer">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" autocomplete="off">
        </div>
        <div class="forminputcontainer">
            <label for="rootpassword">Root password</label>
            <input type="password" name="rootpassword" id="rootpassword" autocomplete="off">
        </div>
        <div class="forminputcontainer">
            <label for="hostname">Hostname</label>
            <input type="text" name="hostname" id="hostname">
        </div>
        <div class="forminputcontainer">
            <label for="keyboard">Keyboard-layout code: </label>
            <input list="keyboardlayouts" name="keyboard" id="keyboard" autocomplete="off">
            <datalist id="keyboardlayouts">');
                foreach ($keyboard as $value) {
                    print('<option value="'.$value['Layout'].'">');
                }
            print('
            </datalist>
        </div>
        <div class="forminputcontainer">
            <label for="lang">Language code: </label>
            <input list="lang" name="lang" id="language" autocomplete="off">
            <datalist id="lang">');
                foreach ($lang as $value) {
                    print('<option value="'.$value['Language'].'">');
                }
            print('
            </datalist>
        </div>
        <div class="forminputcontainer">
            <label for="locale">Locale code: </label>
            <input list="locale" name="locale" id="localeinput" autocomplete="off">
            <datalist id="locale">');
                foreach ($locale as $value) {
                    print('<option value="'.$value['Locale'].'">');
                }
            print('
            </datalist>
        </div>
        <div class="forminputcontainer">
            <label for="Continent">Timezone: </label>
            <select name="Continent" id="Continent" onchange="showTimezone()">
                <option value="0"></option>');
                foreach ($continents as $value) {
                    print('<option value="'.$value['Id'].'" id="Continent'.$value['Id'].'">'.$value['Continent'].'</option>');
                }
                print('
            </select>
            <input list="City" name="Tag" id="Tag" autocomplete="off">
            <datalist id="City" disabled>
            </datalist>
        </div>
        <div class="forminputcontainer">
            <label for="afterinstall">After installation: </label>
            <select name="afterinstall" id="afterinstall" autocomplete="off">
                <option value="0"></option>
                <option value="1" id="afterInstall1">restart</option>
                <option value="2" id="afterInstall2">poweroff</option>
                <option value="3" id="afterInstall3">restart and eject disk</option>
                <option value="4" id="afterInstall4">poweroff and eject disk</option>
            </select>
        </div>
        <div class="forminputcontainer">
            <label for="packages">Packages (separate each package with a new line): </label><br>
            <textarea name="packages" id="packages" rows="5" cols="50"></textarea>
        </div>
        <input type="submit">
    </form>
</fieldset>
');
if (isset($_SESSION['login']))print('<form action="./" method="post"><input type="submit" name="logout" value="logout"></form>');
else print('<a href="./login/" class="button">login</a> <a href="./register/" class="button">register</a>');
print(' <a href="./help/" class="button">help</a>');

//fetch all timezone Cities
$qwr = $pdo->prepare('SELECT * FROM tz_append ORDER BY TzPreId');
$qwr->execute([]);
$timezones = $qwr->fetchAll(PDO::FETCH_ASSOC);


//print javascript function that places the right timezonecities with the continent
print("
<script>
var timezoneArray = [];"
    );
foreach ($continents as $continent) {
    print('timezoneArray['.$continent['Id'].'] = \'');
    foreach ($timezones as $value) {
        if($value['TzPreId'] == $continent['Id']) print('<option value="'.$value['Tag'].'">'.$value['Tag'].'</option>');
    }
    print("';");
}

print(<<<JavaScript
document.getElementById("City").innerHTML = timezoneArray[document.getElementById("Continent").value];
document.getElementById("City").disabled = false;
function showTimezone(){
    document.getElementById("City").innerHTML = timezoneArray[document.getElementById("Continent").value];
    document.getElementById("City").disabled = false;
}
</script>
JavaScript
);

//check if someone is logged in
if (isset($_SESSION['login'])){
    //fetch first config that matches id
    $qwr = $pdo->prepare('SELECT * FROM config WHERE user = :id');
    $qwr->bindParam(':id', $_SESSION['login']);
    $qwr->execute();
    $preconfig = $qwr->fetch(PDO::FETCH_ASSOC);

    //fetch the nullable values
    if (isset($preconfig['keyboard'])) {
        $qwr = $pdo->prepare('SELECT * FROM keyboard WHERE id = :id');
        $qwr->bindParam(':id', $preconfig['keyboard']);
        $qwr->execute();
        $preconfig['keyboard'] = $qwr->fetch(PDO::FETCH_ASSOC)['Layout'];
    }
    if (isset($preconfig['lang'])) {
        $qwr = $pdo->prepare('SELECT * FROM lang WHERE id = :id');
        $qwr->bindParam(':id', $preconfig['lang']);
        $qwr->execute();
        $preconfig['lang'] = $qwr->fetch(PDO::FETCH_ASSOC)['Language'];
    }
    if (isset($preconfig['locale'])) {
        $qwr = $pdo->prepare('SELECT * FROM locale WHERE id = :id');
        $qwr->bindParam(':id', $preconfig['locale']);
        $qwr->execute();
        $preconfig['locale'] = $qwr->fetch(PDO::FETCH_ASSOC)['Locale'];
    }
    if (isset($preconfig['tz_append'])) {
        $qwr = $pdo->prepare('SELECT * FROM tz_append WHERE id = :id');
        $qwr->bindParam(':id', $preconfig['tz_append']);
        $qwr->execute();
        $tz = $qwr->fetch(PDO::FETCH_ASSOC);
        $preconfig['Tag'] = $tz['Tag'];
    }
    
    //print the prefill script
    print('<script>');
    if (isset($preconfig['keyboard'])) print('document.getElementById("keyboard").value = "'.$preconfig['keyboard'].'";');
    if (isset($preconfig['lang'])) print('document.getElementById("language").value = "'.$preconfig['lang'].'";');
    if (isset($preconfig['locale'])) print('document.getElementById("localeinput").value = "'.$preconfig['locale'].'";');
    if (isset($tz['TzPreId'])) print('document.getElementById("Continent'.$tz['TzPreId'].'").selected = "true";');
    if (isset($preconfig['afterinstall'])) print('document.getElementById("afterInstall'.$preconfig['afterinstall'].'").selected = "true";');
    if (isset($preconfig['Tag'])) print('document.getElementById("Tag").value = "'.$preconfig['Tag'].'";');
    if (isset($preconfig['username'])) print('document.getElementById("username").value = "'.$preconfig['username'].'";');
    if (isset($preconfig['fullusername'])) print('document.getElementById("fullusername").value = "'.$preconfig['fullusername'].'";');
    if (isset($preconfig['hostname'])) print('document.getElementById("hostname").value = "'.$preconfig['hostname'].'";');
    print('</script>');
}