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
require_once $_CONF['path_system'] . 'lib-admin.php';

// Ensure user even has the rights to access this page
if (!SEC_hasRights('repository.manage')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             . COM_siteFooter();

    // Log attempt to access.log
    COM_accessLog("User {$_USER['username']} tried to illegally access the event administration screen.");

    COM_output($display);

    exit;
}

$display = "";
$display .= COM_siteHeader('');
$MAIN_ARRAY_OF_LINKS = array($LANG_RMANAGER_UPLUGIN[115] => 'index.php?cmd=3', $LANG_RMANAGER_UPLUGIN[116] => 'index.php?cmd=4', $LANG_RMANAGER_UPLUGIN[150] => 'index.php?cmd=11');
$SECOND_LINK_ARRAY = array ( array('url' => 'index.php?cmd=3', 'text' =>  $LANG_RMANAGER_UPLUGIN[115]), array('url' => 'index.php?cmd=4',
                          'text' => $LANG_RMANAGER_UPLUGIN[116]), array('url' => 'index.php?cmd=11',
                          'text' => $LANG_RMANAGER_UPLUGIN[150]) );

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

if (isset($_GET['msg'])) {
    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[(int)$_GET['msg']]);
}
else if (isset($_GET['tmsg'])) {
    $display .= ShowTMessageRManager((int)$_GET['tmsg']);
}

$glib = array();
// Are there any plugins to moderate? Lets get that information 
$tblname = $_TABLES['repository_listing'];
$result = DB_query("SELECT count(id) FROM {$tblname} WHERE moderation = '1';");
$result2 = DB_fetchArray($result);
$count_plugins = (int) $result2['count(id)'];

if ($count_plugins > 0) {
    $glib[] = '<b><a href="index.php?cmd=1">'.$LANG_RMANAGER_ADMIN[0].$count_plugins.$LANG_RMANAGER_ADMIN[1].'</a></b>';
   
}

// How about patches
$tblname = $_TABLES['repository_patches'];
$result = DB_query("SELECT count(id) FROM {$tblname} WHERE moderation = '1';");
$result2 = DB_fetchArray($result);
$count_patches = (int) $result2['count(id)'];

if ($count_patches > 0) {
     $glib[] = '<b><a href="index.php?cmd=2">'.$LANG_RMANAGER_ADMIN[0].$count_patches.$LANG_RMANAGER_ADMIN[3].'</a><b/>';
}

// How about upgrades
$tblname = $_TABLES['repository_upgrade'];
$result = DB_query("SELECT count(id) FROM {$tblname} WHERE moderation = '1';");
$result2 = DB_fetchArray($result);
$count_upgrade = (int) $result2['count(id)'];

if ($count_upgrade > 0) {
     $glib[] = '<b><a href="index.php?cmd=2">'.$LANG_RMANAGER_ADMIN[0].$count_upgrade.$LANG_RMANAGER_ADMIN[2].'</a><b/>';
}



