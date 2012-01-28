<?php
/**
 * file:		other.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Module Template
 * copyright:	Copyright (C) 2012 The SPT Project. All rights reserved.
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
		$_SESSION['module_alert_message'] = "you do not have permission to [what do they not have permission to do?]";
		header('location:../module/#alert');
		exit;
	}


//do some action


//send them back to the module home page
$_SESSION['module_alert_message'] = "[some action] successfully";
header('location:../module/#alert');
exit;

?>
