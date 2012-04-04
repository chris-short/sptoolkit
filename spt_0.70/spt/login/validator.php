<?php

/**
 * file:    validator.php
 * version: 6.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Login management
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

//this is the username/password validator
//upon successfull authentication, session variables are created that allow access
//start the session
session_start ();

//pull the unique salt value
include 'get_salt.php';

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
    header ( 'location:../#alert' );
    exit;
}

//validate the username/email address length
if ( strlen ( $temp_u ) > 50 ) {
    $_SESSION['alert_message'] = "invalid login attempt";
    header ( 'location:../#alert' );
    exit;
}

//validate the password length
if ( strlen ( $temp_p ) > 15 || strlen ( $temp_p ) < 8 ) {
    $_SESSION['alert_message'] = "invalid login attempt";
    header ( 'location:../#alert' );
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

//if they make it this far with no match then send them back to the login page with an error
$_SESSION['alert_message'] = "invalid login attempt";
header ( 'location:../#alert' );
exit;
?>
