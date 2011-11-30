<?php
/**
 * file:		file_update.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Editor
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
			$_SESSION['came_from']='editor';
			
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

	//validate that the currently logged in user is an admin
		if($_SESSION['admin']!=1)
		{
			$_SESSION['editor_alert_message'] = "you do not have permission to edit template files";
			header('location:.#alert');
			exit;
		}

//validate both template id and file are set
if(!isset($_REQUEST['t']) OR !isset($_REQUEST['f']))
	{
		$_SESSION['editor_alert_message'] = "Please specify a template and file.";
		header('location:.#alert');
		exit;
	}

//validate the template id
if(preg_match('/[^0-9]/', $_REQUEST['t']))
	{
		$_SESSION['editor_alert_message'] = "Please select a valid template.";
		header('location:.#alert');
		exit;
	}

$template = $_REQUEST['t'];
$file = $_REQUEST['f'];
$changes = $_POST['file'];

file_put_contents("../templates/".$template."/".$file, $changes);

$_SESSION['editor_alert_message'] = "Your changes have been saved";
header('location:.#alert');
exit;

?>