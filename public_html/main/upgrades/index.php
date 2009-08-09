<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog 1.6                                                               |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// |  - This file displays a list of all plugins in the repository directory   |
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

/* Include common Geeklog library */
require_once '../../../lib-common.php';

// First loop over each file in the directory, and check the extension.
$results = array();
$dirhandle = opendir(".");
$file = NULL;

while ($file = readdir($dirhandle)) {
    // Is file a directory? (If so, discard)
        if (is_dir($file)) {
            continue;
        }
        
        // Is it the current directory
        if (($file == ".") or ($file == "..")) {
             continue;
        }
        
        // Get the basename, and match the extension
        $pathparts = pathinfo($file);
        
        // Make sure the extension is valid
        switch ($pathparts["extension"]) {
            case "gz":
            case "zip":
            case "bz2":
            case "tar":
                // Add to the array
                $results[] = "<a href='{$file}'>{$file}</a>";
                break;
            default:
                // Unknown file, break out
                break;
        }

}

// Clean up directory resources
closedir($dirhandle);

// Sort array
sort($results);

// If there are no repositories, display message
if (count($results) <= 0) {
    $results = $LANG_RMANAGER_DPLUGIN[4];
}

$display = '';
$display .= COM_siteHeader('');
$display .= tdisplay_formattedmessage($results, $LANG_RMANAGER_DPLUGIN[3], TRUE, FALSE);
$display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
$display .= COM_siteFooter();
COM_output($display);

?>
