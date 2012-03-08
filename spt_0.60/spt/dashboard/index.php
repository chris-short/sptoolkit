<?php
/**
 * file:		index.php
 * version:		11.0
 * package:		Simple Phishing Toolkit (spt)
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
if ( file_exists ( $includeContent ) ) {
    require_once $includeContent;
} else {
    header ( 'location:../errors/404_is_authenticated.php' );
}
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
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
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
			text: null
		},
		tooltip: {
                                                        formatter: function() {
				return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
			}
                                    },   
                                    plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
                                                                        dataLabels: {
                                                                            enabled: false
                                                                       },
				showInLegend: true
			} 
		},
                    
		series: [{
			type: 'pie',
			name: 'Browser share',
			data: [
                
<?php
//connect to database
include('../spt_config/mysql_config.php');

//get filters if they are set
//campaign id
if(isset($_REQUEST['campaign']) && $_REQUEST['campaign'] != 'All'){
    $campaign_id = $_REQUEST['campaign'];
    
    //get all campaign ids
    $r = mysql_query("SELECT id FROM campaigns");
    while($ra = mysql_fetch_assoc ( $r)){
        if($campaign_id == $ra['id']){
            $match = 1;
        }
    }
    
    //validate its a valid campaign id
    if(!isset($match)){
        $_SESSION['alert_message'] = "Please specify a valid campaign";
        header('location:./#alert');
        exit;
    }
    
    //reset match
    unset($match);
}
//browser
if(isset($_REQUEST['browser']) && $_REQUEST['browser'] != 'All'){
    $browser = $_REQUEST['browser'];
    
    //get all types of browsers
    $r = mysql_query("SELECT DISTINCT browser FROM campaigns_responses");
    while($ra = mysql_fetch_assoc ( $r)){
        if($browser == $ra['browser']){
            $match = 1;
        }
    }
    
    //validate its a valid browser
    if(!isset($match)){
        $_SESSION['alert_message'] = "Please specify a selectable browser";
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
if(isset($campaign_id)){
    $total_phishes_sql .= " AND campaign_id = ".$campaign_id;
    $total_sql .= " AND campaign_id = ".$campaign_id;
    $total_link_only_sql .= " AND campaign_id = ".$campaign_id;
}

//append any filters if necessary
if(isset($browser)){
    $total_phishes_sql .= " AND browser = '".$browser."'";
    $total_sql .= " AND browser = '".$browser."'";
    $total_link_only_sql .= " AND browser = '".$browser."'";    
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

//calcuate percentages
$total_no_response_percentage = round(($total_no_response / $total_phishes) * 100,2);
$total_link_only_percentage = round(($total_link_only / $total_phishes) * 100,2);
$total_posts_percentage = round(($total_posts / $total_phishes) * 100,2);

if($total_link_only_percentage == 0 && $total_no_response_percentage == 0 && $total_posts_percentage == 0){
    echo "['No Responses Yet', 100]";
}else{
    //print results in highcharts format
    echo "['Did Not Click', ".$total_no_response_percentage."],";
    echo "['Followed Link', ".$total_link_only_percentage."],";
    echo "{name: 'Submitted Form', y: ".$total_posts_percentage.", sliced: true, selected: true},";
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
//scan the root directory
$dirs = scandir ( '../' );

//for each directory look for dashboard_module.php
foreach ( $dirs as $dir ) {
    if ( is_dir ( '../' . $dir ) ) {
        //if campaigns module do something a little different
        if ( file_exists ( '../' . $dir . '/dashboard_module.php' ) && $dir == 'campaigns' ) {
            echo "<div class=\"dashboard_module_campaigns\">";
            include "../" . $dir . "/dashboard_module.php";
            echo "</div>";
        }
    }
}
echo "<div class=\"dashboard_module\">";
echo "<h1>Other Stats</h1>";
echo "<table>";
foreach ( $dirs as $dir ) {
    //if dashboard_module.php exists in the directory include it
    if ( file_exists ( '../' . $dir . '/dashboard_module.php') && $dir != 'campaigns' ) {
        include "../" . $dir . "/dashboard_module.php";
    }
}
echo "</table>";
echo "</div>";
?>
            </div>
        </div>
    </body>
</html>
