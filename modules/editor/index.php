<?php
/**
 * file:		index.php
 * version:		3.0
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

	//turn off PHP error reporting, some platforms report error on form post url, but post url is correct
	error_reporting(0);
	
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
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>spt - editor</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="../images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_editor.css" type="text/css" />

		<!--script-->
		<script language="Javascript" type="text/javascript">
			function selectFile(template_id,file) 
				{ 
					//re-direct
					window.location = ".?t="+template_id+"&f="+file;								
				}
		</script>


	</head>
	<body>
		<?php
			//check to see if the alert session is set
			if(isset($_SESSION['editor_alert_message']))
				{
					//create alert popover
					echo "<div id=\"alert\">";

					//echo the alert message
					echo "<div>".$_SESSION['editor_alert_message']."<br />";
					
					//close the alert message
					echo "<br /><a href=\"\"><img src=\"../images/left-arrow.png\" alt=\"close\" /></a></div>";

					//close alert popover
					echo "</div>";

					//unset the seession
					unset ($_SESSION['editor_alert_message']);		
							
				}
		?>
		<div id="wrapper">
			<!--sidebar-->
			<?php include '../includes/sidebar.php'; ?>					

			<!--content-->
			<div id="content">
				<form id="editor_form" method="post" action="file_update.php?t=<?php echo $_REQUEST['t']."&f=".$_REQUEST['f']?>">
					<?php
						if(!isset($_REQUEST['t']))
							{
								//connect to database
								include "../spt_config/mysql_config.php";

								//pull in all the template id's and names
								$r = mysql_query("SELECT id,name FROM templates") or die('<div id="die_error">There is a problem with the database...please try again later</div>');;

								//error if there are no templates
								if(mysql_num_rows($r)==0)
									{
										$_SESSION['templates_alert_message'] = "You do not have any templates.  Please create a template first.";
										header('location:../templates/#alert');
										exit;
									}
								else
									{
										//create a popover that will list all templates and and available files to edit
										echo 
											"
												<table id=\"editor_buttons\"></table>
												<div id=\"template_select\">\n\t
													<div>
														<span>Select a file to edit from one of the templates listed below: </span>\n
														<br \><br \>
														<table id=\"template_list\">\n\t
											";		
										while($ra = mysql_fetch_assoc($r))
											{
													//start template row
													echo "<tr><td>".$ra['name']."</td><td><select onchange=\"selectFile(".$ra['id'].",this.value)\"><option value=\"\">select file...</option>";

													//query template directory for files
													$files = scandir('../templates/'.$ra['id'].'/');
													
													//do this process for each item
													foreach($files as $file)
														{
															//determine if the item is a directory or file
															if(!is_dir($file))
																{
																	//break apart the filename
																	$file_array = explode(".",$file);

																	//look for htm, php and html files
																	if($file_array[1] == "htm" OR $file_array[1] == "php" OR $file_array[1] == "html" OR $file_array[1] == "css" OR $file_array[1] == "js")
																		{
																			if($file == "license.htm")
																			{ }
																			else
																			{
																			//add the file to the drop-down
																			echo "<option value=\"".$file."\">".$file."</option>";
																			}
																		}
																}
														}
													
													//finish the template row
													echo "</select></td></tr>\n";
											}
										echo
											"
														</table>
													</div>
												</div>
											";
										exit;
									}
							}
						else if(!isset($_REQUEST['f']))
							{
								//send them back to select a template and file
								$_SESSION['editor_alert_message'] = "Please select a template and file first.";
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
						
						//pull in data
						$data = file_get_contents("../templates/".$_REQUEST['t']."/".$_REQUEST['f']);
						
						//show buttons
						echo 
							"
								<table id=\"editor_buttons\">
									<tr>
										<td>
											<a href=\".\"><img src=\"../images/x.png\" alt=\"close\" /></a>
											<input type=\"image\" src=\"../images/thumbs-up.png\" alt=\"save\" />	
										</td>
									</tr>
								</table>
							";

						//write data into textarea
						echo "<textarea id=\"editor\" name=\"file\">".$data."</textarea>";
					?>
				</form>
			</div>
		</div>	
	</body>
</html>
