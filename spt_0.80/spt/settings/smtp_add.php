<?php

/**
 * file:    smtp_add.php
 * version: 1.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Settings
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license: GNU/GPL, see license.htm.
 * 
 * This file is part of the Simple Phishing Toolkit (spt).
 * 
 * spt is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, under version 3 of the License.
 *
 * spt is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with spt.  If not, see <http://www.gnu.org/licenses/>.
 * */
// verify session is authenticated and not hijacked
$includeContent = "../includes/is_authenticated.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_authenticated.php' );
}

// verify user is an admin
$includeContent = "../includes/is_admin.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_admin.php' );
}

//check to see if something was posted
if($_POST){
    //validate and get host
    if(isset($_POST['host']) && preg_match( '/^[a-zA-Z0-9\-\_\.]/' , $_POST['host']) ){
        $host = $_POST['host'];
    }
    else{
        $_SESSION['alert_message'] = 'host was either empty or not a valid hostname';
        header ( 'location:./#alert' );
        exit;
    }
    //validate and get port
    if(isset($_POST['port']) && preg_match('/^[0-9]/', $_POST['port']) && $_POST['port'] > 0 && $_POST['port'] < 65536 ){
        $port = $_POST['port'];
    }
    else{
        $_SESSION['alert_message'] = 'port is required and must be between 1 and 65535';
        header ( 'location:./#alert' );
        exit;
    }
    //get ssl status
    if(isset($_POST['ssl'])){
        $ssl = 1;
    }else{
        $ssl = 0;
    }
    //get username if provided
    if(isset($_POST['username'])){
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    }else{
        $username = "";
    }
    //get password if provided
    if(isset($_POST['password'])){
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    }else{
        $password = "";
    }
    //get default status
    if(isset($_POST['default'])){
        $default = "default";
    }else{
        $default = "";
    }
    //take away default value from any existing smtp servers that are set to default
    if($default == 'default'){
        include '../spt_config/mysql_config.php';
        $r = mysql_query("SELECT value FROM settings WHERE setting='smtp'");
        while($ra=mysql_fetch_assoc($r)){
            $old_smtp_setting = $ra['value'];
            $smtp_setting = explode("|",$ra['value']);
            if($smtp_setting[5] == "default"){
                //reconstruct smtp setting
                $new_smtp_setting = $smtp_setting[0]."|".$smtp_setting[1]."|".$smtp_setting[2]."|".$smtp_setting[3]."|".$smtp_setting[4]."|"; 
                //update smtp setting without default set
                mysql_query("UPDATE settings SET value='$new_smtp_setting' WHERE setting = 'smtp' AND value='$old_smtp_setting'");
            }
        }
    }
    //formulate smtp server entry
    $value = $host."|".$port."|".$ssl."|".$username."|".$password."|".$default;
    //add smtp server details to database
    mysql_query("INSERT INTO settings VALUES('smtp','$value')");
}

header('location:.');
exit;

?>
