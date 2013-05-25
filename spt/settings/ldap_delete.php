<?php

/**
 * file:    ldap_delete.php
 * version: 6.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Settings
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
// verify session is authenticated and not hijacked
$includeContent = "../includes/is_authenticated.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_authenticated.php' );
}

// verify user is an admin
$includeContent = "../includes/is_admin.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_admin.php' );
}

//check to see if something was posted
if($_GET['ldap']){
    //if value is yes
    if(isset($_GET['ldap'])){
        $ldap_id = $_GET['ldap'];
        //validate the ldap setting is constructed correctly
        if(preg_match('/[0-9]/',$ldap_id)){
            //connect to database
            include "../spt_config/mysql_config.php";
            mysql_query("DELETE FROM settings_ldap WHERE id='$ldap_id'");
        }else{
            $_SESSION['alert_message'] = "please only attempt to delete a valid ldap configuration.";
            header('location:.#tabs-3');
            exit;
        }
    }
}
header('location:.#tabs-3');
exit;
?>
