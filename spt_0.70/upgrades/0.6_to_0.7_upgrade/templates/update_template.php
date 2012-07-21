<?php

/**
 * file:    update_template.php
 * version: 1.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Template management
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

//set values
$name = filter_var ( $_POST['name'], FILTER_SANITIZE_STRING );
$description = filter_var ( $_POST['description'], FILTER_SANITIZE_STRING );
if(!filter_var($_POST['tempid'], FILTER_VALIDATE_INT)){
    $_SESSION['alert_message'] = 'please provide a valid template id';
    header('location:./#alert');
    exit;
}
$template_id = $_POST['tempid'];

//validate the template id is valid
//connect to database
include "../spt_config/mysql_config.php";
//query database for existing templates
$sql = "SELECT id FROM templates";
$r = mysql_query($sql);
$match = 0;
while($ra = mysql_fetch_assoc($r)){
    if($ra['id'] == $template_id){
        $match = 1;
    }
}
//if template id provided doesn't match existing id, throw alert
if($match == 0){
    $_SESSION['alert_message'] = 'this template does not exist';
    header ( 'location:./#alert' );
    exit;
}

if ( ! empty ( $name ) ) {
    $_SESSION['temp_template_name'] = $name;
}
if ( ! empty ( $description ) ) {
    $_SESSION['temp_template_description'] = $description;
}

//validate that a name is provided
if ( strlen ( $_POST['name'] ) < 1 ) {
    $_SESSION['alert_message'] = 'you must enter a name';
    header ( 'location:./?id='.$template_id.'#update_template' );
    exit;
}

//validate that a description is provided
if ( ! isset ( $_POST['description'] ) ) {
    $_SESSION['alert_message'] = 'you must enter a description';
    header ( 'location:./?id='.$template_id.'#update_template' );
    exit;
}

//update template details
include "../spt_config/mysql_config.php";
mysql_query ( "UPDATE templates SET name = '$name', description = '$description' WHERE id = '$template_id'" ) or die ( '<div id="die_error">There is a problem with the database...please try again later</div>' );

$_SESSION['alert_message'] = 'template updated successfully';
header ( 'location:./#alert' );
exit;
?>