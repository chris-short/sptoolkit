<?php

/**
 * file:    delete_smtp.php
 * version: 5.0
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
if($_GET['smtp']){
    //if value is yes
    if(isset($_GET['smtp'])){
        $smtp_setting = $_GET['smtp'];
        //validate the smtp setting is constructed correctly
        if(preg_match('/[0-9]/', $smtp_setting)){
            //connect to database
            include "../spt_config/mysql_config.php";
            //ensure this smtp relay is not a part of an existing or scheduled campaign
            $r = mysql_query("SELECT relay_host FROM campaigns_responses");
            while ($ra = mysql_fetch_assoc($r)){
                if($smtp_setting == $ra['relay_host']){
                    $_SESSION['alert_message'] = "this SMTP server cannot be deleted because it is associated with a campaign";
                    header('location:.#tabs-2');
                    exit;
                }
            }
            //delete the smtp server
            mysql_query("DELETE FROM settings_smtp WHERE id='$smtp_setting'");
        }else{
            $_SESSION['alert_message'] = "nothing was deleted";
            header('location:.#tabs-2');
            exit;
        }
    }
}
$_SESSION['alert_message'] = "smtp server deleted";
header('location:.#tabs-2');
exit;
?>
