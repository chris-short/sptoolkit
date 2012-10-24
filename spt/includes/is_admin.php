<?php

/**
 * file:    is_admin.php
 * version: 7.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Core files
 * copyright:	Copyright (C) 2012 The SPT Project. All rights reserved.
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

//validate that the currently logged in user is an admin
if ( ! isset ( $_SESSION['admin'] ) OR $_SESSION['admin'] != 1 ) {
    $_SESSION['alert_message'] = "you do not have permission to perform the attempted action";
    header ( 'location:./#alert' );
    exit;
}
?>
