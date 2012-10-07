<?php

/**
 * file:    ldap_test.php
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
        header ( 'location:./#tabs-3' );
        exit;
    }
    //validate that the host is legit
    include '../spt_config/mysql_config.php';
    $r = mysql_query("SELECT value FROM settings WHERE setting = 'ldap'");
    //set match value
    $match = 0;
    while($ra = mysql_fetch_assoc($r)){
        $current_ldap_server = explode("|", $ra['value']);
        if($host == $current_ldap_server[0]){
            $match = 1;
        }
    }
    if($match != 1){
        $_SESSION['alert_message'] = "please select an existing ldap server";
        header('location:./#tabs-3');
        exit;
    }
    //check to see if valid type is posted
    if(isset($_POST['type']) && ($_POST['type'] == "connectivity" OR $_POST['type'] == "bind")){
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
        $r = mysql_query("SELECT value FROM settings WHERE setting = 'ldap'");
        while ($ra = mysql_fetch_assoc($r)){
            $current_ldap_server = explode("|", $ra['value']);
            if($current_ldap_server[0] == $host){
                $current_ldap_server_port = $current_ldap_server[1];
                $current_ldap_server_ssl = $current_ldap_server[2];
                $current_ldap_server_username = $current_ldap_server[3];
                $current_ldap_server_password = $current_ldap_server[4];
                $current_ldap_server_basedn = $current_ldap_server[5];
            }
        }
        //get connected
        $ldap_conn = ldap_connection($ldap_server,$ldap_port);
        if(!$ldap_conn){
            $_SESSION['alert_message'] = "could not connect to server";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }
        //call bind function
        $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_user,$ldap_pass);
        if($ldap_bind){
            $_SESSION['alert_message'] = "bind successful :)";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }else{
            $_SESSION['alert_message'] = "bind unsuccessful :(";
            header('location:./?test_ldap_server='.$host.'#tabs-3');
            exit;
        }
    }
}

$_SESSION['alert_message'] = "gotta send me something";
header('location:./#tabs-3');
exit;

?>
