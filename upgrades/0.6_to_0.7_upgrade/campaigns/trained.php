<?php

/**
 * file:    trained.php
 * version: 1.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Campaign management
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
session_start ();
//if values are being posted recieve all parameters into an array
if ( $_POST ) {
    if ( isset($_POST['trained'] )) {
        $trained = 1;

        //pull in session variables
        $target_id = $_SESSION['target_id'];
        $campaign_id = $_SESSION['campaign_id'];
        $education_id = $_SESSION['education_id'];
        
        //get the time when the data was posted
        $train_time = date ( 'Y-m-d H:i:s' );

        //connect to database
        include "../spt_config/mysql_config.php";

        //insert post metrics into database
        mysql_query ( "UPDATE campaigns_responses SET trained = '$trained', trained_time = '$train_time' WHERE campaign_id = '$campaign_id' AND target_id = '$target_id'" );
        
        //redirect to trained thank you page
        header ( 'location:../education/' . $education_id . '/trained.htm' );
        exit;
    }
} else {
    header ( 'location:http://127.0.0.1' );
    exit;
}

?>