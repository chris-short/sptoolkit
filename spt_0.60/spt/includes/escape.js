/**
 * file:    escape.js
 * version: 2.0
 * package: Simple Phishing Toolkit (spt)
 * component:	Core Files
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
**/

//script will re-direct the user to the root of the current directory to clear pop-overs 

document.onkeydown = function(escape)
{ 
    if (escape == null) 
    { 
        // internet explorer 
        keycode = event.keyCode; 
    } 
    else 
    { 
        // firefox 
        keycode = escape.which; 
    } 
	          
    if(keycode == 27)
    { 
        window.location = "."
    } 
};
