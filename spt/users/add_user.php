<?php

/**
 * file:    add_user.php
 * version: 9.0
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

//get posted data
$new_username = $_POST['username'];
if ( ! empty ( $new_username ) ) {
    $_SESSION['temp_new_username'] = $new_username;
}
$new_fname = filter_var ( $_POST['fname'], FILTER_SANITIZE_STRING );
if ( ! empty ( $new_fname ) ) {
    $_SESSION['temp_new_fname'] = $new_fname;
}
$new_lname = filter_var ( $_POST['lname'], FILTER_SANITIZE_STRING );
if ( ! empty ( $new_lname ) ) {
    $_SESSION['temp_new_lname'] = $new_lname;
}

//set checkbox values to numbers
if ( isset ( $_REQUEST['a'] ) ) {
    $a = 1;
    $_SESSION['temp_a'] = "CHECKED";
} else {
    $a = 0;
}
if ( isset ( $_REQUEST['disabled'] ) ) {
    $disabled = 1;
    $_SESSION['temp_disabled'] = "CHECKED";
} else {
    $disabled = 0;
}


//validate that the newly entered username is a valid email address
if ( ! filter_var ( $new_username, FILTER_VALIDATE_EMAIL ) ) {
    $_SESSION['alert_message'] = "you must enter a valid email address";
    header ( 'location:./#add_user' );
    exit;
}

//validate that the username is not too long
if ( strlen ( $new_username ) > 50 ) {
    $_SESSION['alert_message'] = "the username is too long";
    header ( 'location:../#add_user' );
    exit;
}

//validate that the entered username is not already taken
$r = mysql_query ( 'SELECT username FROM users' ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    if ( $ra['username'] == $new_username ) {
        $_SESSION['alert_message'] = "this email address is already taken";
        header ( 'location:./#add_user' );
        exit;
    }
}

//make sure its under 50 characters
if ( strlen ( $new_fname ) > 50 ) {
    $_SESSION['alert_message'] = "your first name is too long, please shorten below 50 characters";
    header ( 'location:./#add_user' );
    exit;
}

//make sure its over 1 character
if ( strlen ( $new_fname ) < 1 ) {
    $_SESSION['alert_message'] = "your first name must be at least 1 character long";
    header ( 'location:./#add_user' );
    exit;
}

//make sure its under 50 characters
if ( strlen ( $new_lname ) > 50 ) {
    $_SESSION['alert_message'] = "your last name is too long, please shorten below 50 characters";
    header ( 'location:./#add_user' );
    exit;
}

//make sure its at least 1 character in length
if ( strlen ( $new_lname ) < 1 ) {
    $_SESSION['alert_message'] = "your last name must be at least 1 character long";
    header ( 'location:./#add_user' );
    exit;
}

//validate the password if it is set
if ( isset ( $_POST['password'] ) ) {
    //pull in password to temp variable
    $temp_p = $_POST['password'];

    //validate that the password is an acceptable length
    if ( strlen ( $temp_p ) > 15 || strlen ( $temp_p ) < 8 ) {
        $_SESSION['alert_message'] = "you must enter a valid password length";
        header ( 'location:./#add_user' );
        exit;
    }

    //pass temp password to new variable that has been salted and hashed
    $p = sha1 ( $_SESSION['salt'] . $temp_p . $_SESSION['salt'] );
} else {
    $_SESSION['alert_message'] = "you must enter a password";
    header ( 'location"./#add_user' );
    exit;
}

//validate that the entered passwords match
if ( isset ( $_POST['password'] ) && isset ( $_POST['password_check'] ) ) {
    if ( $_POST['password'] != $_POST['password_check'] ) {
        $_SESSION['alert_message'] = "your password values must match";
        header ( 'location:./#add_user' );
        exit;
    }
} else {
    $_SESSION['alert_message'] = "you must enter something in both password fields";
    header ( 'location"./#add_user' );
    exit;
}

mysql_query ( "INSERT INTO users(fname, lname, username, password, admin, disabled) VALUES ('$new_fname','$new_lname','$new_username','$p','$a','$disabled')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

$_SESSION['alert_message'] = "user added successfully";
header ( 'location:./#alert' );
exit;
?>