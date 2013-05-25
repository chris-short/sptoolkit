<?php

/**
 * file:    smtp_add.php
 * version: 10.0
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
    //get values
    if(isset($_POST['host'])){
        $_SESSION['temp_smtp_host'] = $_POST['host'];
    }
    if(isset($_POST['port'])){
        $_SESSION['temp_smtp_port'] = $_POST['port'];
    }
    if(isset($_POST['ssl'])){
        $_SESSION['temp_smtp_ssl'] = $_POST['ssl'];
    }
    if(isset($_POST['username'])){
        $_SESSION['temp_smtp_username'] = $_POST['username'];
    }
    if(isset($_POST['password'])){
        $_SESSION['temp_smtp_password'] = $_POST['password'];
    }
    if(isset($_POST['default'])){
        $_SESSION['temp_smtp_default'] = $_POST['default'];
    }
    //validate and get host
    if(isset($_POST['host']) && preg_match( '/^[a-zA-Z0-9\-\_\.]/' , $_POST['host']) ){
        $host = $_POST['host'];
    }
    else{
        $_SESSION['alert_message'] = 'host was either empty or not a valid hostname';
        header ( 'location:./?add_smtp_server=true#tabs-2' );
        exit;
    }
    //validate and get port
    if(isset($_POST['port']) && preg_match('/^[0-9]/', $_POST['port']) && $_POST['port'] > 0 && $_POST['port'] < 65536 ){
        $port = $_POST['port'];
    }
    else{
        $_SESSION['alert_message'] = 'port is required and must be between 1 and 65535';
        header ( 'location:./?add_smtp_server=true#tabs-2' );
        exit;
    }
    //get ssl status
    if(isset($_POST['ssl'])){
        $ssl = "1";
    }else{
        $ssl = "0";
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
        $default = '1';
    }else{
        $default = '    0';
    }
    //connect to database
    include '../spt_config/mysql_config.php';
    include '../spt_config/encrypt_config.php';
    //take away default value from any existing smtp servers that are set to default
    if($default == "1"){
        mysql_query("UPDATE settings_smtp SET sys_default = '0' WHERE sys_default='1'");
    }
    //add smtp server details to database
    mysql_query("INSERT INTO settings_smtp(host,port,ssl_enc,username,password,sys_default) VALUES('$host','$port', '$ssl', '$username', aes_encrypt('$password', '$spt_encrypt_key'), '$default')");
    if(mysql_error()){
        $_SESSION['alert_message'] = "there was a problem adding the smtp server";
        header('location:.#tabs-2');
        exit;
    }
    //unset temp variables
    if(isset($_SESSION['temp_smtp_host'])){
        unset($_SESSION['temp_smtp_host']);
    }
    if(isset($_SESSION['temp_smtp_port'])){
        unset($_SESSION['temp_smtp_port']);
    }
    if(isset($_SESSION['temp_smtp_ssl'])){
        unset($_SESSION['temp_smtp_ssl']);
    }
    if(isset($_SESSION['temp_smtp_username'])){
        unset($_SESSION['temp_smtp_username']);
    }
    if(isset($_SESSION['temp_smtp_password'])){
        unset($_SESSION['temp_smtp_password']);
    }
    if(isset($_SESSION['temp_smtp_default'])){
        unset($_SESSION['temp_smtp_default']);
    }

}
$_SESSION['alert_message'] = "smtp server added successfully";
header('location:.#tabs-2');
exit;

?>