// Now we check for a GET parameter - if none, then we simply show them the main link page.
if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 1)) {
    // Show  Moderate Plugins
    $retval = '';
    
    // Set header data
    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG_RMANAGER_UPLUGIN[99], 'field' => 'name', 'sort' => true),
        array('text' => $LANG_RMANAGER_UPLUGIN[100], 'field' => 'uploader', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[96], 'field' => 'download', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[97], 'field' => 'approve', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[98], 'field' => 'delete', 'sort' => false),
    );

    $defsort_arr = array('field' => 'name', 'direction' => 'asc');

    $menu_arr = $SECOND_LINK_ARRAY;

    $retval .= COM_startBlock($LANG_RMANAGER_UPLUGIN[95], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_RMANAGER_UPLUGIN[148],
        $_CONF['layout_url'] . '/images/icons/plugins.' . $_IMAGE_TYPE
    );

    $text_arr = array(
        'has_extras'   => true,
        'instructions' => $LANG_RMANAGER_UPLUGIN[148],
        'form_url'     => 'index.php?cmd=1'
    );
        
    $query_arr = array(
        'table' => 'repository_listing',
        'sql' => "SELECT id, name, version, state, version, uploading_author, moderation FROM {$_TABLES['repository_listing']} WHERE moderation = '1' ",
        'query_fields' => array('name'),
        'default_filter' => ''
    );

    $token = ''; // FIXME: for now ...

    // this is a dummy variable so we know the form has been used if all plugins
    // should be disabled in order to disable the last one.
    $form_arr = array('bottom' => '<input type="hidden" name="pluginenabler" value="true"' . XHTML . '>');

    $retval .= ADMIN_list('plugin_repository', 'ADMIN_getListField_listrepositorypl_plugins', $header_arr,
                $text_arr, $query_arr, $defsort_arr, '', $token, '', $form_arr, false);
                
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    $display .= $retval;    
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 2)) {
    // Show patches waiting for moderation call
    $retval = '';
    
    // Set header data
    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG_RMANAGER_UPLUGIN[99], 'field' => 'name', 'sort' => true),
        array('text' => $LANG_RMANAGER_UPLUGIN[100], 'field' => 'uploader', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[96], 'field' => 'download', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[97], 'field' => 'approve', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[98], 'field' => 'delete', 'sort' => false)
    );

    $defsort_arr = array('field' => 'name', 'direction' => 'asc');

    $menu_arr = $SECOND_LINK_ARRAY;

    $retval .= COM_startBlock($LANG_RMANAGER_UPLUGIN[110], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_RMANAGER_UPLUGIN[148],
        $_CONF['layout_url'] . '/images/icons/plugins.' . $_IMAGE_TYPE
    );

    $text_arr = array(
        'has_extras'   => true,
        'instructions' => $LANG_RMANAGER_UPLUGIN[148],
        'form_url'     => 'index.php?cmd=2'
    );
    
    if (isset($_GET['pid'])) {
        $plugin_id = (int) $_GET['pid'];
        $qstr = "SELECT id, name, applies_num, version, plugin_id, uploading_author, moderation FROM {$_TABLES['repository_patches']} WHERE moderation = '1' AND plugin_id = '{$plugin_id}' ";
    }
    else {
        $qstr = "SELECT id, name, applies_num, version, plugin_id, uploading_author, moderation FROM {$_TABLES['repository_patches']} WHERE moderation = '1' ";
    }
    
    $query_arr = array(
        'table' => 'repository_patches',
        'sql' => $qstr,
        'query_fields' => array('name'),
        'default_filter' => ''
    );

    // this is a dummy variable so we know the form has been used if all plugins
    // should be disabled in order to disable the last one.
    $form_arr = array('bottom' => '<input type="hidden" name="pluginenabler" value="true"' . XHTML . '>');

    $retval .= ADMIN_list('plugin_repository', 'ADMIN_getListField_listrepositorypl_patches', $header_arr,
                $text_arr, $query_arr, $defsort_arr, '', $token, '', $form_arr, false);
                
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    $display .= $retval;     

}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 22)) {
    // 
    $retval = '';

    // Set header data
    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG_RMANAGER_UPLUGIN[99], 'field' => 'name', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[96], 'field' => 'download', 'sort' => false), 
        array('text' => $LANG_RMANAGER_UPLUGIN[97], 'field' => 'approve', 'sort' => false), 
        array('text' => $LANG_RMANAGER_UPLUGIN[98], 'field' => 'delete', 'sort' => false)
    );

    $defsort_arr = array('field' => 'id', 'direction' => 'asc');

    $menu_arr = $SECOND_LINK_ARRAY;

    $retval .= COM_startBlock($LANG_RMANAGER_UPLUGIN[155], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_RMANAGER_UPLUGIN[148],
        $_CONF['layout_url'] . '/images/icons/plugins.' . $_IMAGE_TYPE
    );

    $text_arr = array(
        'has_extras'   => true,
        'instructions' => $LANG_RMANAGER_UPLUGIN[148],
        'form_url'     => 'index.php?cmd=22'
    );

    if (isset($_GET['pid'])) {
        $plugin_id = (int) $_GET['pid'];
        $qstr = "SELECT id, plugin_id, version, version2, moderation FROM {$_TABLES['repository_upgrade']} WHERE moderation = '1' AND plugin_id = '{$plugin_id}' ";
    }
    else {
        $qstr = "SELECT id, plugin_id, version, version2, moderation FROM {$_TABLES['repository_upgrade']} WHERE moderation = '1' ";
    }
    
    $query_arr = array(
        'table' => 'repository_upgrade',
        'sql' => $qstr,
        'query_fields' => array('name'),
        'default_filter' => ''
    );

    $token = ''; // FIXME: for now ...

    // this is a dummy variable so we know the form has been used if all plugins
    // should be disabled in order to disable the last one.
    $form_arr = array('bottom' => '<input type="hidden" name="pluginenabler" value="true"' . XHTML . '>');

    $retval .= ADMIN_list('plugin_repository', 'ADMIN_getListField_listrepositorypl_upgrade', $header_arr,
                $text_arr, $query_arr, $defsort_arr, '', $token, '', $form_arr, false);
                
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    $display .= $retval; 

}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 3)) {
    //  Show all plugins
    
    $retval = '';
    
    // Set header data
    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG_RMANAGER_UPLUGIN[99], 'field' => 'name', 'sort' => true),
        array('text' => $LANG_RMANAGER_UPLUGIN[100], 'field' => 'uploader', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[96], 'field' => 'download', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[111], 'field' => 'add_maintainer', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[98], 'field' => 'delete', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[131], 'field' => 'show_patches', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[151], 'field' => 'show_upgrades', 'sort' => false) // 151 is also a good tasting alcohol :)
    );

    $defsort_arr = array('field' => 'name', 'direction' => 'asc');

    $menu_arr = $SECOND_LINK_ARRAY;

    $retval .= COM_startBlock($LANG_RMANAGER_UPLUGIN[112], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_RMANAGER_UPLUGIN[148],
        $_CONF['layout_url'] . '/images/icons/plugins.' . $_IMAGE_TYPE
    );

    $text_arr = array(
        'has_extras'   => true,
        'instructions' => $LANG_RMANAGER_UPLUGIN[148],
        'form_url'     => 'index.php?cmd=3'
    );
        
    $query_arr = array(
        'table' => 'repository_listing',
        'sql' => "SELECT id, name, version, state, version, uploading_author, moderation FROM {$_TABLES['repository_listing']} WHERE moderation = '0' ",
        'query_fields' => array('name'),
        'default_filter' => ''
    );

    $token = ''; // FIXME: for now ...

    // this is a dummy variable so we know the form has been used if all plugins
    // should be disabled in order to disable the last one.
    $form_arr = array('bottom' => '<input type="hidden" name="pluginenabler" value="true"' . XHTML . '>');

    $retval .= ADMIN_list('plugin_repository', 'ADMIN_getListField_listrepositorypl_plugins', $header_arr,
                $text_arr, $query_arr, $defsort_arr, '', $token, '', $form_arr, false);
                
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    $display .= $retval;
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 4)) {
    //  Show all patches
   
    
    $retval = '';
    
    // Set header data
    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG_RMANAGER_UPLUGIN[99], 'field' => 'name', 'sort' => true),
        array('text' => $LANG_RMANAGER_UPLUGIN[100], 'field' => 'uploader', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[96], 'field' => 'download', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[98], 'field' => 'delete', 'sort' => false)
    );

    $defsort_arr = array('field' => 'name', 'direction' => 'asc');

    $menu_arr = $SECOND_LINK_ARRAY;

    $retval .= COM_startBlock($LANG_RMANAGER_UPLUGIN[114], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_RMANAGER_UPLUGIN[148],
        $_CONF['layout_url'] . '/images/icons/plugins.' . $_IMAGE_TYPE
    );

    $text_arr = array(
        'has_extras'   => true,
        'instructions' => $LANG_RMANAGER_UPLUGIN[148],
        'form_url'     => 'index.php?cmd=4'
    );
    
    if (isset($_GET['pid'])) {
        $plugin_id = (int) $_GET['pid'];
        $qstr = "SELECT id, name, applies_num, version, plugin_id, uploading_author, moderation FROM {$_TABLES['repository_patches']} WHERE moderation = '0' AND plugin_id = '{$plugin_id}' ";
    }
    else {
        $qstr = "SELECT id, name, applies_num, version, plugin_id, uploading_author, moderation FROM {$_TABLES['repository_patches']} WHERE moderation = '0' ";
    }
    
    $query_arr = array(
        'table' => 'repository_patches',
        'sql' => $qstr,
        'query_fields' => array('name'),
        'default_filter' => ''
    );

    $token = ''; // FIXME: for now ...

    // this is a dummy variable so we know the form has been used if all plugins
    // should be disabled in order to disable the last one.
    $form_arr = array('bottom' => '<input type="hidden" name="pluginenabler" value="true"' . XHTML . '>');

    $retval .= ADMIN_list('plugin_repository', 'ADMIN_getListField_listrepositorypl_patches', $header_arr,
                $text_arr, $query_arr, $defsort_arr, '', $token, '', $form_arr, false);
                
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    $display .= $retval; 
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 11)) {
    //  Show all upgrades
   
    
    $retval = '';

    // Set header data
    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG_RMANAGER_UPLUGIN[99], 'field' => 'name', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[96], 'field' => 'download', 'sort' => false), 
        array('text' => $LANG_RMANAGER_UPLUGIN[98], 'field' => 'delete', 'sort' => false)
    );

    $defsort_arr = array('field' => 'id', 'direction' => 'asc');

    $menu_arr = $SECOND_LINK_ARRAY;

    $retval .= COM_startBlock($LANG_RMANAGER_UPLUGIN[149], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $retval .= ADMIN_createMenu(
        $menu_arr,
        $LANG_RMANAGER_UPLUGIN[148],
        $_CONF['layout_url'] . '/images/icons/plugins.' . $_IMAGE_TYPE
    );

    $text_arr = array(
        'has_extras'   => true,
        'instructions' => $LANG_RMANAGER_UPLUGIN[148],
        'form_url'     => 'index.php?cmd=11'
    );

    if (isset($_GET['pid'])) {
        $plugin_id = (int) $_GET['pid'];
        $qstr = "SELECT id, plugin_id, version, version2, moderation FROM {$_TABLES['repository_upgrade']} WHERE moderation = '0' AND plugin_id = '{$plugin_id}' ";
    }
    else {
        $qstr = "SELECT id, plugin_id, version, version2, moderation FROM {$_TABLES['repository_upgrade']} WHERE moderation = '0' ";
    }
    
    $query_arr = array(
        'table' => 'repository_upgrade',
        'sql' => $qstr,
        'query_fields' => array('name'),
        'default_filter' => ''
    );

    $token = ''; // FIXME: for now ...

    // this is a dummy variable so we know the form has been used if all plugins
    // should be disabled in order to disable the last one.
    $form_arr = array('bottom' => '<input type="hidden" name="pluginenabler" value="true"' . XHTML . '>');

    $retval .= ADMIN_list('plugin_repository', 'ADMIN_getListField_listrepositorypl_upgrade', $header_arr,
                $text_arr, $query_arr, $defsort_arr, '', $token, '', $form_arr, false);
                
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    $display .= $retval; 
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 5)) {
    // Show add maintainer / delete maintainers
    $data = new Template($_CONF['path'].'plugins/repository/templates');
    $data->set_file(array('index'=>'maintainer.thtml'));
    $data->set_var('store_0', tdisplay_formattedmessage(NULL, $LANG_RMANAGER_UPLUGIN[117], FALSE, TRUE, $MAIN_ARRAY_OF_LINKS));
    $data->set_var('lang_118', $LANG_RMANAGER_UPLUGIN[118]);
    $data->set_var('lang_119', $LANG_RMANAGER_UPLUGIN[119]);
    $data->set_var('lang_152', $LANG_RMANAGER_UPLUGIN[152]);
    $data->set_var('value_0', (isset($_GET['pid'])) ? $_GET['pid'] : 0);
    $data->set_var('lang_17', $LANG_RMANAGER_UPLUGIN[17]);
    $data->parse('output','index');
    $display .= $data->finish($data->get_var('output'));   
    
    $retval = '';

    // Set header data
    $header_arr = array(      # display 'text' and use table field 'field'
        array('text' => $LANG01[21], 'field' => 'name', 'sort' => false),
        array('text' => $LANG_RMANAGER_UPLUGIN[98], 'field' => 'delete', 'sort' => false)
    );

    $defsort_arr = array();

    $retval .= COM_startBlock('', '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $text_arr = array(
        'has_extras'   => false,
        'instructions' => $LANG_RMANAGER_UPLUGIN[148],
        'form_url'     => ''
    );

    $plugin_id = (int) $_GET['pid'];
    $qstr = "SELECT maintainer_id, plugin_id FROM {$_TABLES['repository_maintainers']} WHERE plugin_id = '{$plugin_id}' ";
    
    $query_arr = array(
        'table' => 'repository_upgrade',
        'sql' => $qstr,
        'query_fields' => array('maintainer_id'),
        'default_filter' => ''
    );

    // this is a dummy variable so we know the form has been used if all plugins
    // should be disabled in order to disable the last one.
    $form_arr = array('bottom' => '<input type="hidden" name="pluginenabler" value="true"' . XHTML . '>');

    $retval .= ADMIN_list('plugin_repository', 'ADMIN_getListField_listrepositorypl_maintainers', $header_arr,
                $text_arr, $query_arr, $defsort_arr, '', $token, '', $form_arr, false);
                
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    $display .= $retval;     
    
    
}
else if ( (isset($_GET['cmd'])) and ($_GET['cmd'] == 6)) {
    // Check for return code
    if ( (isset($_GET['ret'])) and (($_GET['ret'] == 1) or ($_GET['ret'] == 7))) {
        // Download copy of plugin
	// First thing is get all plugin data from the database, so we can make up the file name
	$tblname = $_TABLES['repository_listing'];
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?cmd=3&msg=101");
            exit();    
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,state FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?cmd=3&msg=102");
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
        // Approve Plugin (Just move it), update DB
	$tblname = $_TABLES['repository_listing'];
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?cmd=3&msg=101");
            exit();  
   
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,state FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?cmd=3&msg=102");
            exit();  

	}
	
	// Change Database Flag
	DB_query("UPDATE {$tblname} SET moderation = 0 WHERE id = '{$id}';");
	
	// Make up file path
	$fpath = "../../../repository/tmp_uploads/".$result2['name'].'_'.$result2['version'].'_'.$result2['state'].'_'.$result2['id'].$result2['ext'];
        $npath = "../../../repository/main/".$result2['name'].'_'.$result2['version'].'_'.$result2['state'].'_'.$result2['id'].$result2['ext'];
	
        // Move uploaded file 
	if (!(rename($fpath, $npath))) {
            header("Location: index.php?cmd=3&msg=31");
            exit();  

	}
	
	// Display OK message
        header("Location: index.php?cmd=3&msg=104");
        exit();  

	
    }
    else if ( (isset($_GET['ret'])) and (($_GET['ret'] == 3) or ($_GET['ret'] == 8))) {
        // Delete Plugin :D
	$tblname = $_TABLES['repository_listing'];
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?cmd=3&msg=101");
            exit();  

	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,state FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?cmd=3&msg=102");
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
            header("Location: index.php?cmd=3&msg=106");
            exit();  
 
	}
	
	// Display OK message
        header("Location: index.php?cmd=3&msg=105");
        exit(); 
	
    }
    else if ( (isset($_GET['ret'])) and ($_GET['ret'] == 5)) {
        // Approve Patch :D
	$tblname = $_TABLES['repository_patches'];
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?cmd=4&msg=101");
            exit(); 
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,applies_num FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?cmd=4&msg=102");
            exit(); 
	}
	
	// Change Database Flag
	DB_query("UPDATE {$tblname} SET moderation = 0 WHERE id = '{$id}';");
	
	// Make up file path
	$fpath = "../../../repository/tmp_uploads/patches/".$result2['name'].'_'.$result2['version'].'_'.$result2['applies_num'].'_'.$result2['id'].$result2['ext'];
	$npath = "../../../repository/main/patches/".$result2['name'].'_'.$result2['version'].'_'.$result2['applies_num'].'_'.$result2['id'].$result2['ext'];
	
        // Move uploaded file 
	if (!(rename($fpath,$npath))) {
            header("Location: index.php?cmd=4&msg=31");
            exit(); 
	}
	
	// Display OK message
        header("Location: index.php?cmd=4&msg=109");
        exit(); 
	
    }
    else if ( (isset($_GET['ret'])) and (($_GET['ret'] == 6) or ($_GET['ret'] == 10))) {
        // Delete Patch :D
	$tblname = $_TABLES['repository_patches'];
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?cmd=4&msg=101");
            exit();   
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,applies_num FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?cmd=4&msg=102");
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
            header("Location: index.php?cmd=4&msg=108");
            exit(); 
	}
	
	// Display OK message
        header("Location: index.php?cmd=4&msg=107");
        exit(); 
	
    }
    else if ( (isset($_GET['ret'])) and (($_GET['ret'] == 4) or ($_GET['ret'] == 9))) {
        // Download copy of patch
	// First thing is get all patch data from the database, so we can make up the file name
	$tblname = $_TABLES['repository_patches'];
	$id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
	
	if ($id === 0) {
            header("Location: index.php?cmd=4&msg=101");
            exit();   
	}
	
	// Run DB query, get information
	$result = DB_query("SELECT id,name,version,ext,applies_num FROM {$tblname} WHERE id = '{$id}';");
	
	$result2 = DB_fetchArray($result);
	
	// If the plugin doesn't exist, raise error
	if ($result2 === FALSE) {
            header("Location: index.php?cmd=4&msg=102");
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
            header("Location: index.php?cmd=5&pid={$id}&msg=120");
            exit(); 		
	}
	
	// Does the username even exist?
	$uname = COM_applyFilter($username);
	$tblname = $_TABLES["users"];
	$result = DB_query("SELECT uid FROM {$tblname} WHERE username = '{$uname}';");
	$result2 = DB_fetchArray($result);
	
	// Do they exist?
	if ($result2 === FALSE) {
            header("Location: index.php?cmd=5&pid={$id}&msg=121");
            exit();     
	}
	
	$uid = $result2['uid'];
	
	// Are they already a maintainer?
	$tblname = $_TABLES["repository_maintainers"];
	$result = DB_query("SELECT * FROM {$tblname} WHERE maintainer_id = {$uid} AND plugin_id = {$id};");
	
	$result2 = DB_fetchArray($result);
	
	if ($result2 !== FALSE) {
            header("Location: index.php?cmd=5&pid={$id}&msg=123");
            exit(); 
	}

        // Insert into maintainer table the plugin id and the user id
	DB_query("INSERT INTO {$tblname}(plugin_id, maintainer_id) VALUES({$id}, {$uid});");
	
        header("Location: index.php?cmd=5&pid={$id}&msg=122");
        exit(); 
    }
    else if ((isset($_GET['ret'])) and ($_GET['ret'] == 14)) {
        // Return from delete maintainer
        // Two variables to catch, the plugin_id and the maintainer_id        
        $mid = (int)( (isset($_GET['mid'])) ? $_GET['mid'] : 0);
        $pid = (int)( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
        
        // Attempt to delete the maintainer with that name
        DB_query("DELETE FROM {$_TABLES["repository_maintainers"]} WHERE maintainer_id = '{$mid}' AND plugin_id = '{$pid}';");
        
        header("Location: index.php?cmd=5&pid={$pid}&msg=153");
        exit();
    }
    else if ( (isset($_GET['ret'])) and ($_GET['ret'] == 20)) {
        // Approve Upgrade (Just move it), update DB
        $tblname = $_TABLES['repository_upgrade'];
        $id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
        
        if ($id === 0) {
            header("Location: index.php?cmd=11&pid={$id}&msg=101");
            exit();  
   
        }
        
        // Run DB query, get information
        $result = DB_query("SELECT plugin_id, version, version2, ext FROM {$tblname} WHERE id = '{$id}';");
        
        $result2 = DB_fetchArray($result);
        
        // If the plugin doesn't exist, raise error
        if ($result2 === FALSE) {
            header("Location: index.php?cmd=11&pid={$id}&msg=102");
            exit();  

        }
        
        // Change Database Flag
        DB_query("UPDATE {$tblname} SET moderation = 0 WHERE id = '{$id}';");

        // Make up file path
        $fpath = "../../../repository/tmp_uploads/upgrades/".$result2['version'].'_from_'.$result2['version2'].'_'.$result2['plugin_id'].'_'.$id.$result2['ext'];
        $npath = "../../../repository/main/upgrades/".$result2['version'].'_from_'.$result2['version2'].'_'.$result2['plugin_id'].'_'.$id.$result2['ext'];
        
        // Move uploaded file 
        if (!(rename($fpath, $npath))) {
            header("Location: index.php?cmd=11&pid={$id}&msg=31");
            exit();  

        }
        
        // Display OK message
        header("Location: index.php?cmd=11&pid={$id}&msg=154");
        exit();  

        
    }
    else if ( (isset($_GET['ret'])) and (($_GET['ret'] == 12) or ($_GET['ret'] == 21))) {
        // Download copy of upgrade
        // First thing is get all upgrade data from the database, so we can make up the file name
        $tblname = $_TABLES['repository_upgrade'];
        $id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
        
        if ($id === 0) {
            header("Location: index.php?cmd=11&pid={$id}&msg=101");
            exit();    
        }
        
         // Run DB query, get information
        $result = DB_query("SELECT plugin_id, version, version2, ext FROM {$tblname} WHERE id = '{$id}';");
        
        $result2 = DB_fetchArray($result);
        
        // If the plugin doesn't exist, raise error
        if ($result2 === FALSE) {
            header("Location: index.php?cmd=11&pid={$id}&msg=102");
            exit();  

        }
        
        // Make up file path
        if ($_GET['cmd'] == 21) {
            $fpath = "../../../repository/tmp_uploads/upgrades/".$result2['version'].'_from_'.$result2['version2'].'_'.$result2['plugin_id'].'_'.$id.$result2['ext'];
        }
        else {
            $fpath = "../../../repository/main/upgrades/".$result2['version'].'_from_'.$result2['version2'].'_'.$result2['plugin_id'].'_'.$id.$result2['ext'];
        }
        
        // Set it for downloading
        header("Location: $fpath");
    }
    else if ( (isset($_GET['ret'])) and (($_GET['ret'] == 13) or ($_GET['ret'] == 23))) {
        // Delete upgrade
        // First thing is get all upgrade data from the database, so we can make up the file name
        $tblname = $_TABLES['repository_upgrade'];
        $id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);
        
        if ($id === 0) {
            header("Location: index.php?cmd=11&pid={$id}&msg=101");
            exit();    
        }
        
        // Run DB query, delete plugin
        $result = DB_query("SELECT plugin_id, version, version2, ext FROM {$tblname} WHERE id = '{$id}';");
        
        $result2 = DB_fetchArray($result);
        
        // If the plugin doesn't exist, raise error
        if ($result2 === FALSE) {
            header("Location: index.php?cmd=11&pid={$id}&msg=102");
            exit();  

        }
        
        // Now try deleting from database
        DB_query("DELETE FROM {$tblname} WHERE id = '{$id}';");
        
        // Make up file path
        if ($_GET['cmd'] == 23) {
            $fpath = "../../../repository/tmp_uploads/upgrades/".$result2['version'].'_from_'.$result2['version2'].'_'.$result2['plugin_id'].'_'.$id.$result2['ext'];
        }
        else {
            $fpath = "../../../repository/main/upgrades/".$result2['version'].'_from_'.$result2['version2'].'_'.$result2['plugin_id'].'_'.$id.$result2['ext'];
        }
        
        // Deleted file 
        if (!(unlink($fpath))) {
            header("Location: index.php?cmd=11&pid={$id}&msg=106");
            exit();  
 
        }        
    }

}
else {
    // Show link page
    // Check to see if any need moderation
    if (count($glib) > 0) {
        $tmgd2 = $glib;
    }
    else {
        $tmgd2 = null;
    }
    
    $display .= tdisplay_formattedmessage($tmgd2, $LANG_RMANAGER_UPLUGIN[94], TRUE, TRUE, $MAIN_ARRAY_OF_LINKS);   
} 

 
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    $display .= COM_siteFooter();
    COM_output($display);
?>
