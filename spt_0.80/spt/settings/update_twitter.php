<?php

/**
 * file:    update_twitter.php
 * version: 2.0
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
if($_POST){
    //if value is yes
    if(isset($_POST['twitter_enable']) && $_POST['twitter_enable'] == "yes" ){
        $twitter_value = 1;
    }else{
        $twitter_value = 0;
    }
    //connect to database
    include "../spt_config/mysql_config.php";
    //update value in database
    mysql_query("UPDATE settings SET value = '$twitter_value' WHERE setting = 'twitter_enable'");
    //set alert message and send them back
    $_SESSION['alert_message'] = "Your twitter preference has been updated!";
    header ( 'location:./#alert' );
    exit;

}
?>
