<?php

/**
 * file:    index.php
 * version: 9.0
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
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
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
                <article class="tabs">
                    <section id="intro">
                        <a class="navigation" href="#intro"><span>Introduction</span></a>
                        <table>
                            <tr>
                                <td colspan="3">
                                    <strong>You can easily get started with the spt in one of the following ways:</strong>
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td class="step">1</td>
                                <td>To perform a quick phishing campaign test with your email address:  Skip to <strong>Step 4</strong> and start a campaign immediately using the included quick start templates [QST] and quick start education [QSE].  Select the <strong>Admins - Test</strong> target group that automatically includes your email address (based on your entry during spt installation).<br /><br /><strong>Or...</strong></td>
                            </tr>
                            <tr>
                                <td class="step">2</td>
                                <td>To perform a quick phishing campaign against other targets:  Complete <strong>Step 1</strong> and then skip to <strong>Step 4</strong> and start a campaign immediately using the included quick start templates [QST] and quick start education [QSE].  Select one or more of the target groups created in <strong>Step 1</strong>.<br /><br /><strong>Or...</strong></td>
                            </tr>
                            <tr>
                                <td class="step">3</td>
                                <td>Work your way through each step starting with <strong>Step 1</strong> for a completely customized phishing campaign.</td>
                            </tr>
                        </table>
                    </section>
                    <section id="step1">
                        <a class="navigation" href="#step1"><span>Step 1</span></a>
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
                                    <td><img src="../images/qs_1_1.png" alt="Edit metrics"></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Create single targets, <strong>or</strong>,</td>
                                    <td><img src="../images/qs_1_2.png" alt="Add one target"></td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>
                                        First, use the export to CSV function to download an editable CSV file.<br /><br />
                                        Next, import the edited file to add many targets at once.
                                    </td>
                                    <td>
                                        <img src="../images/qs_1_3a.png" alt="Export CSV"><br />
                                        <img src="../images/qs_1_3b.png" alt="Import CSV">
                                    </td>
                                </tr>
                            </table>    
                    </section>
                    <section id="step2">
                        <a class="navigation" href="#step2"><span>Step 2</span></a>
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
                                    <td><img src="../images/qs_2_1.png" alt="Upload template"></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Copy an existing template and then edit to customize it, <strong>or</strong>,</td>
                                    <td><img src="../images/page_copy_sm.png" alt="Copy template"> then <img src="../images/pencil_sm.png" alt="Edit template"></td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>Edit an existing template to customize it without copying it first, <strong>or</strong>,</td>
                                    <td><img src="../images/pencil_sm.png" alt="Edit template"></td>
                                </tr>
                                <tr>
                                    <td>4.</td>
                                    <td>Scrape a live web site.</td>
                                    <td><img src="../images/qs_2_2.png" alt="Scrape a site"></td>
                                </tr>
                            </table>                    
                    </section>
                    <section id="step3">
                        <a class="navigation" href="#step3"><span>Step 3</span></a>
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
                            <td><img src="../images/qs_3_1.png" alt="Upload education package"></td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td>Copy an existing education package and then edit to customize it, <strong>or</strong>,</td>
                            <td><img src="../images/page_copy_sm.png" alt="Copy template"> then <img src="../images/pencil_sm.png" alt="Edit template"></td>
                        </tr>
                        <tr>
                            <td>3.</td>
                            <td>Edit an existing education package to customize it without copying it first, <strong>or</strong>,</td>
                            <td><img src="../images/pencil_sm.png" alt="Edit template"></td>
                        </tr>
                        </table>                    
                    </section>
                    <section id="step4">
                        <a class="navigation" href="#step4"><span>Step 4</span></a>
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
                                    <td><img src="../images/qs_5_3.png" alt="Enter API key"></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Start a new campaign.</td>
                                    <td><img src="../images/qs_5_1.png" alt="Start campaign"></td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>Optionally, export campaign statistics as a CSV file.</td>
                                    <td><img src="../images/qs_5_2.png" alt="Download CSV"></td>
                                </tr>
                                <tr>
                                    <td>4.</td>
                                    <td>Review campaign statistics from the dashboard.</td>
                                    <td><img src="../images/house_sm.png" alt="Review statistics"></td>
                                </tr>
                            </table>                                             
                    </section>
                </article>
            </div>
        </div>
    </body>
</html>