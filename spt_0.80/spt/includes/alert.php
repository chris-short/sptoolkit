<?php

/**
 * file:    alert.php
 * version: 1.0
 * package: Simple Phishing Toolkit (spt)
 * component:   Core Files
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
//alert pop-over for all modules
//check to see if there are any alerts
if ( isset ( $_SESSION['alert_message'] ) ) {
    //echo alert pop-over
    echo "<div id=\"alert\"><div>".$_SESSION['alert_message']."</div></div>";
    //clear the alert session after it is written
    unset ( $_SESSION['alert_message'] );
    //script to hide the alert div after a few seconds
    echo "
        <script>
            setTimeout(function() {
                $('#alert').fadeOut('slow');
            }, 5000);   
        </script>
    ";
}
?>

