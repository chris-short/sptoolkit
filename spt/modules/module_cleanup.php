<?php
/**
 * file:		module_cleanup.php
 * version:		6.0
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

	//verify that their truly was a module just installed
		if(!isset($_SESSION['installed_module']))
			{
				$_SESSION['alert_message'] = 'you must install a module before you can do the cleanup';			
			}
		else
			{
				//cleanup after a clean install
				if(!isset($_SESSION['installed_module_upgrade']))
					{
						//delete the upload directory
						rmdir("upload");
						header('location:./#alert');
						exit;
					}
				
				//cleanup after an upgrade
				else
					{
						//delete the upgrade zip file
						unlink('upload/'.$_SESSION['installed_module'].'.upgrade.zip');
						$_SESSION['alert_message'] = "upgrade has completed successfully";
						
						//delete the install file
						unlink('../'.$_SESSION['installed_module'].'install.php');

						//delete the upload directory
						rmdir("upload");
						header('location:./#alert');
						exit;
					}
			}
?>