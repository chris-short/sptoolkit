<?php
/**
 * file:		index.php
 * version:		29.0
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

	// verify session is authenticated and not hijacked
	$includeContent = "../includes/is_authenticated.php";
	if(file_exists($includeContent)){
		require_once $includeContent;
	}else{
		header('location:../errors/404_is_authenticated.php');
	}
	
?>
<!DOCTYPE HTML> 
<html>
	<head>
		<title>spt - targets</title>
		
		<!--meta-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
		
		<!--favicon-->
		<link rel="shortcut icon" href="../images/favicon.ico" />
		
		<!--css-->
		<link rel="stylesheet" href="../spt.css" type="text/css" />
		<link rel="stylesheet" href="spt_targets.css" type="text/css" />

		<!--scripts-->
		<script language="Javascript" type="text/javascript">
			function updateCustom(custom,value) 
				{ 
					//begin new request
					xmlhttp = new XMLHttpRequest();

					//send update request
					xmlhttp.open("POST","custom_update.php",false);
					xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					xmlhttp.send("c="+custom+"&n="+value);					
				}
		</script>
		<script language="Javascript" type="text/javascript">
			function updateTarget(id,column,data) 
				{ 
					//begin new request
					xmlhttp = new XMLHttpRequest();

					//send update request
					xmlhttp.open("POST","target_update.php",false);
					xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					xmlhttp.send("id="+id+"&column="+column+"&data="+data);
					
				}
		</script>
		<script language="Javascript" type="text/javascript">
			function updateMetrics(field_name) 
				{ 
					//begin new request
					xmlhttp = new XMLHttpRequest();

					//get checked status
					var check = document.getElementById("checkbox_"+field_name).checked;

					//send update request
					xmlhttp.open("POST","update_metrics.php",false);
					xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					xmlhttp.send("field_name="+field_name+"&shown="+check);

					if (xmlhttp.responseText != "set")
					  {
					  	window.location = ".#alert"
					  	window.location.reload()
					  }					
					
				}
		</script>
		<script type="text/javascript" src="../includes/escape.js"></script>
		
	</head>
	<body>
		<?php
			//check to see if the alert session is set
			if(isset($_SESSION['alert_message']))
				{
					//create alert popover
					echo "<div id=\"alert\">";

					//echo the alert message
					echo "<div>".$_SESSION['alert_message']."<br />";
					
					if(isset($_SESSION['bad_row_stats']))
						{
							//count how many stats there are
							$count = count($_SESSION['bad_row_stats']);
							
							//start the list
							echo "<ul>";
							
							//echo all bad row stats
							while($count > 0)
								{
									echo "<li>".$_SESSION['bad_row_stats'][($count-1)]."</li>";
									$count--;
								}
							
							//end the list
							echo "</ul>";
							
							//unset bad row stat session
							unset ($_SESSION['bad_row_stats']);
						}

					//close the alert message
					echo "<br /><a href=\"\"><img src=\"../images/left-arrow.png\" alt=\"close\" /></a></div>";

					//close alert popover
					echo "</div>";

					//unset the seession
					unset ($_SESSION['alert_message']);		
							
				}
		?>
		<div id="add_one">
			<div>
				<form action="target_upload_single.php" method="post" enctype="multipart/form-data">
					<table id="add_single">
						<tr>
							<td>First Name</td>
							<td><input type="text" name="fname" /></td>
							<td>
								<a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>Enter the target's name, valid email address and then select an existing or new group to add the new target to.</span></a>
							</td>
						</tr>
						<tr>
							<td>Last Name</td>
							<td><input type="text" name="lname" /></td>
						</tr>
						<tr>
							<td>Email</td>
							<td><input type="text" name="email" /></td>
						</tr>
						<tr>
							<td>Existing Group</td>
							<td>
								<select name="group_name">
									<option value="Select an Existing Group...">Select an Existing Group...</option>
									<?php
										//connect to database
										include "../spt_config/mysql_config.php";
										
										//pull in current group names
										$r = mysql_query("SELECT DISTINCT group_name FROM targets ORDER BY group_name") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
										while ($ra = mysql_fetch_assoc($r))
											{
												echo "<option value=\"".$ra['group_name']."\">".$ra['group_name']."</option>";
											}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								OR
							</td>
						</tr>
						<tr>
							<td>New Group</td>
							<td> 
								<input type="text" name="group_name_new" />
							</td>
						</tr>
						<tr>
							<td><br /></td>
							<td><br /></td>
						</tr>
						<tr>
							<td><h3>Custom Attributes</h3</td>
							<td></td>
						</tr>
						<?php
							//query for all metrics
							$r = mysql_query("SELECT * FROM targets_metrics");
							while($ra = mysql_fetch_assoc($r))
								{
									echo 
										"
											<tr>
												<td>".$ra['field_name']."</td>
												<td><input type=\"text\" name=\"".$ra['field_name']."\" /></td>
											</tr>
										";	
								}
						?>
						<tr>	
							<td></td>
							<td>
								<br />
								<a href=""><img src="../images/x.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="image" src="../images/plus.png" alt="add" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<div id="add_many">
			<div>
				<table id="add_bunches">
					<form action="target_upload_batch.php" method="post" enctype="multipart/form-data">
						<tr>
							<td>
								<input type="file"  name="file" />
							</td>
							<td>
								<a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>Upload a csv file with a header row that contains a column for the required columns (fname, lname email, group) as well as any additional attributes you have added.  If you do not match the current set of column headings, the upload will fail.<br /><br />Export the current list by clicking on the export button, even if you have no targets it will make a good template.</span></a>
							</td>
						</tr>
						<tr>
							<td>
								<br />
								<a href=""><img src="../images/x.png" alt="cancel" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="image" src="../images/plus.png" alt="add" />
							</td>
							<td></td>
					</form>
				</table>
			</div>
		</div>
		<div id="metrics">
			<div>
				<form action="add_metric.php" method="post" enctype="multipart/form-data">
					<table id="add metric">
						<tr>
							<td colspan=2></td>
							<td>
								<a class="tooltip"><img src="../images/lightbulb.png" alt="help" /><span>Adding a metric will create a new column in the database for tracking target metrics.<br /><br />Check the box next to 5 metrics and those will be the 5 that are displayed in the group list pop-over.</span></a>
							&nbsp;&nbsp;&nbsp;
							<a href="."><img src="../images/x.png" alt="close" /></a>
							</td>
						</tr>
						<tr>
							<td>Add Metric</td>
							<td><input type="text" name="metric" /></td>
							<td><input type="image" src="../images/plus.png" alt="add" /></td>
							<td></td>
						</tr>
					</table>
				</form>
					<table id="manage_metrics">
						<tr>
							<td>-</td>
							<td>First Name</td>
							<td>-</td>
						</tr>
						<tr>
							<td>-</td>
							<td>Last Name</td>
							<td>-</td>
						</tr>
						<tr>
							<td>-</td>
							<td>Email</td>
							<td>-</td>
						</tr>
						<tr>
							<td>-</td>
							<td>Group</td>
							<td>-</td>
						</tr>
						<?php
							
							//connect to database
							include "../spt_config/mysql_config.php";

							//query for all metrics
							$r = mysql_query("SELECT * FROM targets_metrics");

							while($ra = mysql_fetch_assoc($r))
								{
									echo "<tr>
										<td><input id=\"checkbox_".$ra['field_name']."\"type=\"checkbox\" name=\"".$ra['field_name']."\" onclick=\"updateMetrics('".$ra['field_name']."')\" value=\"".$ra['field_name']."\"";
									
									if($ra['shown']==1)
										{
											echo "checked";
										}
									
									echo "
										></td>
										<td>".$ra['field_name']."</td>
										<td><a href=\"delete_metric.php?m=".$ra['field_name']."\"><img src=\"../images/trash_sm.png\" alt=\"delete\" /></a></td>
										</tr>
									";
								}
						?>
					</table>
			</div>
		</div>
		<div  id="group_list">
			<div>
				<table id="group_list_header">
					<tr>
						<td class="left">
							<h1><?php if(isset($_REQUEST['g'])){echo filter_var($_REQUEST['g'], FILTER_SANITIZE_STRING);}else{echo "All Targets";}?></h1>
						</td>
						<td class="right">
							<a class="tooltip">
								<img src="../images/lightbulb.png" alt="help" />
								<span>You can easily edit any cell by clicking on it and making your changes.  Changes are automatically saved when you click anywhere <strong>outside</strong> of the cell just edited. 
								</span>
							</a>
							&nbsp;&nbsp;&nbsp;
							<a href="."><img src="../images/x.png" alt="close" /></a>
						</td>
					</tr>
				</table>
				<br />
				<table id="group_user_list">
					<tr>
						<td><h3>First Name</h3></td>
						<td><h3>Last Name</h3></td>
						<td><h3>Email</h3></td>
						<td><h3>Group</h3></td>
						<?php 
							
							//get the list of columns that should be shown
							$r = mysql_query("SELECT field_name FROM targets_metrics WHERE shown = 1 ORDER BY field_name ASC");
							while($ra = mysql_fetch_assoc($r))
								{
									echo "<td><h3>".$ra['field_name']."</h3></td>";
								}
						?>
						<td></td>
					</tr>
					<tr>
						<form action="target_upload_single.php" method="post" enctype="multipart/form-data">
							<td class="target_cell"><input type="text" name="fname" class="invisible_input" /></td>
							<td class="target_cell"><input type="text" name="lname" class="invisible_input" /></td>
							<td class="target_cell"><input type="text" name="email" class="invisible_input" /></td>
							<td class="target_cell"><input type="text" name="group_name_new" <?php if(isset($_REQUEST['g'])){echo "value=\"".filter_var($_REQUEST['g'], FILTER_SANITIZE_STRING)."\"";} ?> class="invisible_input" /></td>
							
							<?php
								//get the list of columns that should be shown
								$r = mysql_query("SELECT field_name FROM targets_metrics WHERE shown = 1 ORDER BY field_name ASC");
								while($ra = mysql_fetch_assoc($r))
									{
										echo "<td class=\"target_cell\"><input type=\"text\" name=\"".$ra['field_name']."\"class=\"invisible_input\"  /></td>";
									}
							?>
							<td class="submit_cell"><input type="image" src="../images/plus.png" alt="add" class="invisible_input" /></td>
							<input type="hidden" name="group_list" <?php if(isset($_REQUEST['g'])){echo "value=\"".filter_var($_REQUEST['g'], FILTER_SANITIZE_STRING)."\"";} ?> />
						</form>
					</tr>
					<?php

						//connect to database
						include "../spt_config/mysql_config.php";

						if(isset($_REQUEST['g']))
							{

								$group = filter_var($_REQUEST['g'], FILTER_SANITIZE_STRING);

								//ensure the group name is under 50 characters
								if(strlen($group) > 50)
									{
										$_SESSION['alert_message'] = "group names cannot be over 50 characters";
										header("location:./#alert");
										exit;
									}
									
								//ensure that the group name exists in the database
								$r = mysql_query("SELECT DISTINCT group_name FROM targets") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while ($ra = mysql_fetch_assoc($r))
									{
										if($ra['group_name'] == $group)
											{
												$match = 1;
											}
									}

								if(!isset($match))
									{
										$_SESSION['alert_message'] = "this group does not exist";
										header("location:./#alert");
										exit;
									}
													
								//query for a list of group members ordered alphabetically
								$r = mysql_query("SELECT * FROM targets WHERE group_name = '$group' ORDER BY fname") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while ($ra = mysql_fetch_assoc($r))
									{
										
										//build a row for each member of the group wrapped in a form that will dynamically edit each entry as changes are made
										echo 
											"
												<tr>\n
														<td class=\"target_cell\"><input id=\"".$ra['id']."_fname\" onchange=\"updateTarget(".$ra['id'].",'fname',this.value)\" type=\"text\" value=\"".$ra['fname']."\" class=\"invisible_input\"/></td>\n
														<td class=\"target_cell\"><input id=\"".$ra['id']."_lname\" onchange=\"updateTarget(".$ra['id'].",'lname',this.value)\" type=\"text\" value=\"".$ra['lname']."\" class=\"invisible_input\"/></td>\n
														<td class=\"target_cell\"><input id=\"".$ra['id']."_email\" onchange=\"updateTarget(".$ra['id'].",'email',this.value)\" type=\"text\" value=\"".$ra['email']."\" class=\"invisible_input\" /></td>\n
														<td class=\"target_cell\"><input id=\"".$ra['id']."_group\" onchange=\"updateTarget(".$ra['id'].",'group_name',this.value)\" type=\"text\" value=\"".$ra['group_name']."\" class=\"invisible_input\" /></td>\n";
														
										//get the list of columns that should be shown
										$r2 = mysql_query("SELECT field_name FROM targets_metrics WHERE shown = 1 ORDER BY field_name ASC");
										while($ra2 = mysql_fetch_assoc($r2))
											{
												$field_name = $ra2['field_name'];

												echo "
														<td class=\"target_cell\"><input id=\"".$ra['id']."_".$ra2['field_name']."\" onchange=\"updateTarget(".$ra['id'].",'".$ra2['field_name']."',this.value)\" type=\"text\" value=\"".$ra[$field_name]."\" class=\"invisible_input\" /></td>\n
													";
											}

										echo "
														<td><a href=\"target_delete.php?g=".$ra['group_name']."&u=".$ra['id']."\"><img src=\"../images/trash_sm.png\" alt=\"delete\" /></a></td>\n
												</tr>
											";
									}
							}
						else
							{
								//query for a list of group members ordered alphabetically
								$r = mysql_query("SELECT * FROM targets") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while ($ra = mysql_fetch_assoc($r))
									{
										//build a row for each member of the group wrapped in a form that will dynamically edit each entry as changes are made
										echo 
											"
												<tr>\n
														<td class=\"target_cell\"><input id=\"".$ra['id']."_fname\" onchange=\"updateTarget(".$ra['id'].",'fname',this.value)\" type=\"text\" value=\"".$ra['fname']."\" class=\"invisible_input\"/></td>\n
														<td class=\"target_cell\"><input id=\"".$ra['id']."_lname\" onchange=\"updateTarget(".$ra['id'].",'lname',this.value)\" type=\"text\" value=\"".$ra['lname']."\" class=\"invisible_input\"/></td>\n
														<td class=\"target_cell\"><input id=\"".$ra['id']."_email\" onchange=\"updateTarget(".$ra['id'].",'email',this.value)\" type=\"text\" value=\"".$ra['email']."\" class=\"invisible_input\" /></td>\n
														<td class=\"target_cell\"><input id=\"".$ra['id']."_group\" onchange=\"updateTarget(".$ra['id'].",'group_name',this.value)\" type=\"text\" value=\"".$ra['group_name']."\" class=\"invisible_input\" /></td>\n";
																																		
										//get the list of columns that should be shown
										$r2 = mysql_query("SELECT field_name FROM targets_metrics WHERE shown = 1 ORDER BY field_name ASC");
										while($ra2 = mysql_fetch_assoc($r2))
											{
												
												$field_name = $ra2['field_name'];

												echo
													"
														<td class=\"target_cell\"><input id=\"".$ra['id']."_".$ra2['field_name']."\" onchange=\"updateTarget(".$ra['id'].",'".$ra2['field_name']."',this.value)\" type=\"text\" value=\"".$ra[$field_name]."\" class=\"invisible_input\" /></td>\n
													";
											}

										echo "
														<td><a href=\"target_delete.php?g=".$ra['group_name']."&u=".$ra['id']."\"><img src=\"../images/trash_sm.png\" alt=\"delete\" /></a></td>\n
												</tr>
											";
									}
							}
						?>
				</table>
			</div>
		</div>
		<div id="wrapper">
			<!--sidebar-->
			<?php include '../includes/sidebar.php'; ?>					

			<!--content-->
			<div id="content">
				<span class="button"><a href="#add_one"><img src="../images/plus_sm.png" alt="add" /> One</a></span>
				<span class="button"><a href="#add_many"><img src="../images/plus_sm.png" alt="add" /> Many</a></span>
				<span class="button"><a href="#metrics"><img src="../images/list_sm.png" alt="metrics" /> Metrics</a></span>
				<span class="button"><a href="target_export.php"><img src="../images/list_sm.png" alt="template" /> Export</a></span>
				<table class="spt_table">
					<tr>
						<td><h3>Group Name</h3></td>
						<td><h3>Quantity</h3></td>
						<td><h3>Delete</h3></td>
					</tr>
					<tr>
						<td><a href="#group_list"><strong>All Targets</strong></a></td>
						<?php
							//connect to database
							include "../spt_config/mysql_config.php";

							//query for total count of targets
							$r = mysql_query("SELECT COUNT(id) AS target_count FROM targets");
							while($ra = mysql_fetch_assoc($r))
								{
									echo "<td>".$ra['target_count']."</td>";		
								}
						?>
						<td></td>
					</tr>
					<?php
						//connect to database
						include "../spt_config/mysql_config.php";
						
						//query for a list of groups ordered alphabetically
						$r = mysql_query("SELECT DISTINCT group_name FROM targets ORDER BY group_name") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
						while ($ra = mysql_fetch_assoc($r))
							{
								echo "<tr>";
								echo "<td><a href=\"?g=".$ra['group_name']."#group_list\">".$ra['group_name']."</a></td>";
								$group_name = $ra['group_name'];
								$r1 = mysql_query("SELECT COUNT(group_name) FROM targets WHERE group_name = '$group_name'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
								while($ra1 = mysql_fetch_assoc($r1))
									{
										echo "<td>".$ra1['COUNT(group_name)']."</td>";
									}
								echo "<td><a href=\"group_delete.php?g=".$ra['group_name']."\"><img src=\"../images/trash_sm.png\" alt=\"delete\" /></a></td>";
								echo "</tr>";
							}
					?>
				</table>
			</div>
		</div>	
	</body>
</html>
