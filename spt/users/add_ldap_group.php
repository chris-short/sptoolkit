<?php

/**
 * file:    add_ldap_group.php
 * version: 1.0
 * package: Simple Phishing Toolkit (spt)
 * component:	User management
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
//connect to database
include "../spt_config/mysql_config.php";
include "../spt_config/encrypt_config.php";
//get posted data
$ldap_group = $_POST['ldap_group'];
if ( ! empty ( $ldap_group ) ) {
    $_SESSION['temp_ldap_group_name'] = $ldap_group;
}
//set checkbox values to numbers
if ( isset ( $_REQUEST['ldap_admin'] ) ) {
    $ldap_admin = 1;
    $_SESSION['temp_ldap_group_admin'] = "CHECKED";
} else {
    $ldap_admin = 0;
}
if ( isset ( $_REQUEST['ldap_disabled'] ) ) {
    $ldap_disabled = 1;
    $_SESSION['temp_ldap_group_disabled'] = "CHECKED";
} else {
    $ldap_disabled = 0;
}
$ldap_server = $_POST['ldap_server'];
if ( ! empty ( $ldap_server ) ) {
    $_SESSION['temp_ldap_group_server_id'] = $ldap_server;
}
//validate that the entered group name is not already used
$r = mysql_query ( 'SELECT ldap_group FROM users_ldap_groups' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['ldap_group'] == $ldap_group ) {
        $_SESSION['alert_message'] = "this group is already taken";
        header ( 'location:./?add_ldap_group=true#tabs-3' );
        exit;
    }
}
//validate that the ldap server id is a number
if(!preg_match('/^[0-9]/', $ldap_server)){
    $_SESSION['alert_message'] = "server id must be an integer";
    header ('location:./?add_ldap_group=true#tabs-3');
    exit;
}
//validate that the group exists
//get ldap server details
$r = mysql_query("SELECT host, port, ssl_enc, ldaptype, bindaccount, aes_decrypt(password, '$spt_encrypt_key') as password, basedn FROM settings_ldap WHERE id = '$ldap_server'");
while($ra = mysql_fetch_assoc($r)){
    $ldap_host = $ra['host'];
    $ldap_port = $ra['port'];
    $ldap_ssl_enc = $ra['ssl_enc'];
    $ldap_ldaptype = $ra['ldaptype'];
    $ldap_bindaccount = $ra['bindaccount'];
    $ldap_password = $ra['password'];
    $ldap_basedn = $ra['basedn'];
}
//include ldap functions
include '../includes/ldap.php';
//lookup group
$ldap_group_lookup = ldap_group_query($ldap_host,$ldap_port, $ldap_bindaccount, $ldap_password, $ldap_basedn, $ldap_ldaptype, $ldap_ssl_enc, $ldap_group);
if($ldap_group_lookup['count'] < 1){
    $_SESSION['alert_message'] = "this group does not exist";
    header('location:./?add_ldap_group=true#tabs-3');
    exit;
}

//add user to database
mysql_query ( "INSERT INTO users_ldap_groups( ldap_group, admin, disabled, ldap_host) VALUES ('$ldap_group','$ldap_admin','$ldap_disabled','$ldap_server')" ) or die ( mysql_error() );

$_SESSION['alert_message'] = "LDAP group added successfully";
header ( 'location:./#tabs-3' );
exit;
?>