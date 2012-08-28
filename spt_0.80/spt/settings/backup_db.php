<?php

/**
 * file:    backup_db.php
 * version: 2.0
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
include '../spt_config/mysql_config.php';
//create the backup file name
$date = date('m_d_Y');
$backup_file = 'spt_backup_'.$date.".sql";
//strip the port off of the hostname
$mysql_host_parts = explode(":", $mysql_host);
$mysql_host = $mysql_host_parts[0];
//dump database to file using mysqldump
exec('mysqldump --user='.$mysql_user.' --password='.$mysql_password.' --host='.$mysql_host.' '.$mysql_db_name.' > '.$backup_file.'');
//send file to user
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($backup_file));
header('Cache-Control: must-revalidate');
header('Content-Length: ' . filesize($backup_file));
ob_clean();
flush();
readfile($backup_file);
unlink($backup_file);
?>
