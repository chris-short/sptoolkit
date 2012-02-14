<?php
/**
 * file:		install.php
 * version:		0.5
 * package:		Simple Phishing Toolkit (spt)
 * component:	Upgrade
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

	//starts php session
	session_start();
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>spt - simple phishing toolkit</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="spt.css" type="text/css" />
	</head>
	
	<body>
		
		<!--login wrapper -->
		<div id="login_wrapper">
			
			<!--logo-->
			<div id="login_logo"><img src="images/logo.png" alt="logo"/></div>
			<div id="login_form">
			<?php

				//Step 1 - Begin Installation of v0.5 Upgrade
				if(isset($_POST['step1']) && $_POST['step1']=="complete")
					{
						//set install status to step 2 if step 1 has already been completed
						$_SESSION['install_status']=2;
					}

				if(!isset($_SESSION['install_status']) && !isset($_POST['step1']))
					{
						echo 
							"
								<form id=\"step_1\" method=\"post\" action=\"\">
									<span>Click below to begin the upgrade to Electric Catfish v0.5.</span>
									<br /><br />
									<input type=\"hidden\" name=\"step1\" value=\"complete\" />
									<input type=\"submit\" value=\"Begin!\" />
								</form>
							";
					}

				//Step 2 - Environmental Checks
				if(isset($_POST['step2']) && $_POST['step2']=="complete")
					{
						//set install status to step 2 if step 1 has already been completed
						$_SESSION['install_status']=3;
					}

				if(isset($_SESSION['install_status']) && $_SESSION['install_status']==2)
					{
						
						//Start Table
						echo "<table id=\"enviro_checks\">";

						//Environmental check introduction
						echo 
							"
								<tr>
									<td colspan=2>First, a couple of checks.  If there are any problems you will see an X below.  Hover over it for more information on the problem.</td>
								</tr>
							";

						//Ensure all files are readable, writeable and executable.
						echo 
							"
								<tr>
									<td>Appropriate Permissions</td>
							";

						foreach (glob("*") as $entity) 
							{
								if(is_dir($entity))
									{
										foreach (glob($entity."/"."*") as $sub_entity) 
											{
												if(is_dir($sub_entity))
													{
														foreach (glob($sub_entity."/"."*") as $sub_sub_entity) 
															{
																if(!is_readable($sub_sub_entity) || !is_writable($sub_sub_entity) || !is_executable($sub_sub_entity))
																	{
																		$permission_error = 1;
																	}
															}		
													}
												else if(!is_readable($sub_entity) || !is_writable($sub_entity) || !is_executable($sub_entity))
													{
														$permission_error = 1;
													}
											}
									}
								else if(!is_readable($entity) || !is_writable($entity) || !is_executable($entity))
									{
										$permission_error = 1;
									}
							}
												
						if(isset($permission_error))
							{
								echo
									"
										<td class=\"td_center\"><a class=\"tooltip\"><img src=\"images/x.png\" alt=\"problem\" /><span>The account that PHP runs under needs read, write and execute permissions for spt to function properly.  Visit sptoolkit.com for troubleshooting information on how to ensure you have the correct permissions set.<br /><br />If you are using WAMP, this may incorrectly state that permissions are not correct because Windows, in some cases does not accurately report if a file is executable or not.  99% of WAMP installs do not have permissions problems, so just click \"Proceed Anyways\".</span></a></td>
									";
							}
						else
							{
								echo 
									"
										<td class=\"td_center\"><img src=\"images/thumbs-up.png\" alt=\"success\" /></td>
									";
							}
						
						echo "</tr>";

						//Ensure all upgraded files were successfully copied over their replacements
						echo 
							"
								<tr>
									<td>All Files in Place</td>
							";


						function checkVersion($path,$expected)
							{
								//read in the file
								$file = file_get_contents($path);

								//check to see if the expected version exists
								if(!preg_match('#\* version:.*.'.$expected.'\s#', $file))
									{
										return $path;												
									}
							}

							//initialize array
							$failures = array();

							//Check these files using the function above
							array_push($failures, checkVersion("campaigns/start_campaign.php", "15.0"));
							array_push($failures, checkVersion("targets/group_delete.php", "7.0"));
							array_push($failures, checkVersion("targets/index.php", "29.0"));
							array_push($failures, checkVersion("targets/target_delete.php", "6.0"));
							array_push($failures, checkVersion("targets/target_upload_batch.php", "17.0"));
							array_push($failures, checkVersion("templates/upload_template.php", "6.0"));
							array_push($failures, checkVersion("users/add_user.php", "7.0"));
							array_push($failures, checkVersion("users/edit_other_user.php", "6.0"));
							array_push($failures, checkVersion("users/edit_user.php", "5.0"));
							array_push($failures, checkVersion("campaigns/delete_campaign.php", "4.0"));
							array_push($failures, checkVersion("campaigns/index.php", "24.0"));
							array_push($failures, checkVersion("campaigns/response.php", "4.0"));
							array_push($failures, checkVersion("education/delete_package.php", "6.0"));
							array_push($failures, checkVersion("education/upload_package.php", "6.0"));
							array_push($failures, checkVersion("login/validator.php", "4.0"));
							array_push($failures, checkVersion("errors/404_is_admin.php", "1.0"));
							array_push($failures, checkVersion("errors/404_is_authenticated.php", "1.0"));
							array_push($failures, checkVersion("dashboard/index.php", "4.0"));
							array_push($failures, checkVersion("education/index.php", "6.0"));
							array_push($failures, checkVersion("modules/index.php", "10.0"));
							array_push($failures, checkVersion("modules/module_cleanup.php", "7.0"));
							array_push($failures, checkVersion("modules/module_uninstall.php", "5.0"));
							array_push($failures, checkVersion("modules/module_upload.php", "8.0"));
							array_push($failures, checkVersion("targets/custom_update.php", "4.0"));
							array_push($failures, checkVersion("targets/target_update.php", "4.0"));
							array_push($failures, checkVersion("targets/target_upload_single.php", "12.0"));
							array_push($failures, checkVersion("templates/delete_template.php", "7.0"));
							array_push($failures, checkVersion("templates/index.php", "17.0"));
							array_push($failures, checkVersion("templates/scrape_it.php", "14.0"));
							array_push($failures, checkVersion("users/delete_user.php", "3.0"));
							array_push($failures, checkVersion("users/index.php", "11.0"));
							array_push($failures, checkVersion("campaigns/dashboard_module.php", "2.0"));
							array_push($failures, checkVersion("education/dashboard_module.php", "2.0"));
							array_push($failures, checkVersion("modules/dashboard_module.php", "2.0"));
							array_push($failures, checkVersion("targets/dashboard_module.php", "2.0"));
							array_push($failures, checkVersion("users/dashboard_module.php", "2.0"));
							array_push($failures, checkVersion("templates/dashboard_module.php", "2.0"));
							array_push($failures, checkVersion("templates/temp_upload/email.php", "6.0"));
							array_push($failures, checkVersion("includes/is_admin.php", "4.0"));
							array_push($failures, checkVersion("includes/is_authenticated.php", "2.0"));
							array_push($failures, checkVersion("index.php", "15.0"));
							array_push($failures, checkVersion("login/forgot_password.php", "4.0"));
							array_push($failures, checkVersion("login/logout.php", "2.0"));
							array_push($failures, checkVersion("includes/sidebar.php", "2.0"));
							array_push($failures, checkVersion("includes/escape.js", "1.0"));
							array_push($failures, checkVersion("targets/add_metric.php", "1.0"));
							array_push($failures, checkVersion("targets/delete_metric.php", "2.0"));
							array_push($failures, checkVersion("targets/spt_targets.css", "8.0"));
							array_push($failures, checkVersion("targets/update_metrics.php", "1.0"));
							array_push($failures, checkVersion("targets/target_export.php", "1.0"));
							array_push($failures, checkVersion("campaigns/campaigns_export.php", "4.0"));
							array_push($failures, checkVersion("campaigns/spt_campaigns.css", "6.0"));
							array_push($failures, checkVersion("spt.css", "11.0"));

						//initialize array
						$fails = array();

						//take out empties
						foreach($failures as $failure)
							{
								if(strlen($failure) > 0)
									{
										array_push($fails,$failure);
									}
							}

						if(count($fails) > 0)
							{
								echo 
									"
										<td class=\"td_center\"><a class=\"tooltip\"><img src=\"images/x.png\" alt=\"problem\" /><span>The following files did not report the expected version.  Please ensure you've uploaded and overwitten these files with the files in the upgrade download.<br /><br /><ul>";
								foreach($fails as $fail)
									{
										echo "<li>".$fail."</li>";
									}
								echo 
									"
										</ul></span></a></td>
									";
							}
						else
							{
								echo 
									"
										<td class=\"td_center\"><img src=\"images/thumbs-up.png\" alt=\"success\" /></td>
									";
							}
						
						echo "</tr>";

						//Ensure all enviromental checks pass
						if(isset($permission_error) OR count($fails) > 0)
							{
								$enviro_checks = 0;
							}
						else
							{
								$enviro_checks = 1;
							}

						//Provide buttons to check again or proceed with caution
						if($enviro_checks == 0)
							{
								echo 
									"
										<tr>
											<td>
												<form id=\"step_2_1\" method=\"post\" action=\"\">
													<input type=\"hidden\" name=\"step1\" value=\"complete\" />
													<input type=\"submit\" value=\"Check Again\" />
												</form>
											</td>
											<td>
												<form id=\"step_2_2\" method=\"post\" action=\"\">
													<input type=\"hidden\" name=\"step2\" value=\"complete\" />
													<input type=\"submit\" value=\"Upgrade Anyways!\" />
												</form>
											</td>
										</tr>
									";
							}

						//Provide a button to proceed if all checks pass
						if($enviro_checks == 1)
							{
								echo 
									"
										<tr>
											<td></td>
											<td>
												<form id=\"step_2\" method=\"post\" action=\"\">
													<input type=\"hidden\" name=\"step2\" value=\"complete\" />
													<input type=\"submit\" value=\"Upgrade!\" />
												</form>
											</td>
										</tr>
									";
							}
						
						//End Table
						echo "</table>";

					}


				//Step 3 - Upgrade Database
				if(isset($_SESSION['install_status']) && $_SESSION['install_status']==3)
					{
						//connect to database
						include "spt_config/mysql_config.php";

						//sql statements
						$sql = "ALTER TABLE modules CHANGE installed_date module_date DATE NOT NULL";
						mysql_query($sql) or die(mysql_error());

						$sql = "ALTER TABLE campaigns_responses ADD response_id varchar(40) DEFAULT NULL";
						mysql_query($sql) or die(mysql_error());

						$sql = "ALTER TABLE modules DROP enabled";
						mysql_query($sql) or die(mysql_error());

						$sql = "CREATE TABLE `targets_metrics` (
									`field_name` varchar(155) NOT NULL,
									`shown` int(1) NOT NULL
								)
							";
						mysql_query($sql) or die(mysql_error());

						$sql = "ALTER TABLE campaigns ADD date_sent varchar(255) NOT NULL";
						mysql_query($sql) or die(mysql_error());

						$sql = "ALTER TABLE targets ADD fname varchar(255) NOT NULL";
						mysql_query($sql) or die(mysql_error());

						$sql = "ALTER TABLE targets ADD lname varchar(255) NOT NULL";
						mysql_query($sql) or die(mysql_error());

						$sql = "SELECT id, name FROM targets";
						$results = mysql_query($sql) or die(mysql_error());

						while($result = mysql_fetch_assoc($results))
							{
								preg_match_all('#\w+#', $result['name'], $name_parts);
								$id = $result['id'];
								$fname = $name_parts[0][0];
								if(isset($name_parts[0][1]))
									{
										if(isset($name_parts[0][2]))
											{
												$fname .= " ".$name_parts[0][1];
												$lname = $name_parts[0][2];
												mysql_query("UPDATE targets SET lname = '$lname' WHERE id = '$id'");
											}
										else
											{
												$lname = $name_parts[0][1];
												mysql_query("UPDATE targets SET lname = '$lname' WHERE id = '$id'");
											}
									}
								mysql_query("UPDATE targets SET fname = '$fname' WHERE id = '$id'");
							}
						
						$sql = "ALTER TABLE targets DROP name";
						mysql_query($sql) or die(mysql_error());

						$sql = "SHOW COLUMNS FROM targets";
						$results = mysql_query($sql);
						while($result=mysql_fetch_assoc($results))
							{
								if($result['Field'] != "id" && $result['Field'] != "email" && $result['Field'] != "name" && $result['Field'] != "group_name" && $result['Field'] != "fname" && $result['Field'] != "lname")
									{
										$metric = $result['Field'];
										mysql_query("INSERT INTO targets_metrics (field_name, shown) VALUES ('$metric', 1)");
									}
							}

						//delete some files
						unlink('targets/targets.csv');

						echo "Upgrade complete!  Click Finish below to proceed to the login page and delete this installation file.<br /><br />
							<form method=\"post\" action=\".\">
								<input type=\"hidden\" name=\"delete_install\" value=\"1\" />
								<input type=\"submit\" value=\"Finish!\" />
							</form>
							";
					}
			?>
			</div>
		</div>
	</body>
</html>
