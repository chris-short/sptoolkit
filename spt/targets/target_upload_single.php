<?php

/**
 * file:    target_upload_single.php
 * version: 20.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Target management
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

//recieve posted elements into session variables
if ( ! empty ( $_POST['fname'] ) ) {
    $_SESSION['temp_fname'] = $_POST['fname'];
}
if ( ! empty ( $_POST['lname'] ) ) {
    $_SESSION['temp_lname'] = $_POST['lname'];
}
if ( ! empty ( $_POST['email'] ) ) {
    $_SESSION['temp_email'] = $_POST['email'];
}
if ( ! empty ( $_POST['group_name'] ) ) {
    $_SESSION['temp_group_name'] = $_POST['group_name'];
}
if ( ! empty ( $_POST['group_name_new'] ) ) {
    $_SESSION['temp_group_name_new'] = $_POST['group_name_new'];
}

//validate first name is set and if so throw it in a variable
if ( isset ( $_POST['fname'] ) ) {
    $fname = filter_var ( $_POST['fname'], FILTER_SANITIZE_STRING );
} else {
    $_SESSION['alert_message'] = "you must enter a first name";
    header ( 'location:./?add_one=true#tabs-1' );
    exit;
}

//validate last name is set and if so throw it in a variable
if ( isset ( $_POST['lname'] ) ) {
    $lname = filter_var ( $_POST['lname'], FILTER_SANITIZE_STRING );
} else {
    $_SESSION['alert_message'] = "you must enter a last name";
    header ( 'location:./?add_one=true#tabs-1' );
    exit;
}

//validate email is set and if so throw it in a variable
if ( isset ( $_POST['email'] ) ) {
    $email = $_POST['email'];
} else {
    $_SESSION['alert_message'] = "you must enter an email address";
    header ( 'location:./?add_one=true#tabs-1' );
    exit;
}

//validate that at least one of the groups is set and if so see which one is set and throw it in a variable
if ( isset ( $_POST['group_name'] ) || isset ( $_POST['group_name_new'] ) ) {
    if ( isset ( $_POST['group_name'] ) && $_POST['group_name'] != "Select an Existing Group..." ) {
        $group_name = $_POST['group_name'];
    }
    if ( strlen ( $_POST['group_name_new'] ) > 0 ) {
        $group_name_new = $_POST['group_name_new'];
    }
}
if ( ! isset ( $group_name ) && ! isset ( $group_name_new ) ) {
    $_SESSION['alert_message'] = "you must select an existing group or create a new group";
    header ( 'location:./?add_one=true#tabs-1' );
    exit;
}

//do a little validation on the email
if ( ! filter_var ( $email, FILTER_VALIDATE_EMAIL ) ) {
    $_SESSION['alert_message'] = "you must enter an actual email address";
    header ( 'location:./?add_one=true#tabs-1' );
    exit;
}
$email = filter_var($email, FILTER_SANITIZE_STRING);

//if they selected an existing group name lets ensure that they really selected an existing value
//connect to database
include "../spt_config/mysql_config.php";
if ( isset ( $group_name ) && $group_name != "Select an Existing Group..." ) {
    $r = mysql_query ( "SELECT DISTINCT group_name FROM targets" );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        if ( $ra['group_name'] == $group_name ) {
            $match = 1;
        }
    }
    if ( $match != 1 ) {
        $_SESSION["alert_message"] = "if your going to attempt to select a group that already exists, select one that already exists";
    header ( 'location:./?add_one=true#tabs-1' );
        exit;
    }
}

//if they are adding a new group name, validate it
if ( isset ( $group_name_new ) ) {
    if ( preg_match ( '/[^a-zA-Z0-9_-\s!.()]/', $group_name_new ) ) {
        $_SESSION["alert_message"] = "there are invalid characters in the group name";
    header ( 'location:./?add_one=true#tabs-1' );
        exit;
    }
}

//ensure that the email address is not already in this group
if ( isset ( $group_name ) ) {
    $r = mysql_query ( "SELECT email FROM targets WHERE group_name = '$group_name'" );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        if ( $ra['email'] == $email ) {
            $_SESSION['alert_message'] = "this email address is already in this group";
            header ( 'location:./?add_one=true#tabs-1' );
            exit;
        }
    }
}
if ( isset ( $group_name_new ) ) {
    $r = mysql_query ( "SELECT email FROM targets WHERE group_name = '$group_name_new'" );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        if ( $ra['email'] == $email ) {
            $_SESSION['alert_message'] = "this email address is already in this group";
            header ( 'location:./?add_one=true#tabs-1' );
            exit;
        }
    }
}

//enter the value in the database
//if existing group is selected
if ( isset ( $group_name ) ) {
    mysql_query ( "INSERT INTO targets (fname, lname, email, group_name) VALUES ('$fname', '$lname', '$email', '$group_name')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
} else {
    mysql_query ( "INSERT INTO targets (fname, lname, email, group_name) VALUES ('$fname', '$lname', '$email', '$group_name_new')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
}


//query for all metrics
$r = mysql_query ( "SELECT * FROM targets_metrics" );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $field_name = $ra['field_name'];

    if ( isset ( $_POST[$field_name] ) ) {
        $field_value = $_POST[$field_name];
        mysql_query ( "UPDATE targets SET $field_name = '$field_value' WHERE email = '$email'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    }
}

//if a group field was set then send them back to the group list
if ( isset ( $_POST['group_list'] ) && strlen ( $_POST['group_list'] ) > 0 ) {
    header ( 'location:./?g=' . $_POST['group_list'] . '&group_list=true#tabs-1' );
    exit;
}

//send user back to targets page with success message
$_SESSION['alert_message'] = "target added successfully";
header ( 'location:./?group_list=true#tabs-1' );
exit;
?>