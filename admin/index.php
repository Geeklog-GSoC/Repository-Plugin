<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Repository Management                                                     |
// +---------------------------------------------------------------------------+
// | index.php                                                               |
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
* Geeklog common function library and Admin authentication
*/
require_once '../../../lib-common.php';
require_once '../../auth.inc.php';

$display = "";
/**
* Displays a message on the webpage according to the tmsg standard ($msg contains array key for $MESSAGE array, remaining GET parameters contain sprintf 
* data
* 
* @param    int     $msg        ID of message to show
* @return   string              HTML block with message
*
*/
function ShowTMessageRManager($msg)
{
    global $LANG_RMANAGER_UPLUGIN;

    $retval = '';

    if ($msg > 0) {
        $message = $LANG_RMANAGER_UPLUGIN[$msg];
         
        // Only if $_GET['enable_spf'] is enabled
        if ( (isset($_GET['enable_spf'])) and ($_GET['enable_spf'] == 1)) {
          
            $eval = '$holder = sprintf($message';
            foreach ($_GET as $name => $key) {
                // If its msg as the name, we pass as thats ok. Otherwise, lets start racking up!
                if ( ($name == "tmsg") or ($name == "enable_spf")) {
                    continue;
                }
              
                $eval .= ",COM_applyFilter(\$_GET['$name'])";
            }
            $eval .= ');';
            
            // Evaluate code
            // Use of EVAL here is totally safe as we built the string
            eval($eval);
            $message = $holder;
        }

        if (!empty($message)) {
            $retval .= COM_showMessageText($message);
        }
    }

    return $retval;
}

if ($_GET['msg']) {
    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[(int)$_GET['msg']]);
}
else if ($_GET['tmsg']) {
    $display .= ShowTMessageRManager((int)$_GET['tmsg']);
}

// Ensure user even has the rights to access this page
if (!SEC_hasRights('repository.manage')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             . COM_siteFooter();

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the rmanager administration screen.");

    COM_output($display);

    exit;
}

$display .= COM_siteHeader('');
$glib = "";
// Are there any plugins to moderate? Lets get that information 
$tblname = $_DB_table_prefix.'repository_listing';
$result = DB_query("SELECT count(id) FROM {$tblname} WHERE moderation = '1';");
$result2 = DB_fetchArray($result);
$count_plugins = (int) $result2['count(id)'];

if ($count_plugins > 0) {
    $glib .= '<a href="index.php?cmd=1">'.$LANG_RMANAGER_ADMIN[0].$count_plugins.$LANG_RMANAGER_ADMIN[1].'</a><br /><br />';
   
}

// How about patches
$tblname = $_DB_table_prefix.'repository_patches';
$result = DB_query("SELECT count(id) FROM {$tblname} WHERE moderation = '1';");
$result2 = DB_fetchArray($result);
$count_patches = (int) $result2['count(id)'];

if ($count_patches > 0) {
     $glib .= '<a href="index.php?cmd=2">'.$LANG_RMANAGER_ADMIN[2].$count_patches.$LANG_RMANAGER_ADMIN[3].'</a><br /><br />';
}



// Now we check for a GET parameter - if none, then we simply show them the main link page.
if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 1)) {
    // Show  Moderate Plugins
    // So show listing of all plugins that need to be moderated
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'listmoderateplugins.thtml'));
    $data->set_var('lang_95', $LANG_RMANAGER_UPLUGIN[95]);
    $data->set_var('lang_96', $LANG_RMANAGER_UPLUGIN[96]);
    $data->set_var('lang_97', $LANG_RMANAGER_UPLUGIN[97]);
    $data->set_var('lang_98', $LANG_RMANAGER_UPLUGIN[98]);
    $data->set_var('lang_99', $LANG_RMANAGER_UPLUGIN[99]);
    $data->set_var('lang_100', $LANG_RMANAGER_UPLUGIN[100]);
    
    // Get data from table, loop and output information
    $tblname = $_DB_table_prefix.'repository_listing';
    $result = DB_query("SELECT id, name, version FROM {$tblname} WHERE moderation = '1';");
    $ds2 = "";
    
    while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
