<?php

/**
 * file:    index.php
 * version: 14.0
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
        <link rel="stylesheet" href="../includes/jquery-ui.css" type="text/css" />
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_quickstart.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="../includes/escape.js"></script>
        <script src="../includes/jquery.min.js"></script>
        <script src="../includes/jquery-ui.min.js"></script>
        <script>
            $(function() {
                $( "#tabs" ).tabs();
            });
        </script>
    </head>
    <body>
    <body>
        <!--alert-->
        <?php include '../includes/alert.php'; ?>                 
        <div id="wrapper">
            <!--sidebar-->
            <?php include '../includes/sidebar.php'; ?>                 
            <!--content-->
            <!--content-->
            <div id="content">
                <br />
                <div id="tabs">
                    <ul>
                        <li><a href="#tabs-1">Introduction</a></li>
                        <li><a href="#tabs-2">Step 1</a></li>
                        <li><a href="#tabs-3">Step 2</a></li>
                        <li><a href="#tabs-4">Step 3</a></li>
                        <li><a href="#tabs-5">Step 4</a></li>
                    </ul>
                    <div id="tabs-1">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <strong>You can easily get started with the spt in one of the following ways:</strong>
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="step">1</td>
                                <td>To perform a quick phishing campaign test with your email address:  Skip to <strong>Step 4</strong> and start a campaign immediately using the included quick start templates [QST] and quick start education [QSE].  Select the <strong>Admins - Test</strong> target group that automatically includes your email address (based on your entry during spt installation).<br /><br /><strong>Or...</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="step">2</td>
                                <td>To perform a quick phishing campaign against other targets:  Complete <strong>Step 1</strong> and then skip to <strong>Step 4</strong> and start a campaign immediately using the included quick start templates [QST] and quick start education [QSE].  Select one or more of the target groups created in <strong>Step 1</strong>.<br /><br /><strong>Or...</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="step">3</td>
                                <td>Work your way through each step starting with <strong>Step 1</strong> for a completely customized phishing campaign.</td>
                            </tr>
                        </table>
                    </div>
                    <div id="tabs-2">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <strong>Targets Module:  Configure metrics and add targets</strong>
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td class="step"><strong>Step</strong></td>
                                <td class="do_this"><strong>Do this</strong></td>
                                <td class="click_this"><strong>Click this</strong></td>
                            </tr>
                            <tr>
                                <td>1.</td>
                                <td>Create or edit your metrics (custom attributes).</td>
                                <td><a href="../targets/#metrics"><img src="../images/table_edit_sm.png" alt="metrics" /> Metrics</a></td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Create single targets, <strong>or</strong>,</td>
                                <td><a href="../targets/#add_one"><img src="../images/user_add_sm.png" alt="add" /> One</a></td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>
                                    First, use the export to CSV function to download an editable CSV file.<br /><br />
                                    Next, import the edited file to add many targets at once.
                                </td>
                                <td>
                                    <a href="../targets/target_export.php"><img src="../images/page_white_put_sm.png" alt="template" /> Export</a><br /><br />
                                    <a href="../targets/#add_many"><img src="../images/group_add_sm.png" alt="add" /> Import</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <br /><br />
                                    <strong>NOTE:</strong>  You will not be returned to the Quick Start after closing the page you visit by clicking any links here.
                                </td>
                            </tr>
                        </table>    
                    </div>
                    <div id="tabs-3">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <strong>Templates Module:  Upload, copy or edit templates or scrape web sites (Optional)</strong>
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td class="step"><strong>Step</strong></td>
                                <td class="do_this"><strong>Do this</strong></td>
                                <td class="click_this"><strong>Click this</strong></td>
                            </tr>
                            <tr>
                                <td>1.</td>
                                <td>Upload a new template, <strong>or</strong>,</td>
                                <td><a href="../templates/#add_template"><img src="../images/package_add_sm.png" alt="add" /> Template</a></td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Copy an existing template and then edit to customize it, <strong>or</strong>,</td>
                                <td><a href="../templates"><img src="../images/page_copy_sm.png" alt="copy" /></a> then <a href="../templates"><img src="../images/pencil_sm.png" alt="edit" /></a></td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Edit an existing template to customize it without copying it first, <strong>or</strong>,</td>
                                <td><a href="../templates"><img src="../images/pencil_sm.png" alt="edit" /></a></td>
                            </tr>
                            <tr>
                                <td>4.</td>
                                <td>Scrape a live web site.</td>
                                <td><a href="../templates/#add_scrape"><img src="../images/world_add_sm.png" alt="add" /> Scrape</a></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <br /><br />
                                    <strong>NOTE:</strong>  You will not be returned to the Quick Start after closing the page you visit by clicking any links here.
                                </td>
                            </tr>
                        </table>                    
                    </div>
                    <div id="tabs-4">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <strong>Education Module:  Upload, copy or edit education packages (Optional)</strong>
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td class="step"><strong>Step</strong></td>
                                <td class="do_this"><strong>Do this</strong></td>
                                <td class="click_this"><strong>Click this</strong></td>
                            </tr>
                            <tr>
                                <td>1.</td>
                                <td>Upload a new education package, <strong>or</strong>,</td>
                                <td><a href="../education/#add_package"><img src="../images/package_add_sm.png" alt="add" /> Package</a></td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Copy an existing education package and then edit to customize it, <strong>or</strong>,</td>
                                <td><a href="../education"><img src="../images/page_copy_sm.png" alt="copy" /></a> then <a href="../education"><img src="../images/pencil_sm.png" alt="edit" /></a></td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Edit an existing education package to customize it without copying it first, <strong>or</strong>,</td>
                                <td><a href="../education"><img src="../images/pencil_sm.png" alt="edit" /></a></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <br /><br />
                                    <strong>NOTE:</strong>  You will not be returned to the Quick Start after closing the page you visit by clicking any links here.
                                </td>
                            </tr>
                        </table>                    
                    </div>
                    <div id="tabs-5">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <strong>Campaigns Module:  Start and monitor a campaign</strong>
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td class="step"><strong>Step</strong></td>
                                <td class="do_this"><strong>Do this</strong></td>
                                <td class="click_this"><strong>Click this</strong></td>
                            </tr>
                            <tr>
                                <td>1.</td>
                                <td>Optionally, enter your URL shortener API key.</td>
                                <td><a href="../campaigns/#shorten"><img src="../images/cog_edit_sm.png" alt="config_shorten" /> Shorten</a></td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Start a new campaign.</td>
                                <td><a href="../campaigns/#add_campaign"><img src="../images/email_to_friend_sm.png" alt="add" /> Campaign</a></td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>Optionally, export campaign statistics as a CSV file.</td>
                                <td><a href="../campaigns/campaigns_export.php"><img src="../images/page_white_put_sm.png" alt="export" /> Export</a></td>
                            </tr>
                            <tr>
                                <td>4.</td>
                                <td>Review campaign statistics from the dashboard.</td>
                                <td><a href="../#phish_pie"><img src="../images/house_sm.png" alt="review" /></a></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <br /><br />
                                    <strong>NOTE:</strong>You will not be returned to the Quick Start after closing the page you visit by clicking any links here.
                                </td>
                            </tr>
                        </table>                                             
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>