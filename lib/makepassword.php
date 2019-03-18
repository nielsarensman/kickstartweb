<?php
function makePassword($plainpassword)
{
    //prepare the salt
    $salt="";
    $saltAllowedChars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
    //construct the 8 character salt
    for($i=0; $i<8; $i++){
        $salt.=$saltAllowedChars[mt_rand(0,strlen($saltAllowedChars)-1)];
    }
    
    //return the password
    return crypt($plainpassword, '$6$'.$salt.'$');
}