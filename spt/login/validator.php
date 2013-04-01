<?php

/**
 * file:    validator.php
 * version: 10.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Login management
 * copyright:   Copyright (C) 2011 The SPT Project. All rights reserved.
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

//this is the username/password validator
//upon successfull authentication, session variables are created that allow access
//start the session
session_start ();
//pull the unique salt value
include 'get_salt.php';
//get encrypt key
include '../spt_config/encrypt_config.php';
//get ldap functions
include '../includes/ldap.php';
//set an ip session variable with a salt to avoid session hijacking
$_SESSION['ip'] = md5 ( $_SESSION['salt'] . $_SERVER['REMOTE_ADDR'] . $_SESSION['salt'] );
//connect to the database
include '../spt_config/mysql_config.php';
//pull in username and password to temp variables
$temp_u = $_POST['u'];
$temp_p = $_POST['p'];
//validate the username/email address
if ( ! filter_var ( $temp_u, FILTER_VALIDATE_EMAIL ) ) {
    $_SESSION['alert_message'] = "invalid login attempt";
    header ( 'location:../' );
    exit;
}
//validate the username/email address length
if ( strlen ( $temp_u ) > 50 ) {
    $_SESSION['alert_message'] = "invalid login attempt";
    header ( 'location:../' );
    exit;
}
//validate the password length
if ( strlen ( $temp_p ) > 15 || strlen ( $temp_p ) < 8 ) {
    $_SESSION['alert_message'] = "invalid login attempt";
    header ( 'location:../' );
    exit;
}
//set variables to their final destination before the comparison occurs
$u = $temp_u;
$p = sha1 ( $_SESSION['salt'] . $temp_p . $_SESSION['salt'] );
//grab all usernames and passwords from the database
$r = mysql_query ( 'SELECT username, password, admin, disabled FROM users' ) or die ( '<div id="die_error">Error: Had trouble connection to database.</div>' );
//start a loop to compare the data pulled from the database to the data submitted by user for each user in the database
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    //the actual comparison
    if ( $ra['username'] == $u && $ra['password'] == $p && $ra['disabled'] != 1 ) {
        //create an authenticated session
        $_SESSION['authenticated'] = 1;
        //create a username session
        $_SESSION['username'] = $u;
        //check to see if they are an admin
        if ( $ra['admin'] == 1 ) {
            //create an admin session
            $_SESSION['admin'] = 1;
        }
        //send authenticated user to the dashboard
        header ( 'location:../dashboard/#phish_pie' );
        exit;
    }
}
//grab all usernames from the ldap table
$r = mysql_query ( 'SELECT * FROM users_ldap' ) ;
//start a loop to compare the data pulled from the database to the data submitted by user for each user in the database
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    //the actual comparison
    if ( $ra['username'] == $u && $ra['disabled'] != 1 ) { 
        //get ldap host id
        $ldap_server = $ra['ldap_host'];
        //get ldap server details
        $r1 = mysql_query("SELECT host, port, ssl_enc, ldaptype, bindaccount, aes_decrypt(password, '$spt_encrypt_key') as password, basedn FROM settings_ldap WHERE id = '$ldap_server'");
        while($ra1 = mysql_fetch_assoc($r1)){
            $ldap_host = $ra1['host'];
            $ldap_port = $ra1['port'];
            $ldap_ssl_enc = $ra1['ssl_enc'];
            $ldap_ldaptype = $ra1['ldaptype'];
            $ldap_bindaccount = $ra1['bindaccount'];
            $ldap_password = $ra1['password'];
            $ldap_basedn = $ra1['basedn'];
        }
        //attempt bind with provided username and password
        //get connected
        $ldap_conn = ldap_connection($ldap_host,$ldap_port, $ldap_ssl_enc);
        if(!$ldap_conn){
            $_SESSION['alert_message'] = "problems attempting authentication";
            header('location:../');
            exit;
        }
        //get username
        $ldap_user_lookup = ldap_user_email_query($ldap_host, $ldap_port, $ldap_bindaccount, $ldap_password, $ldap_basedn, $ldap_ssl_enc, $ldap_ldaptype, $temp_u);
        if($ldap_user_lookup){
            $ldap_dn = $ldap_user_lookup['0']['dn'];
        }
        //attempt bind with provided username and password
        $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_dn,$temp_p);
        if($ldap_bind){
            //create an authenticated session
            $_SESSION['authenticated'] = 1;
            //create a username session
            $_SESSION['username'] = $u;
            //check to see if they are an admin
            if ( $ra['admin'] == 1 ) {
                //create an admin session
                $_SESSION['admin'] = 1;
            }
            //send authenticated user to the dashboard
            header ( 'location:../dashboard/#phish_pie' );
            exit;
        }
    }
}
//grab all groups from the ldap group table
$r = mysql_query('SELECT * FROM users_ldap_groups');
//start a loop to get all users from each group
while ($ra = mysql_fetch_assoc($r)){
    $group = $ra['ldap_group'];
    $host = $ra['ldap_host'];
    if($ra['disabled'] != 1){
        //get ldap servers
        $r1 = mysql_query("SELECT host, port, ssl_enc, ldaptype, bindaccount, aes_decrypt(password, '$spt_encrypt_key') as password, basedn FROM settings_ldap WHERE id = '$host'");
        while($ra1 = mysql_fetch_assoc($r1)){
            $ldap_host = $ra1['host'];
            $ldap_port = $ra1['port'];
            $ldap_ssl_enc = $ra1['ssl_enc'];
            $ldap_ldaptype = $ra1['ldaptype'];
            $ldap_bindaccount = $ra1['bindaccount'];
            $ldap_password = $ra1['password'];
            $ldap_basedn = $ra1['basedn'];
        }
        //get group dn
        $ldap_group_dn = ldap_group_query($ldap_host,$ldap_port,$ldap_bindaccount,$ldap_password,$ldap_basedn,$ldap_ldaptype,$ldap_ssl_enc,$group);
        $ldap_group_dump = ldap_user_of_group($ldap_host,$ldap_port,$ldap_ssl_enc,$ldap_ldaptype,$ldap_bindaccount,$ldap_password,$ldap_basedn,$ldap_group_dn[0]['dn']);
        foreach ($ldap_group_dump as $username) {
            $ldap_user = $username['mail'][0];
            if ( strtolower($ldap_user) == strtolower($u) ) { 
                //attempt bind with provided username and password
                //get connected
                $ldap_conn = ldap_connection($ldap_host,$ldap_port, $ldap_ssl_enc);
                if(!$ldap_conn){
                    $_SESSION['alert_message'] = "problems attempting authentication";
                    header('location:../');
                    exit;
                }
                //get username
                $ldap_user_lookup = ldap_user_email_query($ldap_host, $ldap_port, $ldap_bindaccount, $ldap_password, $ldap_basedn, $ldap_ssl_enc, $ldap_ldaptype, $ldap_user);
                if($ldap_user_lookup){
                    $ldap_dn = $ldap_user_lookup['0']['dn'];
                }
                //attempt bind with provided username and password
                $ldap_bind = ldap_bind_connection($ldap_conn,$ldap_dn,$temp_p);
                if($ldap_bind){
                    //create an authenticated session
                    $_SESSION['authenticated'] = 1;
                    //create a username session
                    $_SESSION['username'] = $u;
                    //check to see if they are an admin
                    if ( $ra['admin'] == 1 ) {
                        //create an admin session
                        $_SESSION['admin'] = 1;
                    }
                    //send authenticated user to the dashboard
                    header ( 'location:../dashboard/#phish_pie' );
                    exit;
                }
            }
        }
    }
}

//if they make it this far with no match then send them back to the login page with an error
$_SESSION['alert_message'] = "invalid login attempt";
header ( 'location:../' );
exit;
?>
