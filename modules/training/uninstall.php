<?php
/**
 * file:		uninstall.php
 * version:		1.0
 * package:		Simple Phishing Toolkit (spt)
 * component:	Training
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

  //This is the uninstall script for the training module

  //start session
  session_start();
  
  //check for authenticated session
  if($_SESSION['authenticated']!=1)
    {
      //for potential return
      $_SESSION['came_from']='training';
      
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

//make sure the user is an admin
  if($_SESSION['admin']!=1)
    {
      $_SESSION['training_alert_message'] = "you do not have permission to uninstall the training module";
      header('location:#alert');
      exit;
    }
  
//Campaigns Table
  $sql = "ALTER TABLE `campaigns` DROP `training_id`";
  
  mysql_query($sql) or die(mysql_error());
  
?>