<?php
/**
 * file:		target_update.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Target management
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license:		GNU/GPL, see license.htm.
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
**/

//start session
session_start();

//check for authenticated session
if($_SESSION['authenticated']!=1)
	{
		//for potential return
		$_SESSION['came_from']='targets';
		
		//set error message and send them back to login
		$_SESSION['login_error_message']="login first";
		header('location:../');
		exit;
	}

//check for session hijacking
elseif($_SESSION['ip']!=md5($_SESSION['salt'].$_SERVER['REMOTE_ADDR'].$_SESSION['salt']))
	{
		//set error message and send them back to login
		$_SESSION['login_error_message']="your ip address must have changed, please authenticate again";
		header('location:../');
		exit;
	}
		
//recieve post
$update_info=$_POST;

echo $update_info;
		
?>

