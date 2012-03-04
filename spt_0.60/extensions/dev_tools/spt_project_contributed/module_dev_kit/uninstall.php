<?php

/**
 * file:    uninstall.php
 * version: 2.0
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

//This is the uninstall script for the module template

//This script is only needed if your module install.php script:
// - Made one or more tables other than the default module table
// - Modified an existing table (perhaps by adding a column)
// - Put file into the file system outside of the default module directory (/spt/module_name/)

//Delete additional tables...
//mysql_query("DROP TABLE $table") or die ('<div id="die_error">There is a problem with the database...please try again later</div>');
//mysql_query("DROP TABLE $table") or die ('<div id="die_error">There is a problem with the database...please try again later</div>');

//Modify existing tables (change back to original condition)...possibly DANGEROUS!
//mysql_query("Query goes here") or die ('<div id="die_error">There is a problem with the database...please try again later</div>');
//mysql_query("Query goes here") or die ('<div id="die_error">There is a problem with the database...please try again later</div>');

//Delete files that are outside of the module directory...
//unlink('path/filename');
//unlink('path/filename');
?>