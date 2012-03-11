<?php

/**
 * file:    index.php
 * version: 5.0
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
                                    <strong>There is one of two ways to get started...</strong>
                                    <br /><br />
                                </td>
                            </tr>
                            <tr>
                                <td class="step">1</td>
                                <td>Work your way through each step starting with Step 1 above, <strong>or...</strong></td>
                            </tr>
                            <tr>
                                <td class="step">2</td>
                                <td>Skip to Step 5 and start a campaign immediately using the included quick start templates and education as well as the auto-generated <strong>Admin - Test</strong> target group we've put your email address in when you setup spt.</td>
                            </tr>
                        </table>
                    </section>
                    <section id="step1">
                        <a class="navigation" href="#step1"><span>Step 1</span></a>
                            <table>
                                <tr>
                                    <td colspan="3">
                                        <strong>Configure metrics and add targets</strong>
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
                                    <td><img src="images/qs_1_1.png" alt="Edit metrics"></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Create single targets, <strong>or</strong>,</td>
                                    <td><img src="images/qs_1_2.png" alt="Add one target"></td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>
                                        First, use the export to CSV function to download an editable CSV file.<br />
                                        Next, import the edited file to add many targets at once.
                                    </td>
                                    <td>
                                        <img src="images/qs_1_3a.png" alt="Export CSV"><br />
                                        <img src="images/qs_1_3b.png" alt="Import CSV">
                                    </td>
                                </tr>
                            </table>    
                    </section>
                    <section id="step2">
                        <a class="navigation" href="#step2"><span>Step 2</span></a>
                            <table>
                                <tr>
                                    <td colspan="3">
                                        <strong>Upload templates or scrape live sites</strong>
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
                                    <td>Upload a template, <strong>or</strong>,</td>
                                    <td><img src="images/qs_2_1.png" alt="Upload template"></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Scrape a live web site.</td>
                                    <td><img src="images/qs_2_2.png" alt="Scrape a site"></td>
                                </tr>
                            </table>                    
                    </section>
                    <section id="step3">
                        <a class="navigation" href="#step3"><span>Step 3</span></a>
                        <table>
                        <tr>
                            <td colspan="3">
                                <strong>Upload education packages (Optional)</strong>
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
                            <td><img src="images/qs_3_1.png" alt="Upload education package"></td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td>Upload a copy of the default education package.</td>
                            <td><img src="images/qs_3_1.png" alt="Upload education package"></td>
                        </tr>
                        </table>                    
                    </section>
                    <section id="step4">
                        <a class="navigation" href="#step4"><span>Step 4</span></a>
                            <table>
                                <tr>
                                    <td colspan="3">
                                        <strong>Edit templates or education packages as needed to customize them (Optional)</strong>
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
                                    <td>If needed, select a template and then a file to edit.</td>
                                    <td>[select file]</td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>If needed, select an education package and then a file to edit.</td>
                                    <td>[select file]</td>
                                </tr>
                            </table>                        
                    </section>
                    <section id="step5">
                        <a class="navigation" href="#step5"><span>Step 5</span></a>
                            <table>
                                <tr>
                                    <td colspan="3">
                                        <strong>Start and monitor a campaign</strong>
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
                                    <td>Start a new campaign.</td>
                                    <td><img src="images/qs_5_1.png" alt="Start campaign"></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Optionally, export campaign statistics as a CSV file.</td>
                                    <td><img src="images/qs_5_2.png" alt="Download CSV"></td>
                                </tr>
                                <tr>
                                    <td>3.</td>
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