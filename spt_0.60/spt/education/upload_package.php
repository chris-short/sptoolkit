<?php

/**
 * file:    upload_package.php
 * version: 7.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Education
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

//validate that a name is provided
if ( ! isset ( $_POST['name'] ) ) {
    $_SESSION['alert_message'] = 'you must enter a name';
    header ( 'location:./#alert' );
    exit;
}

//validate that a description is provided
if ( ! isset ( $_POST['description'] ) ) {
    $_SESSION['alert_message'] = 'you must enter a description';
    header ( 'location:./#alert' );
    exit;
}

//set values
$name = filter_var ( $_POST['name'], FILTER_SANITIZE_STRING );
$description = filter_var ( $_POST['description'], FILTER_SANITIZE_STRING );

//validate a file was uploaded
if ( is_uploaded_file ( $_FILES['file']['tmp_name'] ) ) {
    //ensure its a zip file
    if ( preg_match ( '/^(zip)\i/', $_FILES["file"]["type"] ) ) {
        $_SESSION['alert_message'] = 'you must only upload zip files';
        header ( 'location:./#alert' );
        exit;
    }

    //ensure that the file is under 20M
    if ( $_FILES["file"]["size"] > 100000000 ) {
        $_SESSION['alert_message'] = 'max file size is 100MB';
        header ( 'location:./#alert' );
        exit;
    }

    //ensure there are no errors
    if ( $_FILES["file"]["error"] > 0 ) {
        $_SESSION['alert_message'] = $_FILES["file"]["error"];
        header ( 'location:./#alert' );
        exit;
    }

    //add data to table
    include "../spt_config/mysql_config.php";
    mysql_query ( "INSERT INTO education (name, description) VALUES ('$name','$description')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

    //figure out the id of this new education package
    $r = mysql_query ( "SELECT MAX(id) as max FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $id = $ra['max'];
    }

    //upload zip file to temp upload location
    move_uploaded_file ( $_FILES["file"]["tmp_name"], "temp_upload/" . $_FILES["file"]["name"] );

    //determine what the filename of the file is
    $filename = $_FILES["file"]["name"];

    //make a directory for the new templated id
    mkdir ( $id );

    //extract file to its final destination
    $zip = new ZipArchive;
    $res = $zip -> open ( 'temp_upload/' . $filename );
    if ( $res === TRUE ) {
        $zip -> extractTo ( '../education/' . $id . '/' );
        $zip -> close ();

        //go delete the original
        unlink ( 'temp_upload/' . $filename );
    } else {
        $_SESSION['alert_message'] = 'unzipping the file failed';
        header ( 'location:./#alert' );
        exit;
    }
} else {
    //add data to table
    include "../spt_config/mysql_config.php";
    mysql_query ( "INSERT INTO education (name, description) VALUES ('$name','$description')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

    //figure out the id of this new education package
    $r = mysql_query ( "SELECT MAX(id) as max FROM education" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
    while ( $ra = mysql_fetch_assoc ( $r ) ) {
        $id = $ra['max'];
    }

    //make a directory for the new templated id
    mkdir ( $id );

    //copy default files into new education package directory
    copy ( "temp_upload/index.htm", $id . "/index.htm" );
    copy ( "temp_upload/default.css", $id . "/default.css" );
    copy ( "temp_upload/logo.png", $id . "/logo.png" );
}

$_SESSION['alert_message'] = 'education package added successfully';
header ( 'location:./#alert' );
exit;
?>