$ds2 .= <<<EOM
<tr>
<td class='name'>{$result2['name']}</td><td class='type'>{$_USER['username']}</td><td class='opt'><a href="index.php?cmd=6&ret=1&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[96]}</a></td><td class='opt'<a href="index.php?cmd=6&ret=2&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[97]}</a></td><td class='opt'><a href="index.php?cmd=6&ret=3&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[98]}</a></td><td class='opt'></td>
</tr>
EOM;
    }
    
    // Set to content
    $data->set_var('value_1', $ds2);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 2)) {
    // Show  Moderate Patches
    // So show listing of all plugins that need to be moderated
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'listmoderateplugins.thtml'));
    $data->set_var('lang_95', $LANG_RMANAGER_UPLUGIN[110]);
    $data->set_var('lang_96', $LANG_RMANAGER_UPLUGIN[96]);
    $data->set_var('lang_97', $LANG_RMANAGER_UPLUGIN[97]);
    $data->set_var('lang_98', $LANG_RMANAGER_UPLUGIN[98]);
    $data->set_var('lang_99', $LANG_RMANAGER_UPLUGIN[99]);
    $data->set_var('lang_100', $LANG_RMANAGER_UPLUGIN[100]);
    
    // Get data from table, loop and output information
    $tblname = $_DB_table_prefix.'repository_patches';
    $result = DB_query("SELECT id, name, version,applies_num,plugin_id FROM {$tblname} WHERE moderation = '1';");
    $ds2 = "";
    
    while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
$ds2 .= <<<EOM
<tr>
<td class='name'>{$result2['name']} {$result2['applies_num']} {$result2['version']} {$LANG_RMANAGER_UPLUGIN[103]} {$result2['plugin_id']}</td><td class='type'>{$_USER['username']}</td><td class='opt'><a href="index.php?cmd=6&ret=4&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[96]}</a></td><td class='opt'<a href="index.php?cmd=6&ret=5&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[97]}</a></td><td class='opt'><a href="index.php?cmd=6&ret=6&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[98]}</a></td><td class='opt'></td>
</tr>
EOM;
    }
    
    // Set to content
    $data->set_var('value_1', $ds2);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 3)) {
    //  Show all plugins
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'listmoderateplugins.thtml'));
    $data->set_var('lang_95', $LANG_RMANAGER_UPLUGIN[112]);
    $data->set_var('lang_96', $LANG_RMANAGER_UPLUGIN[96]);
    $data->set_var('lang_97', $LANG_RMANAGER_UPLUGIN[111]);
    $data->set_var('lang_98', $LANG_RMANAGER_UPLUGIN[98]);
    $data->set_var('lang_99', $LANG_RMANAGER_UPLUGIN[99]);
    $data->set_var('lang_100', $LANG_RMANAGER_UPLUGIN[100]);
    $data->set_var('lang_131', $LANG_RMANAGER_UPLUGIN[131]);
     
    // Get data from table, loop and output information
    $tblname = $_DB_table_prefix.'repository_listing';
    
    // First its time to check for a search phrase
    if (isset($_POST['GEEKLOG_SEARCH'])) {
        $search = COM_applyFilter($_POST['GEEKLOG_SEARCH']);
	$limit = (int) $_POST['GEEKLOG_LIMIT'];
	$version = COM_applyFilter($_POST['GEEKLOG_VERSION']);
	$result = DB_query("SELECT id, name, state, version FROM {$tblname} WHERE moderation = '0' AND name LIKE '%{$search}%' AND version LIKE '%{$version}%' LIMIT $limit;");
    }
    else
    {    
        $result = DB_query("SELECT id, name, state, version FROM {$tblname} WHERE moderation = '0';");
    }
    
    $ds2 = "";
    $i = 0;
    
    while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
