<?php

/**
 * file:    export_package.php
 * version: 1.0
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
//get education id
$education_id = filter_var($_GET['education_id'], FILTER_SANITIZE_NUMBER_INT );
//build zip file
exec('cd '.$education_id.';zip -r package_export *');
//send file to user
header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename=package_export.zip');
header('Content-Length: ' . filesize($education_id.'/package_export.zip'));
readfile($education_id.'/package_export.zip');
unlink($education_id.'/package_export.zip');
?>
