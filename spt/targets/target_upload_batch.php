<?php
/**
 * file:		target_upload_batch.php
 * version:		13.0
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

	// verify user is an admin
	$includeContent = "../includes/is_admin.php";
	if(file_exists($includeContent)){
		require_once $includeContent;
	}else{
		header('location:../errors/404_is_admin.php');
	}

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
			$_SESSION['alert_message'] = "you either did not select a file or there was a problem with it";
			header('location:./#alert');
			exit;
		}

//set ini to detect line endings to prepare for csv import
	ini_set("auto_detect_line_endings", true);

//pull in file
	$lines = file($_FILES["file"]["tmp_name"]);

//initialize counters
	$counter = 0;
	$counter_total = 0;
	$counter_bad_name = 0;
	$counter_bad_emails = 0;

	$temp_counter_bad_name = 0;
	$temp_counter_bad_emails = 0;

//ensure there is a comma in every line
	foreach($lines as $line)
	    {
	        
	        if(!preg_match('/[,]/',$line))
	            {
	                $_SESSION['alert_message'] = "this file is not properly comma delimited";
	       	    	header('location:./#alert');
	                exit;

	            }
	    }

//ensure the header exists and is in the right order
	//connect to database
	include "../spt_config/mysql_config.php";

	$header_line = explode(',',$lines[0]);

	//set counter
	$c = 0;
	$c2 = 0;

	//get target columns
	$r = mysql_query("SHOW COLUMNS FROM targets");
	while($ra = mysql_fetch_assoc($r))
		{
			if($c2 > 0)
				{
					if(strtolower($ra['Field']) != trim(strtolower($header_line[$c])))
						{
							$_SESSION['alert_message'] = "the header row does not match the column names in the database".$ra['Field']."|".$header_line[$c];
				       	    header('location:./#alert');
				            exit;
						}	
						$c++;
				}
			$c2++;
		}

//get number of fields
$field_count = mysql_num_fields($r);

//ensure there are at least the three required fields
	foreach($lines as $line)
		{
			//separate each line into an array based on the comma delimiter
			$line_contents = explode(',',$line);

			//ensure there are at least three columns
			if(!isset($line_contents[0]) || !isset($line_contents[1]) || !isset($line_contents[2]))
			    {
					$_SESSION['alert_message'] = "you do not have at least the first three required columns";
					header('location:./#alert');
					exit;
			    }
		}

//set counter
	$c = 0;

//validate each column of data and if all columns validate write the entire line to the database
	foreach($lines as $line)
		{
			
			if($c > 0)
				{
					//separate each line into an array based on the comma delimiter
					$line_contents = explode(',',$line);
					
					//filter name
					$temp_name = filter_var(trim($line_contents[1]), FILTER_SANITIZE_STRING);

					//validate email
					if(filter_var(trim($line_contents[0]), FILTER_VALIDATE_EMAIL))
						{
							$temp_email = trim($line_contents[0]);
						}
					else
						{
							//increment bad email counter
							$temp_counter_bad_emails++;
						}
							
					//set the group name
					$temp_group = filter_var(trim($line_contents[2]), FILTER_SANITIZE_STRING);	
						
					//if there are any errors increment counters, otherwise write values to database
					if($temp_counter_bad_name == 1 || $temp_counter_bad_emails == 1)
						{
							$counter_bad_name = $temp_counter_bad_name + $counter_bad_name;
							$counter_bad_emails = $temp_counter_bad_emails + $counter_bad_emails;
						}

					else
						{									
							//connect to database
							include "../spt_config/mysql_config.php";

							//insert data
							mysql_query("INSERT INTO targets (name, email, group_name) VALUES ('$temp_name','$temp_email','$temp_group')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

							$r = mysql_query("SHOW COLUMNS FROM targets");
							while($ra = mysql_fetch_row($r))
								{
									echo "field count".$field_count."<br />";

									print_r($ra);
									$value = $line_contents[$field_count];

									echo "column".$column."<br />";

									echo "value".$value."<br />";

									mysql_query("UPDATE targets SET $column = '$value' WHERE email = '$temp_email'");

									$field_count--;

								}
							

							exit;

							//increment counter
							$counter++;
					
							$counter_total++;
							
							//set temp counters back to 0
							$temp_counter_bad_name = 0;
							$temp_counter_bad_emails = 0;
						}
				}
			
			//increment counter
			$c++;
		}


//send stats back if there were bad rows
	if($counter_bad_name > 0)
		{
			$_SESSION["bad_row_stats"][] = $counter_bad_name." rows excluded due to names with bad values";
		}
	if($counter_bad_emails > 0)
		{
			$_SESSION["bad_row_stats"][] = $counter_bad_emails." rows excluded due to bad email addresses";
		}
			
//send user back to targets page with success message
	$_SESSION['alert_message'] = $counter." of ".$counter_total." targets uploaded successfully";
	header('location:./#alert');
	exit;
?>