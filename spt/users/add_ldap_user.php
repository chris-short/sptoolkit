<?php

/**
 * file:    add_ldap_user.php
 * version: 4.0
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
$ldap_username = $_POST['ldap_username'];
if ( ! empty ( $ldap_username ) ) {
    $_SESSION['temp_ldap_username'] = $ldap_username;
}
//set checkbox values to numbers
if ( isset ( $_REQUEST['ldap_admin'] ) ) {
    $ldap_admin = 1;
    $_SESSION['temp_ldap_admin'] = "CHECKED";
} else {
    $ldap_admin = 0;
}
if ( isset ( $_REQUEST['ldap_disabled'] ) ) {
    $ldap_disabled = 1;
    $_SESSION['temp_ldap_disabled'] = "CHECKED";
} else {
    $ldap_disabled = 0;
}
$ldap_server = $_POST['ldap_server'];
if ( ! empty ( $ldap_server ) ) {
    $_SESSION['temp_ldap_server'] = $ldap_server;
}
//validate that the newly entered username is a valid email address
if ( ! filter_var ( $ldap_username, FILTER_VALIDATE_EMAIL ) ) {
    $_SESSION['alert_message'] = "you must enter a valid email address";
    header ( 'location:./?add_ldap_user=true#tabs-2' );
    exit;
}
//validate that the username is not too long
if ( strlen ( $ldap_username ) > 255 ) {
    $_SESSION['alert_message'] = "the username is too long";
    header ( 'location:../?add_ldap_user=true#tabs-2' );
    exit;
}
//validate that the entered username is not already taken by a non-ldap user
$r = mysql_query ( 'SELECT username FROM users' ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['username'] == $ldap_username ) {
        $_SESSION['alert_message'] = "this email address is already taken";
        header ( 'location:./?add_ldap_user=true#tabs-2' );
        exit;
    }
}
//validate that the entered username is not already taken by another ldap user
$r = mysql_query ( 'SELECT username FROM users_ldap' ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['username'] == $ldap_username ) {
        $_SESSION['alert_message'] = "this email address is already taken";
        header ( 'location:./?add_ldap_user=true#tabs-2' );
        exit;
    }
}
//validate that the user exists
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
//lookup email address
$ldap_user_lookup = ldap_user_email_query($ldap_host,$ldap_port, $ldap_bindaccount, $ldap_password, $ldap_basedn, $ldap_ssl_enc, $ldap_ldaptype, $ldap_username);
if($ldap_user_lookup['count'] < 1){
    $_SESSION['alert_message'] = "this email address is not associated with a user";
    header('location:./?add_ldap_user=true#tabs-3');
    exit;
}
//find the user based on email address

//add user to database
mysql_query ( "INSERT INTO users_ldap(username, admin, disabled, ldap_host) VALUES ('$ldap_username','$ldap_admin','$ldap_disabled','$ldap_server')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

$_SESSION['alert_message'] = "LDAP user added successfully";
header ( 'location:./#tabs-2' );
exit;
?>