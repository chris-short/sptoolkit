<?php
/**
 * file:		install.php
 * version:		3.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Installation
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

				//Step 1 - Welcome & License
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
									<span>Welcome! Ready to begin? <br /><br />By proceeding forward, you accept the license agreement below.</span>
									<br /><br />
									<iframe src=\"license.htm\" width=\"100%\" height=\"175\">
										<p>Your browser does not support iframes.</p>
									</iframe>
									<br /><br />
									<input type=\"hidden\" name=\"step1\" value=\"complete\" />
									<input type=\"submit\" value=\"I Agree!\" />
								</form>
							";
					}

				//Step 2 - Install Database
				if(isset($_POST['step2']) && $_POST['step2']=="complete")
						{
							//validate the database config data
								$host = $_POST['host'];
								$port = $_POST['port'];
								$username = trim($_POST['username']);
								$password = trim($_POST['password']);
								$database = trim($_POST['database']);

									//validate that all fields were entered
									if(!$host || !$port || !$username || !$password || !$database)
										{
											$_SESSION['install_status']=2;
											echo
												"
													Please enter something in all fields!<br /><br />
													<form id=\"empty_error\" method=\"post\" action\"\">
														<input type=\"submit\" value=\"< back\" />
													</form>
												";
											exit;
										}

									//validate the host has only acceptable characters and at least one period
										if(preg_match('/[^a-zA-Z0-9-\.]/', $host))
											{
												$_SESSION['install_status']=2;
												echo
													"
														Please enter a valid hostname!<br /><br />
														<form id=\"host_error\" method=\"post\" action\"\">
															<input type=\"submit\" value=\"< back\" />
														</form>
													";
												exit;
											}

									//validate that the port is of valid length
										if($port < 1 || $port > 65535)
											{
												$_SESSION['install_status']=2;
												echo
													"
														Please enter a port number between 1 and 65535<br /><br />
														<form id=\"port_error\" method=\"post\" action\"\">
															<input type=\"submit\" value=\"< back\" />
														</form>
													";
												exit;
											}
							
							//connect to MySQL server
								$link = mysql_connect($host.":".$port, $username, $password);

								if(!$link)
									{
										$_SESSION['install_status']=2;
										echo
											"
												Could not connect to ".$host.":".$port.".<br /><br />
												MySQL Error: ".mysql_error()."<br /><br />
												<form id=\"host_error\" method=\"post\" action\"\">
													<input type=\"submit\" value=\"< back\" />
												</form>
											";										;
										exit;
									}
							
							//connect to database
								$dbcheck = mysql_select_db($database);

								if(!$dbcheck)
									{
										$_SESSION['install_status']=2;
										echo
											"
												Could not connect to ".$database.".<br /><br />
												MySQL Error: ".mysql_error()."<br /><br />
												<form id=\"host_error\" method=\"post\" action\"\">
													<input type=\"submit\" value=\"< back\" />
												</form>
											";										;
										exit;
									}
							
							//if you get connected run all the install files
								$dirs = scandir('.');
							
								//for each directory look for sql_install.php
									foreach($dirs as $dir)
										{
											if(is_dir($dir))
												{
													//if sql_install.php exists in the directory, run it and delete it
													if(file_exists($dir.'/sql_install.php'))
														{
															include $dir."/sql_install.php";
															unlink ($dir."/sql_install.php");
														}
												}
										}
									
									//run the salt install script
									include "salt_install.php";

									//delete the salt install script
									unlink ("salt_install.php");

									//populate the mysql_config.php file in the spt_config directory
										function f_and_r($find, $replace, $path)
											{
												$find = "#".$find."#";
												$globarray = glob($path);
												if ($globarray) foreach ($globarray as $filename) 
													{
													  $source = file_get_contents($filename);
													  $source = preg_replace($find,$replace,$source);
													  file_put_contents($filename,$source);
													}
											}
										
										f_and_r("mysql_host='(.*?)';", "mysql_host='".$host.":".$port."';", "spt_config/mysql_config.php");
										f_and_r("mysql_user='(.*?)';", "mysql_user='".$username."';", "spt_config/mysql_config.php");
										f_and_r("mysql_password='(.*?)';", "mysql_password='".$password."';", "spt_config/mysql_config.php");
										f_and_r("mysql_db_name='(.*?)';", "mysql_db_name='".$database."';", "spt_config/mysql_config.php");

								//echo back the install tables and successfull database configuration
								echo "Database connectivity has been established and the following tables have been installed into ".$database.":<br />";
								echo "<ul>";

								$r = mysql_query('SHOW TABLES');
								while($ra = mysql_fetch_row($r))
									{
										echo "<li>".$ra[0]."</li>";
									}
								echo "</ul>";
								
								//set the install status to move along to step 3
								$_SESSION['install_status']=3;

								//provide the button to move along
								echo
									"
										<form id=\"database_install_complete\" method=\"post\" action\"\">
											<input type=\"submit\" value=\"Continue!\" />
										</form>
									";										
								exit;
						}

				if(isset($_SESSION['install_status']) && $_SESSION['install_status']==2)
					{
						echo
							"
								<form id=\"step_2\" method=\"post\" action=\"\">
									<span>Do you have a database we can use?<br /><br />...If not, you'll need one (MySQL at that).  Please come back when you have one.</span>
									<br /><br />
									<table>
										<tr>
											<td>Host</td>
											<td><input type=\"text\" name=\"host\" value=\"localhost\"/></td>
										</tr>
										<tr>
											<td>Port</td>
											<td><input type=\"text\" name=\"port\" value=\"3306\" /></td>
										</tr>
										<tr>
											<td>Username</td>
											<td><input type=\"text\" name=\"username\" /></td>
										</tr>
										<tr>
											<td>Password</td>
											<td><input type=\"password\" name=\"password\" autocomplete=\"off\"/></td>
										</tr>
										<tr>
											<td>Database</td>
											<td><input type=\"database\" name=\"database\" /></td>
										</tr>
											<input type=\"hidden\" name=\"step2\" value=\"complete\" />
										<tr>
											<td><br /><input type=\"submit\" value=\"Install Database!\" /></td>
										</tr>
									</table>
								</form>
							";
					}

				//Step 3 - Configure Salt
				if(isset($_SESSION['install_status']) && $_SESSION['install_status']==3)
					{
						$salt = '';
						
						//generate salt
						function genRandomString() 
							{
							    $length = 50;
							    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
								$salt = 'p';
							    for ($p = 0; $p < $length; $p++) {
							        $salt .= $characters[mt_rand(0, strlen($characters) - 1)];
							    }
							    return $salt;
							}

						$salt = genRandomString();


						//enter salt value into database
						include "spt_config/mysql_config.php";
						mysql_query("INSERT INTO salt (salt) VALUES ('$salt')");

						$_SESSION['install_status']=4;
					}

				//Step 4 - Configure First User
				if(isset($_POST['step4']) && $_POST['step4'] == "complete")
					{
						//validate that the newly entered username is a valid email address
						$new_username = $_POST['username'];
						if(!filter_var($new_username, FILTER_VALIDATE_EMAIL))
							{
								echo
									"
										Please enter a valid email address<br /><br />
										<form id=\"username_error\" method=\"post\" action\"\">
											<input type=\"submit\" value=\"< back\" />
										</form>
									";
								exit;
							}

						//validate that the username is not too long
						if(strlen($new_username) > 50)
							{
								echo
									"
										This email address is too long<br /><br />
										<form id=\"username_error\" method=\"post\" action\"\">
											<input type=\"submit\" value=\"< back\" />
										</form>
									";
								exit;
							}
						
						//make sure its only letters
						$new_fname = $_POST['first_name'];
						if(preg_match('/[^a-zA-Z]/', $new_fname))
							{
								echo
									"
										Your first name may only contain letters<br /><br />
										<form id=\"first_name_error\" method=\"post\" action\"\">
											<input type=\"submit\" value=\"< back\" />
										</form>
									";
								exit;
							}

						//make sure the first name is between 1 and 50 characters
						if(strlen($new_fname) > 50 || strlen($new_fname) < 1)
							{
								echo
									"
										Your first name must be between 1 and 50 characters<br /><br />
										<form id=\"first_name_error\" method=\"post\" action\"\">
											<input type=\"submit\" value=\"< back\" />
										</form>
									";
								exit;
							}

						//make sure the last name is between 1 and 50 characters
						$new_lname = $_POST['last_name'];
						if(strlen($new_lname) > 50 || strlen($new_lname) < 1)
							{
								echo
									"
										Your last name must be between 1 and 50 characters<br /><br />
										<form id=\"last_name_error\" method=\"post\" action\"\">
											<input type=\"submit\" value=\"< back\" />
										</form>
									";
								exit;
							}

						//make sure the last name only contains letters
						if(preg_match('/[^a-zA-Z]/', $new_lname))
							{
								echo
									"
										Your last name may only contain letters<br /><br />
										<form id=\"last_name_error\" method=\"post\" action\"\">
											<input type=\"submit\" value=\"< back\" />
										</form>
									";
								exit;
							}

						//validate the password if it is set
						if(!empty($_POST['password']))
							{
								//pull in password to temp variable
								$temp_p = $_POST['password'];
								
								//validate the password doesn't have any characters that are not allowed
								if(preg_match('/[$+*"=&%]/', $temp_p))
									{ 
										echo
											"
												Your password contains special characters that are not allowed ($+*\"=&%)<br /><br />
												<form id=\"password_error\" method=\"post\" action\"\">
													<input type=\"submit\" value=\"< back\" />
												</form>
											";
										exit;
									} 
								
								//validate that the password is an acceptable length
								if(strlen($temp_p) > 15 || strlen($temp_p) < 8)
									{
										echo
											"
												The password must be between 8 and 15 characters in length<br /><br />
												<form id=\"password_error\" method=\"post\" action\"\">
													<input type=\"submit\" value=\"< back\" />
												</form>
											";
										exit;
									}

								//connect to database
								include 'spt_config/mysql_config.php';
								
								//get the salt value
								$r = mysql_query("SELECT salt FROM salt");
								while ($ra = mysql_fetch_assoc($r))
									{
										$salt = $ra['salt'];
									}
								
								//pass temp password to new variable that has been salted and hashed
								$p = sha1($salt.$temp_p.$salt);
							}
						else
							{
								echo
									"
										Your must enter a password.<br /><br />
										<form id=\"password_error\" method=\"post\" action\"\">
											<input type=\"submit\" value=\"< back\" />
										</form>
									";
								exit;
							}

						//add first user to database
						include "spt_config/mysql_config.php";
						mysql_query("INSERT INTO users(fname, lname, username, password, admin, disabled) VALUES ('$new_fname','$new_lname','$new_username','$p','1','0')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

						$_SESSION['install_status']=5;
					}
				if(isset($_SESSION['install_status']) && $_SESSION['install_status']==4)
					{
						echo
							"
								Lets create the first user!  Enter the Information below that you will use to log into SPT for the first time.<br /><br />
								<form id=\"initial_user\" method=\"post\" action\"\">
									<table>
										<tr>
											<td>First Name</td>
											<td><input type=\"text\" name=\"first_name\" /></td>
										</tr>
										<tr>
											<td>Last Name</td>
											<td><input type=\"text\" name=\"last_name\" /></td>
										</tr>
										<tr>
											<td>Email</td>
											<td><input type=\"text\" name=\"username\" /></td>
										</tr>
										<tr>
											<td>Password</td>
											<td><input type=\"password\" name=\"password\" /></td>
										</tr>
											<input type=\"hidden\" name=\"step4\" value=\"complete\" />
										<tr>
											<td><br /><input type=\"submit\" value=\"Create User\" /></td>
											<td></td>
										</tr>
									</table>
								</form>									
							";
					}

				//Step 5 - Send User to login page
				if(isset($_SESSION['install_status']) && $_SESSION['install_status']==5)
					{
						echo
							"
								You have successfully installed the Simple Phishing Toolkit!<br /><br />

								You may now proceed to the login screen which will delete this install file.<br /><br />

								<form id=\"done\" method=\"post\" action=\"index.php\">
									<input type=\"hidden\" value=\"delete_install\" name=\"delete_install\" />
									<input type=\"submit\" value=\"Proceed to Login\" />
								</form>
							";
					}
			?>
			</div>
		</div>
	</body>
</html>
