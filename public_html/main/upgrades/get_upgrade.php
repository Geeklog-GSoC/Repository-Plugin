<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Redirect to the patch  based on ID passed                                 |
// +---------------------------------------------------------------------------+
// | get.php                                                                   |
// |                                                                           |
// | Geeklog Repository Manager                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2009-2039 by the following authors:                         |
// |                                                                           |
// | Authors: Timothy Patrick   - timpatrick AT gmail DOT com                  |
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

require_once '../../../lib-common.php';

// Get the plugin ID
$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);

// Select all the information from the database
$result = DB_query("SELECT plugin_id, version, version2, ext FROM {$_TABLES['repository_upgrade']} WHERE id = {$id};");
    
// Loop until we receive false
$result2 = DB_fetchArray($result);

// Did it succeed
if ($result2 === FALSE) {
    // Cannot install, error message, exit
    header("Location: ../no.php");
    exit;
}
  
// Make up file path
$get_path = $result2['version'] . '_from_' . $result2['version2'] . '_' . $result2['plugin_id'] . '_' . $id . $result2['ext'];

if (!file_exists($get_path)) {
    header("Location: ../no.php");
    exit;
}

header("Location: {$get_path}");   
