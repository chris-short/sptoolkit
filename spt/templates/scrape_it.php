<?php
/**
 * file:		scrape_it.php
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

//get URL from passed parameter
	if(isset($_POST['url']))
		{
			$url = $_POST['url'];
		}
	else
		{
			//set error message and send them back to template page
			$_SESSION['templates_alert_message']="please enter a URL";
			header('location:../templates/#');
			exit;
		}

//get name from passed parameter
	if(isset($_POST['name']))
		{
			$name = $_POST['name'];
		}
	else
		{
			//set error message and send them back to template page
			$_SESSION['templates_alert_message']="please enter a name";
			header('location:../templates/#');
			exit;
		}

//get description from passed parameter
	if(isset($_POST['description']))
		{
			$description = $_POST['description'];
		}
	else
		{
			//set error message and send them back to template page
			$_SESSION['templates_alert_message']="please enter a description";
			header('location:../templates#');
			exit;
		}

//function to get data from URL 
	function get_url_contents($url, $timeout = 10, $userAgent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.215 Safari/534.10'){
	    $rawhtml = curl_init();
	    curl_setopt ($rawhtml, CURLOPT_URL,$url);
	    curl_setopt ($rawhtml, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt ($rawhtml, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt ($rawhtml, CURLOPT_CONNECTTIMEOUT, $timeout);
	    curl_setopt ($rawhtml, CURLOPT_USERAGENT, $userAgent);
	    $output = curl_exec($rawhtml);
	    curl_close($rawhtml);
	    if (!$output) 
		    {
				echo "Nothing was returned.  Try verifying the URL you provided.";
				exit;
			}
	    return $output;
	}
 
//get passed URL and turn that URL into raw html
	$html_string = get_url_contents($url);

	$file = "temp_upload/index.htm";
	$working = fopen($file, 'w') or die("can't open file");
	fwrite($working, $html_string);
	fclose($working);

//prepare URL for parsing links
$parsed_url = parse_url($url);
$url = $parsed_url['scheme']."://".$parsed_url['host'];

//find and replace function
	function f_and_r($find, $replace, $path)
		{
			$globarray = glob($path);
			if ($globarray) foreach ($globarray as $filename) 
				{
				  $source = file_get_contents($filename);
				  $source = preg_replace($find,$replace,$source);
				  file_put_contents($filename,$source);
				}
		}

	//find and replace relative links 	
	f_and_r('#(href|src)="([^/][^/])([^:|\#"]*)(?:")#', '$1="'.$url.'/$2"', 'temp_upload/index.htm');
	//f_and_r('#(href|src)="([^http])#', '$1="'.$url.'/', 'temp_upload/index.htm');

	//fix double relative, absolute paths
	f_and_r('#href="([/][/])#', 'href="http://','temp_upload/index.htm');
	f_and_r('#src="([/][/])#', 'src="http://','temp_upload/index.htm');

	//fix inline css url links
	f_and_r('#url\(//#', 'url(http://', 'temp_upload/index.htm');

	//replace post destination to spt
	f_and_r('#action="(.*?)"#', 'action="../../campaigns/response.php"', 'temp_upload/index.htm');

//connect to database
include('../spt_config/mysql_config.php');

//figure out what the next template id is
$r = mysql_query("SELECT MAX(id) AS max FROM templates") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra = mysql_fetch_assoc($r))
	{
		$max_id = $ra['max'];
		++$max_id;
		$template_id = $max_id;
	}

//create a directory for the new template
mkdir($template_id);

//copy scraped file into new template directory
copy("temp_upload/index.htm", $template_id."/index.htm");

//copy default email and return files into new template directory
copy("temp_upload/return.htm", $template_id."/return.htm");
copy("temp_upload/email.php", $template_id."/email.php");
copy("temp_upload/screenshot.png", $template_id."/screenshot.png");

//add information to the database
mysql_query("INSERT INTO templates (id, name, description) VALUES ('$template_id','$name','$description')") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

//send them back to template page with a success message
$_SESSION['templates_alert_message']="Template installed successfully!";
header('location:../templates');
exit;


?>
 