$ds2 .= <<<EOM
<tr>
<td class='name'>{$result2['name']} {$result2['state']} {$result2['version']}</td><td class='type'>{$_USER['username']}</td><td class='opt'><a href="index.php?cmd=6&ret=7&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[96]}</a></td><td class='opt'<a href="index.php?cmd=5&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[111]}</a></td><td class='opt'><a href="index.php?cmd=6&ret=8&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[98]}</a></td><td class='opt'><a href="index.php?cmd=4&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[131]}</a></td>
</tr>
EOM;
        $i++;
    }
    
    // No plugins
    if ($i == 0) {
        $ds2 = $LANG_RMANAGER_UPLUGIN[113];
    }
    
    // Set to content
    $data->set_var('value_1', $ds2);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
    
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 4)) {
    //  Show all patches
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'listmoderateplugins.thtml'));
    $data->set_var('lang_95', $LANG_RMANAGER_UPLUGIN[114]);
    $data->set_var('lang_96', $LANG_RMANAGER_UPLUGIN[96]);
    $data->set_var('lang_97', '');
    $data->set_var('lang_131', '');
    $data->set_var('lang_98', $LANG_RMANAGER_UPLUGIN[98]);
    $data->set_var('lang_99', $LANG_RMANAGER_UPLUGIN[99]);
    $data->set_var('lang_100', $LANG_RMANAGER_UPLUGIN[100]);
    
    // Get data from table, loop and output information
    $tblname = $_DB_table_prefix.'repository_patches';
    
    // Search by name or version
    if (isset($_POST['GEEKLOG_SEARCH'])) {
        $search = COM_applyFilter($_POST['GEEKLOG_SEARCH']);
	$limit = (int) $_POST['GEEKLOG_LIMIT'];
	$version = COM_applyFilter($_POST['GEEKLOG_VERSION']);
	$result = DB_query("SELECT id, name, applies_num, version, plugin_id FROM {$tblname} WHERE moderation = '0' AND name LIKE '%{$search}%' AND version LIKE '%{$version}%' LIMIT $limit;");
    }
    // Search by plugin id
    else if (isset($_GET['pid'])) {
        $pid = (int) $_GET['pid'];
	
	$result = DB_query("SELECT id, name, applies_num, version, plugin_id FROM {$tblname} WHERE moderation = '0' AND plugin_id = {$pid};");
    }
    else
    {    
        $result = DB_query("SELECT id, name, applies_num, version, plugin_id FROM {$tblname} WHERE moderation = '0';");
    }
    
    $ds2 = "";
    $i = 0;
    
    while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
