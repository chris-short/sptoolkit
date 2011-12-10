<?php
/**
 * file:		delete_package.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Education
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
			$_SESSION['came_from']='education';
			
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

//validate that the currently logged in user is an admin
if($_SESSION['admin']!=1)
	{
		$_SESSION['education_alert_message'] = "you do not have permission to delete a package";
		header('location:../education/#alert');
		exit;
	}

//get education id
$education_id = $_REQUEST['t'];


//validate the education id
include "../spt_config/mysql_config.php";
$r = mysql_query("SELECT id FROM education") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra = mysql_fetch_assoc($r))
	{
		if($ra['id']==$education_id)
			{
				$match = 1;
			}
	}
if($match!= 1)
	{
		$_SESSION['education_alert_message'] = "you specified an invalid package";
		header('location:../education/#alert');
		exit;
	}

//verify this education package is not used in an existing campaign
$r = mysql_query("SELECT DISTINCT education_id FROM campaigns") or die('<div id="die_error">There is a problem with the database...please try again later</div>');
while($ra = mysql_fetch_assoc($r))
	{
		if($ra['education_id']==$education_id)
			{
				$match2 = 1;
			}
	}
if($match2 == 1)
	{
		$_SESSION['education_alert_message'] = "you cannot delete a package that is currently used by a campaign";
		header('location:../education/#alert');
		exit;
	}

//delete the education directory from the filesystem
$dir = $education_id;

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

//delete the education from the database
mysql_query("DELETE FROM education WHERE id = '$education_id'") or die('<div id="die_error">There is a problem with the database...please try again later</div>');

//send them back to the education home page
$_SESSION['education_alert_message'] = "education package deleted successfully";
header('location:../education/#alert');
exit;

?>
