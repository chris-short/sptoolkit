<?php

/**
 * file:    upload_template.php
 * version: 10.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Template management
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

//set values
$name = filter_var ( $_POST['name'], FILTER_SANITIZE_STRING );
$description = filter_var ( $_POST['description'], FILTER_SANITIZE_STRING );

if ( ! empty ( $name ) ) {
    $_SESSION['temp_name'] = $name;
}
if ( ! empty ( $description ) ) {
    $_SESSION['temp_description'] = $description;
}

//validate that a name is provided
if ( strlen ( $_POST['name'] ) < 1 ) {
    $_SESSION['alert_message'] = 'you must enter a name';
    header ( 'location:./#add_template' );
    exit;
}

//validate that a description is provided
if ( ! isset ( $_POST['description'] ) ) {
    $_SESSION['alert_message'] = 'you must enter a description';
    header ( 'location:./#add_template' );
    exit;
}

//if file uploaded ensure its a zip file
if ( is_uploaded_file($_FILES['file']['tmp_name']) && preg_match ( '/^(zip)\i/', $_FILES["file"]["type"] ) ) {
    $_SESSION['alert_message'] = 'you must only upload zip files';
    header ( 'location:./#add_template' );
    exit;
}

//if file uploaded ensure that the file is under 20M
if ( is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES["file"]["size"] > 20000000 ) {
    $_SESSION['alert_message'] = 'max file size is 20MB';
    header ( 'location:./#add_template' );
    exit;
}

//if file uploaded ensure there are no errors
if ( is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES["file"]["error"] > 0 ) {
    $_SESSION['alert_message'] = "there was a problem uploading your file";
    header ( 'location:./#add_template' );
    exit;
}

//add data to table
include "../spt_config/mysql_config.php";
mysql_query ( "INSERT INTO templates (name, description) VALUES ('$name','$description')" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

//figure out the id of this new template
$r = mysql_query ( "SELECT MAX(id) as max FROM templates" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//if file uploaded move zip file to temp upload location
if (is_uploaded_file($_FILES['file']['tmp_name'])){
    move_uploaded_file ( $_FILES["file"]["tmp_name"], "temp_upload/" . $_FILES["file"]["name"] );

    //determine what the filename of the file is
    $filename = $_FILES["file"]["name"];

    //make a directory for the new templated id
    mkdir ( $id );

    //extract file to its final destination
    $zip = new ZipArchive;
    $res = $zip -> open ( 'temp_upload/' . $filename );
    if ( $res === TRUE ) {
        $zip -> extractTo ( '../templates/' . $id . '/' );
        $zip -> close ();

        //go delete the original
        unlink ( 'temp_upload/' . $filename );
    } else {
        $_SESSION['alert_message'] = 'unzipping the file failed';
        header ( 'location:./#add_template' );
        exit;
    }
}else{
    //create a directory for the new template
    mkdir ( $id );
    //copy scraped file into new template directory
    copy ( "temp_upload/index.htm", $id . "/index.htm" );
    //copy default email and return files into new template directory
    copy ( "temp_upload/return.htm", $id . "/return.htm" );
    copy ( "temp_upload/email.php", $id . "/email.php" );
    copy ( "temp_upload/screenshot.png", $id . "/screenshot.png" );
    //set correct permissions on newly created files
    $directory = $id;
    $filemode = 0775;
    function chmodr ( $directory, $filemode ) {
        if ( ! is_dir ( $directory ) )
            return chmod ( $directory, $filemode );
        $dh = opendir ( $directory );
        while ( ($file = readdir ( $dh )) !== false ) {
            if ( $file != '.' && $file != '..' ) {
                $fullpath = $directory . '/' . $file;
                if ( is_link ( $fullpath ) )
                    return FALSE;
                elseif ( ! is_dir ( $fullpath ) && ! chmod ( $fullpath, $filemode ) )
                    return FALSE;
                elseif ( ! chmodr ( $fullpath, $filemode ) )
                    return FALSE;
            }
        }
        closedir ( $dh );
        if ( chmod ( $directory, $filemode ) )
            return TRUE;
        else
            return FALSE;
    }
    chmodr ( $directory, $filemode );
}



$_SESSION['alert_message'] = 'template added successfully';
header ( 'location:./#alert' );
exit;
?>