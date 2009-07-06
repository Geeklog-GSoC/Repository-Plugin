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

require_once '../../lib-common.php';

// Get the plugin ID
$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);

// Select all the information from the database
$result = DB_query("SELECT name, applies_num, version, ext FROM {$_TABLES['repository_patches']} WHERE id = {$id};");
    
// Loop until we receive false
$result2 = DB_fetchArray($result);

// Did it succeed
if ($result2 === FALSE) {
    // Cannot install, error message, exit
    header("Location: ../no.php");
    exit;
}

// Make up file path
$get_path = $result2['name'] . '_' . $result2['version'] . '_' . $result2['applies_num'] . '_' . $id . $result2['ext'];
    
header("Location: {$get_path}");   