$ds2 .= <<<EOM
<tr>
<td class='name'>{$result2['name']} {$result2['applies_num']} {$result2['version']} {$LANG_RMANAGER_UPLUGIN[103]} {$result2['plugin_id']}</td><td class='type'>{$_USER['username']}</td><td class='opt'><a href="index.php?cmd=6&ret=9&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[96]}</a></td><td class="opt"></td><td class='opt'<a href="index.php?cmd=6&ret=10&pid={$result2['id']}">{$LANG_RMANAGER_UPLUGIN[98]}</a></td><td class='opt'></td>
</tr>
EOM;
        $i++;
    }
    
    // No plugins
    if ($i == 0) {
        $ds2 = $LANG_RMANAGER_UPLUGIN[113];
    }
    
    // Set to content
    $data->set_var('value_1', $ds2);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 5)) {
    // Show add maintainer
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'maintainer.thtml'));
    $data->set_var('lang_117', $LANG_RMANAGER_UPLUGIN[117]);
    $data->set_var('lang_118', $LANG_RMANAGER_UPLUGIN[118]);
    $data->set_var('lang_119', $LANG_RMANAGER_UPLUGIN[119]);
    $data->set_var('value_0', (isset($_GET['pid'])) ? $_GET['pid'] : 0);
    $data->set_var('lang_17', $LANG_RMANAGER_UPLUGIN[17]);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
    
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 7)) {
    // Show search plugin
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'search.thtml'));
    $data->set_var('lang_124', $LANG_RMANAGER_UPLUGIN[124]);
    $data->set_var('lang_125', $LANG_RMANAGER_UPLUGIN[125]);
    $data->set_var('lang_126', $LANG_RMANAGER_UPLUGIN[126]);
    $data->set_var('lang_127', $LANG_RMANAGER_UPLUGIN[127]);
    $data->set_var('lang_129', $LANG_RMANAGER_UPLUGIN[129]);    
    $data->set_var('lang_17', $LANG_RMANAGER_UPLUGIN[17]);
    $data->set_var('value_0', 3);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
    
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 8)) {
    // Show search patch
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'search.thtml'));
    $data->set_var('lang_124', $LANG_RMANAGER_UPLUGIN[130]);
    $data->set_var('lang_125', $LANG_RMANAGER_UPLUGIN[125]);
    $data->set_var('lang_126', $LANG_RMANAGER_UPLUGIN[126]);
    $data->set_var('lang_127', $LANG_RMANAGER_UPLUGIN[127]);
    $data->set_var('lang_129', $LANG_RMANAGER_UPLUGIN[129]);    
    $data->set_var('lang_17', $LANG_RMANAGER_UPLUGIN[17]);
    $data->set_var('value_0', 4);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
    
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 6)) {
    // Check for return code
    if ( (isset($_GET['ret'])) and (($_GET['ret'] == 1) or ($_GET['ret'] == 7))) {
        // Download copy of plugin
	// First thing is get all plugin data from the database, so we can make up the file name
	$tblname = $_DB_table_prefix.'repository_listing';
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?msg=101");
            exit();    
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,state FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?msg=102");
            exit();  
	}
	
	// Make up file path
	if ($_GET['cmd'] == 1) {
	    $fpath = "../../../repository/tmp_uploads/".$result2['name'].'_'.$result2['version'].'_'.$result2['state'].'_'.$result2['id'].$result2['ext'];
	}
	else {
	    $fpath = "../../../repository/main/".$result2['name'].'_'.$result2['version'].'_'.$result2['state'].'_'.$result2['id'].$result2['ext'];
	}
	
	// Set it for downloading
	header("Location: $fpath");
    }
    else if ( (isset($_GET['ret'])) and ($_GET['ret'] == 2)) {
        // Approve Patch (Just move it), update DB
	$tblname = $_DB_table_prefix.'repository_listing';
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?msg=101");
            exit();  
   
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,state FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?msg=102");
            exit();  

	}
	
	// Change Database Flag
	DB_query("UPDATE {$tblname} SET moderation = 0 WHERE id = '{$id}';");
	
	// Make up file path
	$fpath = "../../../repository/tmp_uploads/".$result2['name'].'_'.$result2['version'].'_'.$result2['state'].'_'.$result2['id'].$result2['ext'];
        $npath = "../../../repository/main/".$result2['name'].'_'.$result2['version'].'_'.$result2['state'].'_'.$result2['id'].$result2['ext'];
	
        // Move uploaded file 
	if (!(rename($fpath, $npath))) {
            header("Location: index.php?msg=31");
            exit();  

	}
	
	// Display OK message
        header("Location: index.php?msg=104");
        exit();  

	
    }
    else if ( (isset($_GET['ret'])) and (($_GET['ret'] == 3) or ($_GET['ret'] == 8))) {
        // Delete Plugin :D
	$tblname = $_DB_table_prefix.'repository_listing';
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?msg=101");
            exit();  

	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,state FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?msg=102");
            exit();  

	}
	
	// Change Database Flag
	DB_query("DELETE FROM {$tblname} WHERE id = '{$id}';");
	
	// Make up file path
	if ($_GET['cmd'] == 3) {
	    $fpath = "../../../repository/tmp_uploads/".$result2['name'].'_'.$result2['version'].'_'.$result2['state'].'_'.$result2['id'].$result2['ext'];
	}
	else {
	    $fpath = "../../../repository/main/".$result2['name'].'_'.$result2['version'].'_'.$result2['state'].'_'.$result2['id'].$result2['ext'];
	}
	
        // Move uploaded file 
	if (!(unlink($fpath))) {
            header("Location: index.php?msg=106");
            exit();  
 
	}
	
	// Display OK message
        header("Location: index.php?msg=105");
        exit(); 
	
    }
    else if ( (isset($_GET['ret'])) and ($_GET['ret'] == 5)) {
        // Approve Patch :D
	$tblname = $_DB_table_prefix.'repository_patches';
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?msg=101");
            exit(); 
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,applies_num FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?msg=102");
            exit(); 
	}
	
	// Change Database Flag
	DB_query("UPDATE {$tblname} SET moderation = 0 WHERE id = '{$id}';");
	
	// Make up file path
	$fpath = "../../../repository/tmp_uploads/patches/".$result2['name'].'_'.$result2['version'].'_'.$result2['applies_num'].'_'.$result2['id'].$result2['ext'];
	$npath = "../../../repository/main/patches/".$result2['name'].'_'.$result2['version'].'_'.$result2['applies_num'].'_'.$result2['id'].$result2['ext'];
	
        // Move uploaded file 
	if (!(rename($fpath,$npath))) {
            header("Location: index.php?msg=31");
            exit(); 
	}
	
	// Display OK message
        header("Location: index.php?msg=109");
        exit(); 
	
    }
    else if ( (isset($_GET['ret'])) and (($_GET['ret'] == 6) or ($_GET['ret'] == 10))) {
        // Delete Patch :D
	$tblname = $_DB_table_prefix.'repository_patches';
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?msg=101");
            exit();   
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,applies_num FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?msg=102");
            exit(); 
	}
	
	// Change Database Flag
	DB_query("DELETE FROM {$tblname} WHERE id = '{$id}';");
	
	// Make up file path
	if ($_GET['ret'] == 6) {
	    $fpath = "../../../repository/tmp_uploads/patches/".$result2['name'].'_'.$result2['version'].'_'.$result2['applies_num'].'_'.$result2['id'].$result2['ext'];
	}
	else {
	    $fpath = "../../../repository/main/patches/".$result2['name'].'_'.$result2['version'].'_'.$result2['applies_num'].'_'.$result2['id'].$result2['ext'];
	}
	
        // Move uploaded file 
	if (!(unlink($fpath))) {
            header("Location: index.php?msg=108");
            exit(); 
	}
	
	// Display OK message
        header("Location: index.php?msg=107");
        exit(); 
	
    }
    else if ( (isset($_GET['ret'])) and (($_GET['ret'] == 4) or ($_GET['ret'] == 9))) {
        // Download copy of patch
	// First thing is get all patch data from the database, so we can make up the file name
	$tblname = $_DB_table_prefix.'repository_patches';
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?msg=101");
            exit();   
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,applies_num FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?msg=102");
            exit(); 
	}
	
	// Make up file path
	if ($_GET['ret'] == 4) {
	    $fpath = "../../../repository/tmp_uploads/patches/".$result2['name'].'_'.$result2['version'].'_'.$result2['applies_num'].'_'.$result2['id'].$result2['ext'];
	}
	else {
	    $fpath = "../../../repository/main/patches/".$result2['name'].'_'.$result2['version'].'_'.$result2['applies_num'].'_'.$result2['id'].$result2['ext'];
	}
	// Set it for downloading
	header("Location: $fpath");
    }
    else if ((isset($_GET['ret'])) and ($_GET['ret'] == 11)) {
        // Return from add_maintainer
	$username = ( (isset($_POST['GEEKLOG_PLUNAME'])) and (strlen($_POST['GEEKLOG_PLUNAME']) > 1)) ? $_POST['GEEKLOG_PLUNAME'] : false;
        $id = (int)( (isset($_GET['pid'])) ? $_GET['pid'] : 0);

        if ( ($username === FALSE) or ($id == 0)) {
            header("Location: index.php?msg=120");
            exit(); 		
	}
	
	// Does the username even exist?
	$uname = COM_applyFilter($username);
	$tblname = $_DB_table_prefix."users";
	$result = DB_query("SELECT uid FROM {$tblname} WHERE username = '{$uname}';");
	$result2 = DB_fetchArray($result);
	
	// Do they exist?
	if ($result2 === FALSE) {
            header("Location: index.php?msg=121");
            exit();     
	}
	
	$uid = $result2['uid'];
	
	// Are they already a maintainer?
	$tblname = $_DB_table_prefix."repository_maintainers";
	$result = DB_query("SELECT * FROM {$tblname} WHERE maintainer_id = {$uid} AND plugin_id = {$id};");
	
	$result2 = DB_fetchArray($result);
	
	if ($result2 !== FALSE) {
            header("Location: index.php?msg=123");
            exit(); 
	}

        // Insert into maintainer table the plugin id and the user id
	DB_query("INSERT INTO {$tblname}(plugin_id, maintainer_id) VALUES({$id}, {$uid});");
	
        header("Location: index.php?msg=122");
        exit(); 
    }    
}
else {
    // Show link page
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'adminindex.thtml'));
    $data->set_var('lang_94', $LANG_RMANAGER_UPLUGIN[94]);
    $data->set_var('lang_115', $LANG_RMANAGER_UPLUGIN[115]);
    $data->set_var('lang_116', $LANG_RMANAGER_UPLUGIN[116]);
    $data->set_var('lang_128', $LANG_RMANAGER_UPLUGIN[128]); 
    $data->set_var('value_0', $glib);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
} 

 
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    $display .= COM_siteFooter();
    COM_output($display);
?>
