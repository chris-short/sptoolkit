<?php

/**
 * file:    index.php
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Quick Start 
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
        <title>spt - quick start</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_quickstart.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
    </head>

    <body>
        <div id="wrapper">

            <!--sidebar-->
<?php include '../includes/sidebar.php'; ?>					

            <!--content-->
            <div id="content">
                <table class="spt_qs_table">
                    <tr>
                        <td>
                            <h3>Quick Start guide to using the spt</h3><br /><br />
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 100%;" colspan="3">
                                        <strong>Step one:  Configure metrics and add targets</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%;"><strong>Step</strong></td>
                                    <td style="width: 80%;"><strong>Do this</strong></td>
                                    <td style="width: 15%;"><strong>Click this</strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">1.</td>
                                    <td style="width: 80%;">Create or edit your metrics (custom attributes).</td>
                                    <td style="width: 15%;"><img src="images/qs_1_1.png" alt="Edit metrics"></td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">2.</td>
                                    <td style="width: 80%;">Create single targets, or,</td>
                                    <td style="width: 15%;"><img src="images/qs_1_2.png" alt="Add one target"></td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">3.</td>
                                    <td style="width: 80%; vertical-align: top;"> First, use the export to CSV function to download an editable CSV file.<br />
                                        Next, import the edited file to add many targets at once.</td>
                                    <td style="width: 15%;">
                                        <img src="images/qs_1_3a.png" alt="Export CSV"><br />
                                        <img src="images/qs_1_3b.png" alt="Import CSV">
                                    </td>
                                </tr>
                            </table>
                            <br /><br />
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 100%;" colspan="3">
                                        <strong>Step two:  Upload templates or scrape live sites</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%;"><strong>Step</strong></td>
                                    <td style="width: 80%;"><strong>Do this</strong></td>
                                    <td style="width: 15%;"><strong>Click this</strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">1.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">2.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">3.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                            </table>
                            <br /><br />
                            <table style="width: 100%;">
                            <tr>
                                <td style="width: 100%;" colspan="3">
                                    <strong>Step three:  Upload education packages</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 5%;"><strong>Step</strong></td>
                                <td style="width: 80%;"><strong>Do this</strong></td>
                                <td style="width: 15%;"><strong>Click this</strong></td>
                            </tr>
                            <tr>
                                <td style="width: 5%; vertical-align: top;">1.</td>
                                <td style="width: 80%;">meh</td>
                                <td style="width: 15%;">Click this</td>
                            </tr>
                            <tr>
                                <td style="width: 5%; vertical-align: top;">2.</td>
                                <td style="width: 80%;">meh</td>
                                <td style="width: 15%;">Click this</td>
                            </tr>
                            <tr>
                                <td style="width: 5%; vertical-align: top;">3.</td>
                                <td style="width: 80%;">meh</td>
                                <td style="width: 15%;">Click this</td>
                            </tr>
                        </table>
                            <br /><br />
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 100%;" colspan="3">
                                        <strong>Step four:  Edit templates or education packages as needed to customize them</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%;"><strong>Step</strong></td>
                                    <td style="width: 80%;"><strong>Do this</strong></td>
                                    <td style="width: 15%;"><strong>Click this</strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">1.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">2.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">3.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                            </table>
                            <br /><br />
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 100%;" colspan="3">
                                        <strong>Step five:  Start and monitor a campaign</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%;"><strong>Step</strong></td>
                                    <td style="width: 80%;"><strong>Do this</strong></td>
                                    <td style="width: 15%;"><strong>Click this</strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">1.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">2.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">3.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                            </table>
                            <br /><br />
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 100%;" colspan="3">
                                        <strong>Administrative tasks:  Manage users</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%;"><strong>Step</strong></td>
                                    <td style="width: 80%;"><strong>Do this</strong></td>
                                    <td style="width: 15%;"><strong>Click this</strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">1.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">2.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">3.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                            </table>  
                            <br /><br />
                            <table style="width: 100%;">
                                <tr>
                                    <td style="width: 100%;" colspan="3">
                                        <strong>Administrative tasks:  Manage modules</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 5%;"><strong>Step</strong></td>
                                    <td style="width: 80%;"><strong>Do this</strong></td>
                                    <td style="width: 15%;"><strong>Click this</strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">1.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">2.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                                <tr>
                                    <td style="width: 5%; vertical-align: top;">3.</td>
                                    <td style="width: 80%;">meh</td>
                                    <td style="width: 15%;">Click this</td>
                                </tr>
                            </table>                          
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>