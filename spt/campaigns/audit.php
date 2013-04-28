<?php
/**
 * file:    audit.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Campaign management
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
//start session
session_start();
include '../spt_config/mysql_config.php';
//get sessions
$response_id = $_SESSION['response_id'];
$link_time = $_SESSION['link_time'];
//get java value if java version session is set
if(isset($_GET['java_version'])){
	$java_version = $_GET['java_version'];
	mysql_query("UPDATE campaigns_responses SET java = '$java_version' WHERE response_id = '$response_id' AND link_time = '$link_time'");
	unset($_SESSION['check_java']);
}
//get flash value if flash version session is set
if(isset($_GET['flash_version'])){
	$flash_version = $_GET['flash_version'];	
	mysql_query("UPDATE campaigns_responses SET flash = '$flash_version' WHERE response_id = '$response_id' AND link_time = '$link_time'");
	unset($_SESSION['check_flash']);
}
//if java check is set send them to get the java version
if(isset($_SESSION['check_java'])){
	header('location:check_java.html');
	exit;
}
//if flash check is set send them to get the flash version
if(isset($_SESSION['check_flash'])){
	header('location:check_flash.html');
	exit;
}
//move on to the next step
$template_id = $_SESSION['template_id'];
$education_id = $_SESSION['education_id'];
$education_timing = $_SESSION['education_timing'];
//if the campaign is set to education immediately, send the target to be educated
if ( $education_id > 0 && $education_timing == 1 ) {
    header ( 'location:../education/' . $education_id . '/' );
    exit;
}
if(preg_match('/[0-9]/',$template_id)){
	//send the user to the appropriate template
	header ( 'location:../templates/' . $template_id . '/' );
	exit;	
}
header ( 'location:http://127.0.0.1' );
exit;	
?>
