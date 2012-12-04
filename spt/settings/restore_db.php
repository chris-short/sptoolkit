<?php

/**
 * file:    restore_db.php
 * version: 3.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Settings
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

//validate that a file was selected
if ( $_FILES["file"]["error"] > 0 ) {
    $_SESSION['alert_message'] = "you either did not select a file or there was a problem with it";
    header ( 'location:./#tabs-1' );
    exit;
}

//if file uploaded ensure its a sql file
if (is_uploaded_file($_FILES['file']['tmp_name']) && !preg_match("/sql/i",end(explode('.', $_FILES['file']['name'])))) {
    $_SESSION['alert_message'] = 'please ensure you are uploading a .sql file';
    header ( 'location:./#tabs-1' );
    exit;
}

//if file uploaded ensure that the file is under 20M
if ( is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES["file"]["size"] > 20000000 ) {
    $_SESSION['alert_message'] = 'max file size is 20MB';
    header ( 'location:./#tabs-1' );
    exit;
}

//if file uploaded ensure there are no errors
if ( is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES["file"]["error"] > 0 ) {
    $_SESSION['alert_message'] = "there was a problem uploading your file";
    header ( 'location:./#tabs-1' );
    exit;
}
//connect to database
include '../spt_config/mysql_config.php';
//strip the port off of the hostname
$mysql_host_parts = explode(":", $mysql_host);
$mysql_host = $mysql_host_parts[0];
//get file contents
$backup_file = $_FILES['file']['tmp_name'];
//restore database
exec('mysql --user='.$mysql_user.' --password='.$mysql_password.' --host='.$mysql_host.' '.$mysql_db_name.' < '.$backup_file.'');
//send user back with success message
$_SESSION['alert_message'] = "database restored";
header('location:./#tabs-1');
exit;

?>
