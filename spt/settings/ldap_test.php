<?php

/**
 * file:    ldap_test.php
 * version: 13.0
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
    if(isset($_POST['host']) && preg_match('/[0-9]/',$_POST['host']) ){
        $host = $_POST['host'];
    }
    else{
        $_SESSION['alert_message'] = 'host was either empty or not a valid hostname';
        header ( 'location:./#tabs-3' );
        exit;
    }
    //validate that the host is legit
    include '../spt_config/mysql_config.php';
    include '../spt_config/encrypt_config.php';
    $r = mysql_query("SELECT id, host, port, ssl_enc, ldaptype, bindaccount, aes_decrypt(password, '$spt_encrypt_key') as password, basedn FROM settings_ldap WHERE id = $host");
    if(mysql_num_rows($r) < 1){
        $_SESSION['alert_message'] = "please select an existing ldap server";
        header('location:./#tabs-3');
        exit;        
    }
    //check to see if valid type is posted
    if(isset($_POST['type']) && ($_POST['type'] == "auth" OR $_POST['type'] == "bind")){
    }else{
        $_SESSION['alert_message'] = "please attempt a valid LDAP test";
        header('location:./?test_ldap_server='.$host."#tabs-3");
        exit;
    }
    //get ldap functions
    include '../includes/ldap.php';
    //bind test
    if(isset($_POST['type']) && $_POST['type'] == "bind"){
        //get ldap server details
        while ($ra = mysql_fetch_array($r)){
            $current_ldap_server_host = $ra[1];
            $current_ldap_server_port = $ra[2];
            $current_ldap_server_ssl = $ra[3];
            $current_ldap_server_bindaccount = $ra[5];
            $current_ldap_server_password = $ra[6];
            $current_ldap_server_basedn = $ra[7];
        }
        //get connected
        $ldap_conn = ldap_connection($current_ldap_server_host,$current_ldap_server_port, $current_ldap_server_ssl);
        if(!$ldap_conn){
            $_SESSION['alert_message'] = "could not connect to server";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }
        //call bind function
        $ldap_bind = ldap_bind_connection($ldap_conn,$current_ldap_server_bindaccount,$current_ldap_server_password);
        if($ldap_bind){
            $_SESSION['alert_message'] = "bind successful :)";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }else{
            $_SESSION['alert_message'] = "bind unsuccessful :(";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }
        unset($ldap_bind);
    }
    if(isset($_POST['type']) && $_POST['type'] == "auth"){
        //get ldap server details
        while ($ra = mysql_fetch_array($r)){
            $current_ldap_server_host = $ra[1];
            $current_ldap_server_port = $ra[2];
            $current_ldap_server_ssl = $ra[3];
            $current_ldap_server_ldaptype = $ra[4];
            $current_ldap_server_bindaccount = $ra[5];
            $current_ldap_server_password = $ra[6];
            $current_ldap_server_basedn = $ra[7];
        }
        //get connected
        $ldap_conn = ldap_connection($current_ldap_server_host,$current_ldap_server_port, $current_ldap_server_ssl);
        if(!$ldap_conn){
            $_SESSION['alert_message'] = "could not connect to server";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }
        //get username and password from submission
        $username = $_POST['username'];
        $password = $_POST['password'];
        //get username
        $ldap_user_lookup = ldap_user_email_query($current_ldap_server_host, $current_ldap_server_port, $current_ldap_server_bindaccount, $current_ldap_server_password, $current_ldap_server_basedn, $current_ldap_server_ssl, $current_ldap_server_ldaptype, $username);
        if($ldap_user_lookup){
            $ldap_test_user_dn = $ldap_user_lookup['0']['dn'];
        }
        unset($ldap_bind);
        //attempt bind with provided username and password
        $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_test_user_dn,$password);
        if($ldap_bind){
            $_SESSION['alert_message'] = "authentication successful :)";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }else{
            $_SESSION['alert_message'] = "authentication unsuccessful :(";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }
    }
}
$_SESSION['alert_message'] = "gotta send me something";
header('location:./#tabs-3');
exit;

?>
