<?php
/**
 * file:    index.php
 * version: 25.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Dashboard management
 * copyright:	Copyright (C) 2011 The SPT Project. All rights reserved.
 * license:		GNU/GPL, see license.htm.
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
if (file_exists($includeContent)) {
    require_once $includeContent;
} else {
    header('location:../errors/404_is_authenticated.php');
}
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
    $request_protocol = "https";
} else {
    $request_protocol = "http";
}
//exclude Twitter feed ( 0 = display, 1 = exclude )
$exclude_twitter = 0;
?>

<!DOCTYPE HTML> 
<html>
    <head>
        <title>spt - dashboard</title>
        <!--meta-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="welcome to spt - simple phishing toolkit.  spt is a super simple but powerful phishing toolkit." />
        <!--favicon-->
        <link rel="shortcut icon" href="../images/favicon.ico" />
        <!--css-->
        <link rel="stylesheet" href="../includes/spt.css" type="text/css" />
        <link rel="stylesheet" href="spt_dashboard.css" type="text/css" />
        <!--scripts-->
        <script type="text/javascript" src="<?php echo $request_protocol ?>://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script type="text/javascript">

            var phish_pie;
            $(document).ready(function() {
                pish_pie = new Highcharts.Chart({
                    chart: {
                        renderTo: 'phish_pie_container',
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: 'Phish Pie'
                    },
                    tooltip: {
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage*Math.pow(10,2))/Math.pow(10,2) +'% (' + this.y + ')';
                        }
                    },   
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    return Math.round(this.percentage*Math.pow(10,0))/Math.pow(10,0) +'% (' + this.y + ')' ;
                                }
                            },
                            showInLegend: true
                        } 
                    },
                    
                    series: [{
                            type: 'pie',
                            name: 'Phish Pie',
                            data: [
                
<?php
//connect to database
include('../spt_config/mysql_config.php');

//get filters if they are set
//campaign id
if (isset($_REQUEST['pp_campaign']) && $_REQUEST['pp_campaign'] != 'All') {
    $pp_campaign_id = $_REQUEST['pp_campaign'];

    //get all campaign ids
    $r = mysql_query("SELECT id FROM campaigns");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($pp_campaign_id == $ra['id']) {
            $match = 1;
        }
    }

    //validate its a valid campaign id
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a valid campaign";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}
//browser
if (isset($_REQUEST['pp_browser']) && $_REQUEST['pp_browser'] != 'All') {
    $pp_browser = $_REQUEST['pp_browser'];

    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT browser FROM campaigns_responses");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($pp_browser == $ra['browser']) {
            $match = 1;
        }
    }

    //validate its a valid browser
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a selectable browser";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}
//group
if (isset($_REQUEST['pp_group']) && $_REQUEST['pp_group'] != 'All') {
    $pp_group_name = $_REQUEST['pp_group'];

    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT group_name FROM campaigns_and_groups");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($pp_group_name == $ra['group_name']) {
            $match = 1;
        }
    }

    //validate its a valid browser
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a valid group name";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}

//set SQL statements
$total_phishes_sql = "SELECT target_id FROM campaigns_responses WHERE sent = 2";
$total_sql = "SELECT target_id FROM campaigns_responses WHERE post IS NOT NULL AND sent = 2";
$total_link_only_sql = "SELECT target_id FROM campaigns_responses WHERE post IS NULL AND link != 0 AND sent = 2";

//append any filters if necessary
if (isset($pp_campaign_id)) {
    $total_phishes_sql .= " AND campaign_id = " . $pp_campaign_id;
    $total_sql .= " AND campaign_id = " . $pp_campaign_id;
    $total_link_only_sql .= " AND campaign_id = " . $pp_campaign_id;
}

//append any filters if necessary
if (isset($pp_browser)) {
    $total_phishes_sql .= " AND browser = '" . $pp_browser . "'";
    $total_sql .= " AND browser = '" . $pp_browser . "'";
    $total_link_only_sql .= " AND browser = '" . $pp_browser . "'";
}

//get total number of successful phishes sent
$r = mysql_query($total_phishes_sql);
$total_phishes = mysql_num_rows($r);

//get total number of people who posted data
$r = mysql_query($total_sql);
$total_posts = mysql_num_rows($r);

//get total number of people who clicked the link but didn't post data
$r = mysql_query($total_link_only_sql);
$total_link_only = mysql_num_rows($r);

//calculate no reponse
$total_no_response = $total_phishes - $total_posts - $total_link_only;

if ($total_link_only == 0 && $total_no_response == 0 && $total_posts == 0) {
    echo "['No Responses Yet', 0]";
} else {
    //print results in highcharts format
    echo "['Did Not Click', " . $total_no_response . "],";
    echo "['Followed Link', " . $total_link_only . "],";
    echo "['Submitted Form', " . $total_posts . "],";
}
?>
                    ]
                }]
        });
    });

    var bad_targets;
    $(document).ready(function() {
        bad_targets = new Highcharts.Chart({
            chart: {
                renderTo: 'bad_targets_container',
                type: 'bar'
            },
            title: {
                text: 'Top 10 High Risk Targets'
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.series.name +': '+ this.y +'';
                }
            },   
                              
<?php
//connect to database
include('../spt_config/mysql_config.php');

//get filters if they are set
//campaign id
if (isset($_REQUEST['bt_campaign']) && $_REQUEST['bt_campaign'] != 'All') {
    $bt_campaign_id = $_REQUEST['bt_campaign'];

    //get all campaign ids
    $r = mysql_query("SELECT id FROM campaigns");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($bt_campaign_id == $ra['id']) {
            $match = 1;
        }
    }

    //validate its a valid campaign id
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a valid campaign";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}
//browser
if (isset($_REQUEST['bt_browser']) && $_REQUEST['bt_browser'] != 'All') {
    $bt_browser = $_REQUEST['bt_browser'];

    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT browser FROM campaigns_responses");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($bt_browser == $ra['browser']) {
            $match = 1;
        }
    }

    //validate its a valid browser
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a selectable browser";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}
//group
if (isset($_REQUEST['bt_group']) && $_REQUEST['bt_group'] != 'All') {
    $bt_group_name = $_REQUEST['bt_group'];

    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT group_name FROM campaigns_and_groups");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($bt_group_name == $ra['group_name']) {
            $match = 1;
        }
    }

    //validate its a valid browser
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a valid group name";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}

//set SQL statements
$bad_targets = "SELECT CONCAT(targets.fname, ' ',targets.lname) AS name, SUM(campaigns_responses.link) AS links, COUNT(campaigns_responses.post) AS posts, ((SUM(campaigns_responses.link))+(COUNT(campaigns_responses.post))) AS total_response FROM campaigns_responses JOIN targets ON campaigns_responses.target_id = targets.id WHERE sent = 2";

//append any filters if necessary
//campaign
if (isset($bt_campaign_id)) {
    $bad_targets .= " AND campaigns_responses.campaign_id = " . $bt_campaign_id;
}
//browser
if (isset($bt_browser)) {
    $bad_targets .= " AND campaigns_responses.browser = '" . $bt_browser . "'";
}
//group
if (isset($bt_group_name)) {
    $bad_targets .= " AND targets.group_name = '" . $bt_group_name . "'";
}

$bad_targets .= " GROUP BY name HAVING posts IS NOT NULL ORDER BY posts DESC, links DESC LIMIT 10";

//echo xAxix header for chart
echo "xAxis: {categories: [";

//get bad targets
$r = mysql_query($bad_targets);
$count = mysql_num_rows($r);
while ($ra = mysql_fetch_assoc($r)) {
    //get name
    $target_name = $ra['name'];

    //echo xAxis data
    echo "'" . $target_name . "'";

    //echo comma if not the last one
    if ($count > 1) {
        echo ",";
    }
    --$count;
}

//echo xAxis closing
echo "]},";

//echo yAxis
echo "yAxis:{min: 0,title: {text: 'Links & Posts'}},";

//echo plot options
echo "plotOptions:{series: {stacking: 'normal'}},";

//echo link only header
echo "series: [{name: 'Link Only',data:[";

//echo link only data
$r = mysql_query($bad_targets);
$count = mysql_num_rows($r);
while ($ra = mysql_fetch_assoc($r)) {
    $link_only = $ra['links'] - $ra['posts'];

    echo $link_only;
    if ($count > 1) {
        echo ",";
    }
    --$count;
}

//echo link only footer
echo "]},";

//echo posts header
echo "{name: 'Posts',data:[";

//echo posts data
$r = mysql_query($bad_targets);
$count = mysql_num_rows($r);
while ($ra = mysql_fetch_assoc($r)) {
    $posts = $ra['posts'];

    echo $posts;
    if ($count > 1) {
        echo ",";
    }
    --$count;
}

//echo posts footer
echo "]}]";
?>
        });
    });

    var email_stats;
    $(document).ready(function() {
        email_stats = new Highcharts.Chart({
            chart: {
                renderTo: 'email_stats_container',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Email Status Summary'
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage*Math.pow(10,2))/Math.pow(10,2) +'% (' + this.y + ')';
                }
            },   
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return Math.round(this.percentage*Math.pow(10,0))/Math.pow(10,0) +'% (' + this.y + ')';;
                        }
                    },
                    showInLegend: true
                } 
            },
                    
            series: [{
                    type: 'pie',
                    name: 'Email Status',
                    data: [
                
<?php
//connect to database
include('../spt_config/mysql_config.php');

//get filters if they are set
//campaign id
if (isset($_REQUEST['es_campaign']) && $_REQUEST['es_campaign'] != 'All') {
    $es_campaign_id = $_REQUEST['es_campaign'];

    //get all campaign ids
    $r = mysql_query("SELECT id FROM campaigns");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($es_campaign_id == $ra['id']) {
            $match = 1;
        }
    }

    //validate its a valid campaign id
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a valid campaign";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}
//browser
if (isset($_REQUEST['es_browser']) && $_REQUEST['es_browser'] != 'All') {
    $es_browser = $_REQUEST['es_browser'];

    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT browser FROM campaigns_responses");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($es_browser == $ra['browser']) {
            $match = 1;
        }
    }

    //validate its a valid browser
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a selectable browser";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}
//group
if (isset($_REQUEST['es_group']) && $_REQUEST['es_group'] != 'All') {
    $es_group_name = $_REQUEST['es_group'];

    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT group_name FROM campaigns_and_groups");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($es_group_name == $ra['group_name']) {
            $match = 1;
        }
    }

    //validate its a valid browser
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a valid group name";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}

//set SQL statements
$email_status_sql = "SELECT COUNT(sent) AS sent FROM campaigns_responses";

//add WHERE clause
$email_status_sql .= " WHERE";

//append any filters if necessary
if (isset($es_campaign_id)) {
    $email_status_sql .= " campaign_id = " . $es_campaign_id;
}

if (isset($es_campaign_id)) {
    $email_status_sql .= " AND";
}

//append any filters if necessary
if (isset($es_browser)) {
    $email_status_sql .= " browser = '" . $es_browser . "'";
}

if (isset($es_browser)) {
    $email_status_sql .= " AND";
}

//query for emails not sent
$r = mysql_query($email_status_sql . " sent = 0");
while ($ra = mysql_fetch_assoc($r)) {
    $email_not_sent = $ra['sent'];
}

//query for emails with an unkown status
$r = mysql_query($email_status_sql . " sent = 1");
while ($ra = mysql_fetch_assoc($r)) {
    $email_unknown = $ra['sent'];
}

//query for emails sent successfully
$r = mysql_query($email_status_sql . " sent = 2");
while ($ra = mysql_fetch_assoc($r)) {
    $email_sent_successfully = $ra['sent'];
}

//query for emails that failed
$r = mysql_query($email_status_sql . " sent = 3");
while ($ra = mysql_fetch_assoc($r)) {
    $email_failures = $ra['sent'];
}

if ($email_failures == 0 && $email_not_sent == 0 && $email_sent_successfully == 0 && $email_unknown == 0) {
    echo "['No Responses Yet', 0]";
} else {

    //print results in highcharts format
    echo "['Success', " . $email_sent_successfully . "],";
    echo "['Failures', " . $email_failures . "],";
    echo "['Unkown', " . $email_unknown . "],";
    echo "['Not Sent Yet', " . $email_not_sent . "],";
}
?>
                    ]
                }]
        });
    });
    var browser_stats;
    $(document).ready(function() {
        browser_stats = new Highcharts.Chart({
            chart: {
                renderTo: 'browser_stats_container',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Browser/Version Stats'
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage*Math.pow(10,2))/Math.pow(10,2) +'% (' + this.y + ')';
                }
            },   
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return Math.round(this.percentage*Math.pow(10,0))/Math.pow(10,0) +'% (' + this.y + ')';
                        }
                    },
                    showInLegend: true
                } 
            },
                    
            series: [{
                    type: 'pie',
                    name: 'Browser Stats',
                    data: [
                
<?php
//connect to database
include('../spt_config/mysql_config.php');

//get filters if they are set
//campaign id
if (isset($_REQUEST['bs_campaign']) && $_REQUEST['bs_campaign'] != 'All') {
    $bs_campaign_id = $_REQUEST['bs_campaign'];

    //get all campaign ids
    $r = mysql_query("SELECT id FROM campaigns");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($bs_campaign_id == $ra['id']) {
            $match = 1;
        }
    }

    //validate its a valid campaign id
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a valid campaign";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}
//browser
if (isset($_REQUEST['bs_browser']) && $_REQUEST['bs_browser'] != 'All') {
    $bs_browser = $_REQUEST['bs_browser'];

    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT browser FROM campaigns_responses");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($bs_browser == $ra['browser']) {
            $match = 1;
        }
    }

    //validate its a valid browser
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a selectable browser";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}
//group
if (isset($_REQUEST['bs_group']) && $_REQUEST['bs_group'] != 'All') {
    $bs_group_name = $_REQUEST['bs_group'];

    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT group_name FROM campaigns_and_groups");
    while ($ra = mysql_fetch_assoc($r)) {
        if ($bs_group_name == $ra['group_name']) {
            $match = 1;
        }
    }

    //validate its a valid browser
    if (!isset($match)) {
        $_SESSION['alert_message'] = "Please specify a valid group name";
        header('location:./#alert');
        exit;
    }

    //reset match
    unset($match);
}

//set SQL statements
$browser_stats_sql = "SELECT DISTINCT(CONCAT(browser, ' ', browser_version)) AS browser, COUNT(browser) AS count FROM campaigns_responses WHERE browser IS NOT NULL GROUP BY browser";

//append any filters if necessary
if (isset($bs_campaign_id)) {
    $browser_stats_sql .= " AND campaign_id = " . $bs_campaign_id;
}

//append any filters if necessary
if (isset($bs_browser)) {
    $browser_stats_sql .= " AND browser = '" . $bs_browser . "'";
}

//get total number of browsers
$r2 = mysql_query("SELECT COUNT(browser) AS count FROM campaigns_responses WHERE browser IS NOT NULL");
while ($ra2 = mysql_fetch_assoc($r2)) {
    $total_browser_count = $ra2['count'];
}

//get variables
$r = mysql_query($browser_stats_sql);
if (mysql_num_rows($r) < 1) {
    echo "['No Responses Yet', 0]";
} else {
    $r = mysql_query($browser_stats_sql);
    while ($ra = mysql_fetch_assoc($r)) {
        $browser_and_version = $ra['browser'];
        $browser_count = $ra['count'];
        echo "['" . $browser_and_version . "', " . $browser_count . "],";
    }
}
?>
                    ]
                }]
        });
    });


        </script>
    </head>

    <body>
        <div id="wrapper">

            <!--sidebar-->
<?php include '../includes/sidebar.php'; ?>					

            <!--content-->
            <div id="content">
<?php
echo "<div class=\"dashboard_module_campaigns\">";
include "../campaigns/dashboard_module.php";
echo "</div>";
?>

                <div class="dashboard_module">
                    <table id="twitter_feed">
                        <tr>
                            <td colspan="2"><h3>@sptookit on Twitter</h3></td>
                            <td style="text-align: right;">
                                <a class="tooltip"><img src="../images/lightbulb_sm.png" alt="help" /><span>You can disable the Twitter feed if you want to by editing the value of the <strong>$exclude_twitter</strong> variable in the <strong>/dashboard/index.php</strong> file.<br /><br />The default value is set to 0, which enables the feed.<br />Change value to 1 and save the file to disable the feed.<br /><br />This setting will be controlled via a "Settings" module in a future release of the spt.</span></a>
                            </td>
                        </tr>                        
                        <tr>
                            <td colspan="3">
<?php
//EER twitter opt-out
if ($exclude_twitter == 1) {
    echo "Twitter feed has been disabled using the 'exclude_twitter' variable.";
    echo "<!--";
}
?>

                                <script charset="utf-8" src="<?php echo $request_protocol ?>://widgets.twimg.com/j/2/widget.js"></script>
                                <script>
                                    new TWTR.Widget({
                                        version: 2,
                                        type: 'profile',
                                        rpp: 10,
                                        interval: 30000,
                                        width: 700,
                                        height: 100,
                                        theme: {
                                            shell: {
                                                background: '#ffffff',
                                                color: '#000000'
                                            },
                                            tweets: {
                                                background: '#ffffff',
                                                color: '#000000',
                                                links: '#a3a3a3'
                                            }
                                        },
                                        features: {
                                            scrollbar: true,
                                            loop: false,
                                            live: true,
                                            behavior: 'all'
                                        }
                                    }).render().setUser('sptoolkit').start();
                                </script>    
                            </td>    
                        </tr>

<?php
//EER twitter opt-out
if ($exclude_twitter == 1) {
    echo "-->";
}
?>

                    </table>
                </div>

            </div>
        </div>
    </body>
</html>
