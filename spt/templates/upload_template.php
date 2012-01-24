<?php
/**
 * file:		upload_template.php
 * version:		2.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Template management
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
			$_SESSION['came_from']='templates';
			
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

//make sure the user is an admin
	if($_SESSION['admin']!=1)
		{
			$_SESSION['templates_alert_message'] = "you do not have permission to upload a template";
			header('location:../templates/#alert');
			exit;
		}
		
//validate that a name is provided
	if(!isset($_POST['name']))
		{
			$_SESSION['templates_alert_message'] = 'you must enter a name';
			header('location:../templates/#alert');
			exit;
		}

//validate that a description is provided
	if(!isset($_POST['description']))
		{
			$_SESSION['templates_alert_message'] = 'you must enter a description';
			header('location:../templates/#alert');
			exit;
		}

//set values
$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
$description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);

//validate a file was uploaded
	if(!is_uploaded_file($_FILES['file']['tmp_name']))
		{
			$_SESSION['templates_alert_message'] = 'you must upload a file';
			header('location:../templates/#alert');
			exit;
		}

//ensure its a zip file
	if(preg_match('/^(zip)\i/',$_FILES["file"]["type"]))
		{
			$_SESSION['templates_alert_message'] = 'you must only upload zip files';
			header('location:../templates/#alert');
			exit;
		}

//ensure that the file is under 20M
	if($_FILES["file"]["size"] > 20000000)
		{
	  		$_SESSION['templates_alert_message'] = 'max file size is 20MB';
	  		header('location:../templates/#alert');
	  		exit;
	  	}

//ensure there are no errors
	  if ($_FILES["file"]["error"] > 0)
	    {
	    	$_SESSION['templates_alert_message'] = $_FILES["file"]["error"];
	    	header('location:../templates/#alert');
	    	exit;
	    }


//add data to table
include "../spt_config/mysql_config.php";
mysql_query("INSERT INTO templates (name, description) VALUES ('$name','$description')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

//figure out the id of this new template
$r = mysql_query("SELECT MAX(id) as max FROM templates") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra = mysql_fetch_assoc($r))
	{
		$id = $ra['max'];	
	}

//create a temporary upload location
	mkdir('temp_upload');

//upload zip file to temp upload location
	move_uploaded_file($_FILES["file"]["tmp_name"], "temp_upload/".$_FILES["file"]["name"]);

//determine what the filename of the file is
	$filename = $_FILES["file"]["name"];

//make a directory for the new templated id
	mkdir($id);

//extract file to its final destination
	$zip = new ZipArchive;
	$res = $zip->open('temp_upload/'.$filename);
	if ($res === TRUE) 
		{
			$zip->extractTo('../templates/'.$id.'/');
			$zip->close();
			
			//go delete the original
			unlink('temp_upload/'.$filename);

			//delete the temp upload directory
			rmdir('temp_upload');
		
		} 
	else 
		{
			$_SESSION['templates_alert_message'] = 'unzipping the file failed';
			header('location:../templates/#alert');
			exit;
		}


	$_SESSION['templates_alert_message'] = 'template added successfully';
	header('location:../templates/#alert');
	exit;
?>