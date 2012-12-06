<?php

/**
 * file:    upload_package.php
 * version: 14.0
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
if (file_exists($includeContent)) {
    require_once $includeContent;
} else {
    header('location:../errors/404_is_authenticated.php');
}

// verify user is an admin
$includeContent = "../includes/is_admin.php";
if (file_exists($includeContent)) {
    require_once $includeContent;
} else {
    header('location:../errors/404_is_admin.php');
}

//set values
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

if (!empty($name)) {
    $_SESSION['temp_name'] = $name;
}
if (!empty($description)) {
    $_SESSION['temp_description'] = $description;
}

//validate that a name is provided
if (strlen($_POST['name']) < 1) {
    $_SESSION['alert_message'] = 'you must enter a name';
    header('location:./?add_package=true#tabs-1');
    exit;
}

//validate that a description is provided
if (!isset($_POST['description'])) {
    $_SESSION['alert_message'] = 'you must enter a description';
    header('location:./?add_package=true#tabs-1');
    exit;
}

//validate that a file was selected
if ( $_FILES["file"]["error"] > 0 ) {
    $_SESSION['alert_message'] = "you either did not select a file or there was a problem with it";
    header ( 'location:./?add_package=true#tabs-1' );
    exit;
}

//get file info
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$finfo = finfo_file($finfo, $_FILES["file"]["tmp_name"]);

//if file uploaded ensure its a zip file
if (is_uploaded_file($_FILES['file']['tmp_name']) && ( !preg_match("/zip/i", $finfo) OR $_FILES["file"]["type"] != "application/zip") ) {
    $_SESSION['alert_message'] = 'you must only upload zip files';
    header('location:./?add_package=true#tabs-1');
    exit;
}

//if file uploaded ensure that the file is under 20M
if (is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES["file"]["size"] > 20000000) {
    $_SESSION['alert_message'] = 'max file size is 20MB';
    header('location:./?add_package=true#tabs-1');
    exit;
}

//if file uploaded ensure there are no errors
if (is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES["file"]["error"] > 0) {
    $_SESSION['alert_message'] = "there was a problem uploading your file";
    header('location:./?add_package=true#tabs-1');
    exit;
}

//add data to table
include "../spt_config/mysql_config.php";
mysql_query("INSERT INTO education (name, description) VALUES ('$name','$description')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

//figure out the id of this new template
$r = mysql_query("SELECT MAX(id) as max FROM education") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while ( $ra = mysql_fetch_assoc ( $r ) ) {
    $id = $ra['max'];
}

//if file uploaded move zip file to temp upload location
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        move_uploaded_file($_FILES["file"]["tmp_name"], "temp_upload/" . $_FILES["file"]["name"]);

        //determine what the filename of the file is
        $filename = $_FILES["file"]["name"];

        //make a directory for the new templated id
        mkdir($id);

        //extract file to its final destination
        $zip = new ZipArchive;
        $res = $zip->open('temp_upload/' . $filename);
        if ($res === TRUE) {
            $zip->extractTo('../education/' . $id . '/');
            $zip->close();

            //go delete the original
            unlink('temp_upload/' . $filename);
        } else {
            //clean up
            unlink ( 'temp_upload/' . $filename );
            rmdir ( '../education/' . $id . '/' );
            mysql_query ( "DELETE FROM education WHERE id = '$id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );        
            //return back to form
            $_SESSION['alert_message'] = 'unzipping the file failed';
            header('location:./?add_package=true#tabs-1');
            exit;
        }
    }
    unset($_SESSION['temp_name']);
    unset($_SESSION['temp_description']);
    $_SESSION['alert_message'] = 'education package added successfully';
    header('location:./#tabs-1');
    exit;
?>
