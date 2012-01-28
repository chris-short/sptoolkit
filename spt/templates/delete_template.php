<?php
/**
 * file:		delete_template.php
 * version:		3.0
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

	// verify session is authenticated and not hijacked
	$includeContent = "../includes/is_authenticated.php";
	if(file_exists($includeContent)){
		require_once $includeContent;
	}else{
		header('location:../errors/404_is_authenticated.php');
	}

//validate that the currently logged in user is an admin
if($_SESSION['admin']!=1)
	{
		$_SESSION['templates_alert_message'] = "you do not have permission to delete a template";
		header('location:../templates/#alert');
		exit;
	}

//get template id
$template_id = $_REQUEST['t'];


//validate the template id
include "../spt_config/mysql_config.php";
$r = mysql_query("SELECT id FROM templates") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra = mysql_fetch_assoc($r))
	{
		if($ra['id']==$template_id)
			{
				$match = 1;
			}
	}
if(!isset($match))
	{
		$_SESSION['templates_alert_message'] = "you specified an invalid template";
		header('location:../templates/#alert');
		exit;
	}

//verify this template is not used in an existing campaign
$r = mysql_query("SELECT DISTINCT template_id FROM campaigns") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra = mysql_fetch_assoc($r))
	{
		if($ra['template_id']==$template_id)
			{
				$match2 = 1;
			}
	}
if(!isset($match2))
	{
		$_SESSION['templates_alert_message'] = "you cannot delete a template that is currently used by a campaign";
		header('location:../templates/#alert');
		exit;
	}

//delete the template directory from the filesystem
$dir = $template_id;

function delTree($dir) { 
    $files = glob( $dir . '*', GLOB_MARK ); 
    foreach( $files as $file ){ 
        if( substr( $file, -1 ) == '/' ) 
            delTree( $file ); 
        else 
            unlink( $file ); 
    } 
    rmdir( $dir ); 
}

delTree($dir);

//delete the template from the database
mysql_query("DELETE FROM templates WHERE id = '$template_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

//send them back to the template home page
$_SESSION['templates_alert_message'] = "template deleted successfully";
header('location:../templates/#alert');
exit;

?>
