<?php

/**
 * file:    file_update.php
 * version: 8.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Editor
 * copyright:   Copyright (C) 2011 The SPT Project. All rights reserved.
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

//validate template id OR package id is set and filename is set
if ( ! isset ( $_REQUEST['f'] ) ) {
    $_SESSION['alert_message'] = "Please specify a file.";
    header ( 'location:./#alert' );
    exit;
}
if ( ! isset ( $_REQUEST['t'] ) && ! isset ( $_REQUEST['p'] ) ) {
    $_SESSION['alert_message'] = "Please specify a package or template id.";
    header ( 'location:./#alert' );
    exit;
}

//validate the template id and put the data
if ( isset ( $_REQUEST['t'] ) ) {
    if ( ! filter_var ( $_REQUEST['t'], FILTER_VALIDATE_INT ) ) {
        $_SESSION['alert_message'] = "Please select a valid template.";
        header ( 'location:./#alert' );
        exit;
    }

    $template = $_REQUEST['t'];
    $file = $_REQUEST['f'];
    $changes = $_POST['file'];

    if ( isset ( $template ) ) {
        file_put_contents ( "../templates/" . $template . "/" . $file, $changes );
    }
}

//validate the package id and put the data
if ( isset ( $_REQUEST['p'] ) ) {
    if ( ! filter_var ( $_REQUEST['p'], FILTER_VALIDATE_INT ) ) {
        $_SESSION['alert_message'] = "Please select a valid package.";
        header ( 'location:./#alert' );
        exit;
    }

    $package = $_REQUEST['p'];
    $file = $_REQUEST['f'];
    $changes = $_POST['file'];

    if ( isset ( $package ) ) {
        file_put_contents ( "../education/" . $package . "/" . $file, $changes );
    }
}


$_SESSION['alert_message'] = "Your changes have been saved";
header ( 'location:./#alert' );
exit;
?>