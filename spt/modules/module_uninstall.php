<?php
/**
 * file:		module_uninstall.php
 * version:		2.0
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

	//start session
	session_start();
	
	//check for authenticated session
	if($_SESSION['authenticated']!=1)
		{
			//for potential return
			$_SESSION['came_from']='users';
			
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
				$_SESSION['module_alert_message'] = "you do not have permission to uninstall a module";
				header('location:../modules/#alert');
				exit;
			}

	//pull in the passed module to be uninstalled
	$module = $_REQUEST['m'];

	//connect to database
	include "../spt_config/mysql_config.php";


	//pull in all module names and their path
		$r = mysql_query("SELECT name, directory_name FROM modules WHERE core = 0")  or die('<div id="die_error">There is a problem with the database...please try again later</div>');
		while($ra=mysql_fetch_assoc($r))
			{

				//validate that the module is not depended on
				$r2=mysql_query("SELECT * FROM modules_dependencies WHERE depends_on = '$module'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
				if(mysql_num_rows($r2) > 0)
					{
						$_SESSION['module_alert_message'] = 'this module is depended on';
						header('location:../modules/#alert');
						exit;
					}
				
				//proceed with the uninstall
				if($ra['name'] == $module)
					{
						//delete the directory
						$path = "../".$ra['directory_name'];
						rmdir($path);

						//get the db name to prepare for table listing
						$db_name = $_SESSION['spt_db_name'];

						$tables = mysql_list_tables($db_name) or die('<div id="die_error">There is a problem with the database...please try again later</div>');

						while (list($table) = mysql_fetch_row($tables))
							{
								if(stristr($module, $table))
									{
										mysql_query("DROP TABLE $table")  or die('<div id="die_error">There is a problem with the database...please try again later</div>');
									}
							}
		
						//delete all dependency data
						mysql_query("DELETE FROM modules_dependencies WHERE module = '$module'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

						//delete module entry in modules table
						mysql_query("DELETE FROM modules WHERE name = '$module'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

						//if the uninstall went well send them back
						$_SESSION['module_alert_message'] = 'uninstall successful';
						header('location:../modules/#alert');
						exit;

					}
			}
		//if the uninstall did not happen, send them back with an alert
		$_SESSION['module_alert_message'] = "you must only uninstall valid, non-core modules";
		header('location:../modules/#alert');
		exit;
?>