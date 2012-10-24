<?php

/**
 * file:    logout.php
 * version: 4.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Login management
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license: GNU/GPL, see license.htm.
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
 * */

//this file is the global logout file for spt
//start the session
session_start ();

//destroy the session
session_unset ();
session_destroy ();

//start a new session
session_start ();

//set the logout notification
$_SESSION['alert_message'] = "you have successfully been logged out";

//send to the login screen
header ( 'location:../' );
?>
