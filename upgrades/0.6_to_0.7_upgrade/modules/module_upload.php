<?php

/**
 * file:    module_upload.php
 * version: 13.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Module management
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

//validate a file was uploaded
if ( ! is_uploaded_file ( $_FILES['file']['tmp_name'] ) ) {
    $_SESSION['alert_message'] = 'you must upload a file';
    header ( 'location:./#add_module' );
    exit;
}

//get file info
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$finfo = finfo_file($finfo, $_FILES["file"]["tmp_name"]);

//if file uploaded ensure its a zip file
if (is_uploaded_file($_FILES['file']['tmp_name']) && (!preg_match("/zip/i", $finfo) OR $_FILES["file"]["type"] != "application/zip")) {
    $_SESSION['alert_message'] = 'you must only upload zip files';
    header ( 'location:./#add_module' );
    exit;
} else

//ensure that the file is under 20M
if ( $_FILES["file"]["size"] > 20000000 ) {
    $_SESSION['alert_message'] = 'max file size is 20MB';
    header ( 'location:./#add_module' );
    exit;
}

//ensure there are no errors
if ( $_FILES["file"]["error"] > 0 ) {
    $_SESSION['alert_message'] = "there was a problem uploading your file";
    header ( 'location:./#add_module' );
    exit;
}

//get the filename
$filename = $_FILES["file"]["name"];

//create an upload directory for the unzipped file
mkdir ( 'upload' );

//extract the files
$zip = new ZipArchive;
$res = $zip -> open ( $_FILES["file"]["tmp_name"] );
if ( $res === TRUE ) {
    $zip -> extractTo ( 'upload/' );
    $zip -> close ();
} else {
    $_SESSION['alert_message'] = 'unzipping the file failed';
    header ( 'location:./#add_module' );
    exit;
}

//connect to the database
include "../spt_config/mysql_config.php";

//check to see if the install file exists and is in the right spot
if ( ! file_exists ( "upload/install.php" ) ) {
    $_SESSION['alert_message'] = "please check your module, there is no install.php file in the root of the zipped directory";
    header ( "location:./#add_module" );
    exit;
}

//get the name, path and upgrade status of this module
$install_file_contents = file_get_contents ( "upload/install.php" );
preg_match ( '#\$module_name\s=\s"(.*?)";#', $install_file_contents, $matches );
$module_name = ($matches[1]);
preg_match ( '#\$module_path\s=\s"(.*?)";#', $install_file_contents, $matches );
$module_path = ($matches[1]);
preg_match ( '#\$module_upgrade\s=\s(.*?);#', $install_file_contents, $matches );
$module_upgrade = ($matches[1]);

//alert if there is a module with the same name or path and this is not specified as an upgrade
if ( $module_upgrade != 1 ) {
    $r = mysql_query ( "SELECT name, directory_name FROM modules WHERE name ='$module_name' OR directory_name = '$module_path'" ) or die ( mysql_error () );
    if ( mysql_num_rows ( $r ) > 0 ) {
        $_SESSION['alert_message'] = "There is already a module with the same name or stored in the same directory as the module you are trying to upload.  If this is an upgrade, please specify it as such.";
        header ( 'location:./#add_module' );
        exit;
    }
}

//read in the install file
include "upload/install.php";

//if an upgrade, delete the existing directory
function rrmdir ( $dir ) {
    if ( is_dir ( $dir ) ) {
        $objects = scandir ( $dir );
        foreach ( $objects as $object ) {
            if ( $object != "." && $object != ".." ) {
                if ( filetype ( $dir . "/" . $object ) == "dir" )
                    rrmdir ( $dir . "/" . $object ); else
                    unlink ( $dir . "/" . $object );
            }
        }
        reset ( $objects );
        rmdir ( $dir );
    }
}

if ( $module_upgrade == 1 ) {
    rrmdir ( "../" . $module_path );
}

//move files to new directory
function recursive_copy ( $src, $dst ) {
    $dir = opendir ( $src );
    @mkdir ( $dst );
    while ( false !== ( $file = readdir ( $dir )) ) {
        if ( ( $file != '.' ) && ( $file != '..' ) ) {
            if ( is_dir ( $src . '/' . $file ) ) {
                recursive_copy ( $src . '/' . $file, $dst . '/' . $file );
            } else {
                copy ( $src . '/' . $file, $dst . '/' . $file );
            }
        }
    }
    closedir ( $dir );
}

recursive_copy ( "upload/", "../" . $module_path );

//delete upload directory
rrmdir ( "upload" );

//delete the install file
unlink ( "../" . $module_path . "/install.php" );

//set alert message and send them back
$_SESSION['alert_message'] = "The " . $module_name . " module has been successfully installed.  Look over to the left for a link to your new module!";
header ( 'location:./#alert' );
exit;
?>
