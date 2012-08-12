<?php

/**
 * file:    edit_user.php
 * version: 7.0
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

//connect to database
include "../spt_config/mysql_config.php";

//go ahead and get their original username
$original_username = $_SESSION['username'];

//get the new username
$new_username = $_POST['username'];

//see if the username has changed and if it has validate it
if ( $original_username != $new_username ) {
    //validate that the newly entered username is a valid email address
    if ( ! filter_var ( $new_username, FILTER_VALIDATE_EMAIL ) ) {
        $_SESSION['alert_message'] = "you must enter a valid email address as your username";
        header ( 'location:./#edit_user' );
        exit;
    }

    //validate that the username is not too long
    if ( strlen ( $new_username ) > 50 ) {
        $_SESSION['alert_message'] = "the username is too long";
        header ( 'location:./#edit_user' );
        exit;
    }

    //validate that the entered username is not already taken
    $r = mysql_query ( 'SELECT username FROM users' ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        if ( $ra['username'] == $new_username ) {
            $_SESSION['alert_message'] = "this email address is already taken";
            header ( 'location:./#edit_user' );
            exit;
        }
    }
}

//validate the first name
$new_fname = filter_var ( $_POST['fname'], FILTER_SANITIZE_STRING );

//make sure its under 50 characters
if ( strlen ( $new_fname ) > 50 ) {
    $_SESSION['alert_message'] = "your first name is too long, please shorten below 50 characters";
    header ( 'location:./#edit_user' );
    exit;
}

//make sure its over 1 character
if ( strlen ( $new_fname ) < 1 ) {
    $_SESSION['alert_message'] = "your first name must be at least 1 character long";
    header ( 'location:./#edit_user' );
    exit;
}

//validate the last name
$new_lname = filter_var ( $_POST['lname'], FILTER_SANITIZE_STRING );

//make sure its under 50 characters
if ( strlen ( $new_lname ) > 50 ) {
    $_SESSION['alert_message'] = "your last name is too long, please shorten below 50 characters";
    header ( 'location:./#edit_user' );
    exit;
}

//make sure its at least 1 character in length
if ( strlen ( $new_lname ) < 1 ) {
    $_SESSION['alert_message'] = "your last name must be at least 1 character long";
    header ( 'location:./#edit_user' );
    exit;
}

//validate the password if it is set
if ( isset ( $_POST['password'] ) ) {
    //pull in password to temp variable
    $temp_p = $_POST['password'];

    //validate that the password is an acceptable length
    if ( strlen ( $temp_p ) > 15 || strlen ( $temp_p ) < 8 ) {
        $_SESSION['alert_message'] = "you must enter a valid password length (8-15 characters)";
        header ( 'location:./#edit_user' );
        exit;
    }

    //pass temp password to new variable that has been salted and hashed
    $p = sha1 ( $_SESSION['salt'] . $temp_p . $_SESSION['salt'] );
}

//validate the passwords match
if ( isset ( $_POST['password'] ) && isset ( $_POST['password_check'] ) ) {
    if ( $_POST['password'] != $_POST['password_check'] ) {
        $_SESSION['alert_message'] = "your passwords do not match...please try again";
        header ( 'location:./#edit_user' );
        exit;
    }
}
//all entered variables should have been validated by now
//input variables to database except username and password
mysql_query ( "UPDATE users SET fname = '$new_fname' , lname = '$new_lname' WHERE username = '$original_username'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//if password was entered update it
if ( isset ( $p ) ) {
    mysql_query ( "UPDATE users SET password = '$p' WHERE username = '$original_username'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
}

//if username changed, change it and update the username session variable for the remainder of the user's session
if ( $original_username != $new_username ) {
    mysql_query ( "UPDATE users SET username = '$new_username' WHERE username = '$original_username'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    $_SESSION['username'] = $new_username;
}

//send the user back to the users page once they've edited the user successfully
$_SESSION['alert_message'] = "you have successfully edited the user";
header ( 'location:./#alert' );
exit;
?>