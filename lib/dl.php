<?php
//check if post is set, if not print error to stdout
if (isset($_SESSION)){
    $str = $_SESSION['text'];
    
    //set headers so a download is enforced
    header('Content-Disposition: attachment; filename="ks.cfg"');
    header('Content-Type: text/plain');
    header('Content-Length: ' . strlen($str));
    header('Connection: close');
    
    //echo the contents to the file
    echo $str;
}
else print("<h1>Something went wrong, press your browsers back-button</h1>");