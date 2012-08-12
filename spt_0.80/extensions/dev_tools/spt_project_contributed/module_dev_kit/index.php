<?php
/**
 * file:    index.php
 * version: 5.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Module Template
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

// verify session is authenticated and not hijacked
$includeContent = "../includes/is_authenticated.php";
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_authenticated.php' );
}
?>

<!DOCTYPE HTML> 
<html>
    <head>
        <title>spt - module template</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_module_template.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <!--popovers-->
<?php
//check to see if the alert session is set
if ( isset ( $_SESSION['alert_message'] ) ) {
    //create alert popover
    echo "<div id=\"alert\">";

    //echo the alert message
    echo "<div>" . $_SESSION['alert_message'] . "<br /><br /><a href=\"\"><img src=\"../images/accept.png\" alt=\"close\" /></a></div>";

    //unset the seession
    unset ( $_SESSION['alert_message'] );

    //close alert popover
    echo "</div>";
}
?>
            <!--sidebar-->
            <?php include '../includes/sidebar.php'; ?>					

            <!--content-->
            <div id="content">
                <span>Your module's content would go here.</span>
            </div>
        </div>	
    </body>
</html>
