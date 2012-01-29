<?php
/**
 * file:		module_upload.php
 * version:		7.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Module management
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

	// verify session is authenticated and not hijacked
	$includeContent = "../includes/is_authenticated.php";
	if(file_exists($includeContent)){
		require_once $includeContent;
	}else{
		header('location:../errors/404_is_authenticated.php');
	}

	// verify user is an admin
	$includeContent = "../includes/is_admin.php";
	if(file_exists($includeContent)){
		require_once $includeContent;
	}else{
		header('location:../errors/404_is_admin.php');
	}

//ensure its a zip file
	if(preg_match('/^(zip)\i/',$_FILES["file"]["type"])) 
		{
			$_SESSION['alert_message'] = 'you must only upload zip files';
			header('location:./#alert');
			exit;
		}
			else

//ensure that the file is under 20M
	if($_FILES["file"]["size"] > 20000000)
		{
	  		$_SESSION['alert_message'] = 'max file size is 20MB';
	  		header('location:./#alert');
	  		exit;
	  	}

//ensure there are no errors
	  if ($_FILES["file"]["error"] > 0)
	    {
	    	$_SESSION['alert_message'] = $_FILES["file"]["error"];
	    	header('location:./#alert');
	    	exit;
	    }

//validate that the module doesn't already exist unless it is an upgrade
	//get the entire filename
	$filename = $_FILES["file"]["name"];
	
	//explode the filename
	$pieces = explode(".",$filename);
	//create a variable that is the name of the module
	$module_name = $pieces[0];
	//check to see if this is an upgrade package
	if($pieces[1] == "upgrade")
		{
			$module_upgrade = 1;
		}
	//pull all module names and do the check if its not an upgrade
	if(!isset($module_upgrade))
		{
			include "../spt_config/mysql_config.php";
			$r = mysql_query("SELECT name FROM modules") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
			while($ra=mysql_fetch_assoc($r))
				{
					if($module_name == $ra['name'])
						{
							$_SESSION['alert_message'] = 'there is already a module by this name and there was not an upgrade flag set';
							header('location:./#alert');
							exit;
						}
				}	
		}

//upload zip file to temp upload location
	mkdir('upload');
	move_uploaded_file($_FILES["file"]["tmp_name"], "upload/".$_FILES["file"]["name"]);
	
//delete existing code if an upgrade
	if(isset($module_upgrade))
		{
			rmdir('../'.$module_name);
			$_SESSION['installed_module_upgrade'] = 1;
		}
//determine what the filename of the file is
	$filename = $_FILES["file"]["name"];

//get directory name for the new module
	$basename = basename($filename, ".zip");
		
//extract file to its final destination
	$zip = new ZipArchive;
	$res = $zip->open('upload/'.$filename);
	if ($res === TRUE) 
		{
			$zip->extractTo('../'.$basename.'/');
	    	$zip->close();
			
			//go delete the original
			unlink('upload/'.$filename);
		} 
	else 
		{
    		$_SESSION['alert_message'] = 'unzipping the file failed';
    		header('location:./#alert');
    		exit;
		}

//execute the install file by including the install script that should be with each module
$path = "../".$module_name."/install.php";
include $path;

//pass what module was created to a session and then proceed to the cleanup script
$_SESSION['installed_module'] = $module_name;

header('location:../modules/module_cleanup.php');
exit;

?>