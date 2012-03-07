<?php

/**
 * file:    404_is_authenticated.php
 * version: 3.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Global Errors
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

?>
<!DOCTYPE HTML> 
<html>
    <head>
        <title>spt - 404 - dang!</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />		
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />	
        <!--css-->
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
    </head>
    <body>
        <div id="wrapper">
            <!--sidebar-->
<?php include '../includes/sidebar.php'; ?>					

            <!--content-->
            <div id="content">
                <span><br /><br /><strong>Dang!  That is a 404 on authentication!</strong><br/><br/>Could not find the '/includes/is_authenticated.php' file for inclusion.  Please check its existence.</span>
            </div>
        </div>	
    </body>
</html>
