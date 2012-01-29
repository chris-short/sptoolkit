<?php
/**
 * file:		target_upload_batch.php
 * version:		12.0
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

	//determine what the custom field names are
	$r = mysql_query("SELECT * FROM targets");
	$custom1 = mysql_field_name($r,4);
	$custom2 = mysql_field_name($r,5);
	$custom3 = mysql_field_name($r,6);

	$header_line = explode(',',$lines[0]);

	if(strtolower($header_line[0]) != "name" OR strtolower($header_line[1]) != "email" OR strtolower($header_line[2]) != "group" OR strtolower($header_line[3]) != strtolower($custom1) OR strtolower($header_line[4]) != strtolower($custom2) OR strtolower(trim($header_line[5])) != strtolower($custom3))
		{
			$_SESSION['alert_message'] = "the header row does not match the column names in the database";
       	    header('location:./#alert');
            exit;
		}


//ensure there are between 3 and 6 columns
foreach($lines as $line)
	{
		//separate each line into an array based on the comma delimiter
		$line_contents = explode(',',$line);

		//ensure there are no more than 6 columns
		if(isset($line_contents[6]))
		    {
			$_SESSION['alert_message'] = "you have too many columns, 6 columns is the max";
			header('location:./#alert');
			exit;
		    }

		//ensure there are at least three columns
		if(!isset($line_contents[0]) || !isset($line_contents[1]) || !isset($line_contents[2]))
		    {
			$_SESSION['alert_message'] = "you do not have at least three columns in all rows";
			header('location:./#alert');
			exit;
		    }
	}

//validate each column of data and if all columns validate write the entire line to the database
foreach($lines as $line)
	{
		//separate each line into an array based on the comma delimiter
		$line_contents = explode(',',$line);
		
		//leave out the header row
		if(strtolower($line_contents[0]) != "name" && strtolower($line_contents[1]) != "email" && strtolower($line_contents[2]) != "group" && strtolower($line_contents[3]) != strtolower($custom1) && strtolower($line_contents[4]) != strtolower($custom2) && strtolower(trim($line_contents[5])) != strtolower($custom3))
			{
				//filter name
				$temp_name = filter_var(trim($line_contents[0]), FILTER_SANITIZE_STRING);

				//validate email
				if(filter_var(trim($line_contents[1]), FILTER_VALIDATE_EMAIL))
					{
						$temp_email = trim($line_contents[1]);
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
						//set custom values
						$custom1_value = $line_contents[3];
						$custom2_value = $line_contents[4];
						$custom3_value = $line_contents[5];


						//connect to database
						include "../spt_config/mysql_config.php";

						//insert data
						mysql_query("INSERT INTO targets (name, email, group_name, `$custom1`, `$custom2`, `$custom3`) VALUES ('$temp_name','$temp_email','$temp_group', '$custom1_value', '$custom2_value', '$custom3_value')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

						//increment counter
						$counter++;

					}
			
				$counter_total++;
				
				//set temp counters back to 0
				$temp_counter_bad_name = 0;
				$temp_counter_bad_emails = 0;
			}
		
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