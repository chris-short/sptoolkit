<?php

/**
 * file:    ldap_edit.php
 * version: 3.0
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
    //get previous hostname
    if(isset($_POST['current_host']) && is_int($_POST['current_host'])){
        $current_host = $_POST['current_host'];
    }
    //validate and get host
    if(isset($_POST['host']) && preg_match( '/^[a-zA-Z0-9\-\_\.]/' , $_POST['host']) ){
        $host = $_POST['host'];
    }
    else{
        $_SESSION['alert_message'] = 'host was either empty or not a valid hostname';
        header ( 'location:./?edit_ldap_server='.$current_host.'#tabs-3' );
        exit;
    }
    //validate and get port
    if(isset($_POST['port']) && preg_match('/^[0-9]/', $_POST['port']) && $_POST['port'] > 0 && $_POST['port'] < 65536 ){
        $port = $_POST['port'];
    }
    else{
        $_SESSION['alert_message'] = 'port is required and must be between 1 and 65535';
        header ( 'location:./?edit_ldap_server='.$current_host.'#tabs-3' );
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
    if(isset($_POST['password']) && strlen($_POST['password']) > 0){
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    }
    //get basedn
    if(isset($_POST['basedn'])){
        $basedn = $_POST['basedn'];
    }
    //connect to database
    include '../spt_config/mysql_config.php';
    //delete existing entry
    mysql_query("DELETE FROM settings_ldap WHERE id = '$current_host'");
    //add ldap server details to database
    mysql_query("INSERT INTO settings_ldap(host, port, ssl, username, password, basedn) VALUES('$host','$port', '$ssl', '$username', '$password', '$basedn')");
}
$_SESSION['alert_message'] = "ldap server updated";
header('location:.#tabs-3');
exit;

?>
