<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Repository Management                                                     |
// +---------------------------------------------------------------------------+
// | nchkpdate.php                                                             |
// |                                                                           |
// | - Receives a POST variable with a string of plugin ids and versions       |
// |   Checks to see if any updates or upgrades available                      |
// |                                                                           |
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


/**
* Geeklog common function library 
*/
require_once '../../lib-common.php';

$array = array(5);

$_POST['REPOSITORY_ARRAY_PATCHES'] = serialize($array);

// Are we retreiving an integer count or a long list of ids
if (isset($_GET['cmd'])) {
    // Integer counts
    if ($_GET['cmd'] == 1) {
        // Get POST data for the integer
        $list_of_idv = (isset($_POST['REPOSITORY_ARRAY_INSTALLED'])) ? $_POST['REPOSITORY_ARRAY_INSTALLED'] : false;
	
	if ($list_of_idv === FALSE) {
	    echo serialize(false);
	    exit;
	}
	
        $patch_count = 0;
	$array_of_patches = array();
	$array_of_updates = array();
        $upgrade_count = 0;       
	$patch_array = array();
 
        // Array that has been serialized, in the format: array [ array [ id, version, plugin ids that have already been installed ] ] 
	// Check for patches
        $array_of_idv = unserialize($list_of_idv);
        $tblname = $_DB_table_prefix.'repository_patches';
        foreach ($array_of_idv as $id => $version) {
            // Get patches for that id, 
            $id2 = (int) $id;
            $result = DB_query("SELECT id,version,applies_num FROM {$tblname} WHERE plugin_id = '{$id2}' AND moderation = '0';");
            
	    // Check each plugin for the ID, and version number, check using version check function, and then depending on type
	    while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
		 // If it applies to all versions, no need to check
		 if ($result2['applies_num'] == 'al') {
		     $upgrade_count++;
		     $array_of_patches[] = $result2['id'];
		     continue;
		 }
		 
		 // Try comparing versions
		 $vseg = version_compare($result2['version'], $version, $result2['applies_num']);
		 
		 if ($vseg === TRUE) {
		     $upgrade_count++;
		     $array_of_patches[] = $result2['id'];
		 }
	    }	    
             

        }
	
        // Now check for upgrades
        $tblname = $_DB_table_prefix.'repository_upgrade';
        foreach ($array_of_idv as $id => $version) {
            // Get patches for that id, 
            $id2 = (int) $id;
            $result = DB_query("SELECT id,version FROM {$tblname} WHERE plugin_id = '{$id2}' AND moderation = '0';");
            
	    // Check each plugin for the ID, and version number, check using version check function, and then depending on type
	    while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
	        $array_of_upgrades[$id2] = $result2['version'];
	    }	    
             

        }
	
	$new_array = array($array_of_patches, $array_of_upgrades);
	// Ouput data
	echo serialize($new_array);


    }
    else if ($_GET['cmd'] == 2) {
        header("Content-Type: text/xml");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");	
	
	
        // Now we send back a list of data for each patch requested
	// Data is sent as a POST request in the following format: serialized ( array [ id, id, id, id, id, id  ]
        $list_of_idv = (isset($_POST['REPOSITORY_ARRAY_PATCHES'])) ? $_POST['REPOSITORY_ARRAY_PATCHES'] : false;	
	
	// If its false, exit
	if ($list_of_idv === FALSE) {
	    echo serialize(false);
	}
	
	$arrpatch = unserialize($list_of_idv);
	$tblname = $_DB_table_prefix.'repository_patches';
echo <<<COOSHY
<?xml version="1.0"?>

<repository
xmlns="http://www.geeklog.com"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="http://www.geeklog.com patch_listing.xsd">
<!-- Start patch List -->
COOSHY;
	// Loop over each ID
	foreach ($arrpatch as $id) {
	    $id2 = (int)$id;
	    
	    $result = DB_query("SELECT * FROM {$tblname} WHERE id = '{$id2}' AND moderation = '0';");
	    
	    // Get results, output them, but first, check if it is a blank query or not
	    $result2 = DB_fetchArray($result);
	    
	    if ($result2 === FALSE) {
	        continue;
	    }
	    
echo <<<YUMMY
<patch>
        <id>{$result2['id']}</id>
	<name>{$result2['name']}</name>
	<plugin_id>{$result2['plugin_id']}</plugin_id>
	<applies_num>{$result2['applies_num']}</applies_num>
	<version>{$result2['version']}</version>
	<severity>{$result2['severity']}</severity>
	<automatic_install>{$result2['automatic_install']}</automatic_install>
	<ext>{$result2['ext']}</ext>
	<description>{$result2['description']}</description>
</patch>
YUMMY;
	}

echo <<<ENDPART
<!-- End patch List -->
</repository>
ENDPART;
	
    }
}
else
{
    echo serialize(false);
}
?>
