
<?php

/**
 * file:    ldap_add.php
 * version: 11.0
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
        $_SESSION['temp_host'] = $_POST['host'];
    }
    if(isset($_POST['port'])){
        $_SESSION['temp_port'] = $_POST['port'];
    }
    if(isset($_POST['ssl'])){
        $_SESSION['temp_ssl'] = $_POST['ssl'];
    }
    if(isset($_POST['ldaptype_radio'])){
        $_SESSION['temp_ldaptype'] = $_POST['ldaptype_radio'];
    }
    if(isset($_POST['bindaccount'])){
        $_SESSION['temp_bindaccount'] = $_POST['bindaccount'];
    }
    if(isset($_POST['password'])){
        $_SESSION['temp_password'] = $_POST['password'];
    }
    if(isset($_POST['basedn'])){
        $_SESSION['basedn'] = $_POST['basedn'];
    }    
    //validate and get host
    if(isset($_POST['host']) && preg_match( '/^[a-zA-Z0-9\-\_\.]/' , $_POST['host']) ){
        $host = $_POST['host'];
    }
    else{
        $_SESSION['alert_message'] = 'host was either empty or not a valid hostname';
        header ( 'location:.?add_ldap_server=true#tabs-3' );
        exit;
    }
    //validate and get port
    if(isset($_POST['port']) && preg_match('/^[0-9]/', $_POST['port']) && $_POST['port'] > 0 && $_POST['port'] < 65536 ){
        $port = $_POST['port'];
    }
    else{
        $_SESSION['alert_message'] = 'port is required and must be between 1 and 65535';
        header ( 'location:.?add_ldap_server=true#tabs-3' );
        exit;
    }
    //get ssl status
    if(isset($_POST['ssl'])){
        $ssl = '1';
    }else{
        $ssl = '0';
    }
    //get ldaptype and ensure its valid
    if(isset($_POST['ldaptype_radio'])){
        $ldaptype = $_POST['ldaptype_radio'];
        if($ldaptype != "Active Directory" && $ldaptype != "Unix/Linux"){
            $_SESSION['alert_message'] = "please select a valid LDAP type";
            header('location:.#tabs-3');
            exit;
        }
    }
    //get bindaccount if provided
    if(isset($_POST['bindaccount'])){
        $bindaccount = filter_var($_POST['bindaccount'], FILTER_SANITIZE_STRING);
    }else{
        $bindaccount = "";
    }
    //get password if provided
    if(isset($_POST['password'])){
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
    }else{
        $password = "";
    }
    //get base DN
    if(isset($_POST['basedn'])){
        $basedn = filter_var($_POST['basedn'], FILTER_SANITIZE_STRING);
    }else{
        $basedn = "";
    }
    //add ldap server details to database
    include "../spt_config/mysql_config.php";
    include "../spt_config/encrypt_config.php";
    mysql_query("INSERT INTO settings_ldap (host, port, ssl_enc, ldaptype, bindaccount, password, basedn) VALUES ('$host', '$port', '$ssl', '$ldaptype', '$bindaccount', aes_encrypt('$password','$spt_encrypt_key'), '$basedn')");
    //unset temp variables
    if(isset($_SESSION['temp_host'])){
        unset($_SESSION['temp_host']);
    }
    if(isset($_SESSION['temp_port'])){
        unset($_SESSION['temp_port']);
    }
    if(isset($_SESSION['temp_ssl'])){
        unset($_SESSION['temp_ssl']);
    }
    if(isset($_SESSION['temp_ldaptype'])){
        unset($_SESSION['temp_ldaptype']);
    }
    if(isset($_SESSION['temp_bindaccount'])){
        unset($_SESSION['temp_bindaccount']);
    }
    if(isset($_SESSION['temp_password'])){
        unset($_SESSION['temp_password']);
    }
    if(isset($_SESSION['temp_basedn'])){
        unset($_SESSION['temp_basedn']);
    }
}

header('location:.#tabs-3');
exit;

?>
