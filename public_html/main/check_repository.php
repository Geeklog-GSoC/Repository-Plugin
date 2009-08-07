<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 1.6                                                               |
// +---------------------------------------------------------------------------+
// | check_repository.php                                                      |
// |                                                                           |
// | Checks to see if the repository is blacklisted, whitelisted, or none      |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2009 by the following authors:                         |
// |                                                                           |
// | Authors: Tim Patrick       - timpatrick12 AT gmail DOT com                |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

/**
* Geeklog common function library
*/
require_once '../../lib-common.php';

// Return array of updated lists
if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == "update")) {
    $array_holder = array();
    $rep_array = (isset($_POST['REPOSITORIES'])) ? $_POST['REPOSITORIES'] : array());
    
    foreach ($rep_array as $repository) {
        $repository = COM_applyFilter($repository);
        $result = DB_query("SELECT status FROM {$_TABLES['repository_access_list']} WHERE repository_url = '{$repository}';");
        $result2 = DB_fetchArray($result);
        
        // Doesn't exist yet, so 2
        if ($result2 === FALSE) {
            $array_holder[$repository] = 2;
        }
        else {         
            $array_holder[$repository] = $result2['status'];
        }
    }
    
    echo serialize($array_holder);
}
else {
    // Get repository as $_GET page
    $repo = (isset($_GET['repository'])) ? COM_applyFilter($_GET['repository']) : false;

    if ($repo === FALSE) {
        echo serialize(false);
        exit;
    }

    // Try to get an ID sticker from the database
    $result = DB_query("SELECT status FROM {$_TABLES['repository_access_list']} WHERE repository_url = '{$repo}';");

    $result2 = DB_fetchArray($result);

    // If its false, it means that it doesn't exist in the database
    if ($result2 === FALSE) {
        echo serialize(2);
    }
    else {
        // check status for banned or certified ok
        if ($result2['status'] == 3) {
            echo serialize(3);      
        }
        else {
            echo serialize(1);
        }
    }
}

?>
