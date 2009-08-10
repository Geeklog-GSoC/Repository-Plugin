<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Repository Management                                                     |
// +---------------------------------------------------------------------------+
// | pupload.php                                                               |
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

require_once '../lib-common.php';

ini_set('upload_max_filesize', $_RM_CONF['max_pluginpatch_upload']); 

// If it is a moderator / admin, there is no point in having the plugins uploaded to be moderated since they would do that. Hence it is authenticated directly.
if (SEC_isModerator() == TRUE) {
    $_RM_CONF['repository_moderated'] = 0;
}

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


$display = '';

// Is anonymous user, which means they have not logged in, which means they cannot access the page, which means that they get brought to a login page, 
// which means they are told to login or register, which means Tim is happy
if (COM_isAnonUser()) {
    $display .= COM_siteHeader('');
    $display .= COM_startBlock ($LANG_LOGIN[1], '',
                                COM_getBlockTemplate ('_msg_block', 'header'));
    $login = new Template($_CONF['path_layout'] . 'submit');
    $login->set_file (array ('login'=>'submitloginrequired.thtml'));
    $login->set_var ( 'xhtml', XHTML );
    $login->set_var ('login_message', $LANG_LOGIN[2]);
    $login->set_var ('site_url', $_CONF['site_url']);
    $login->set_var ('site_admin_url', $_CONF['site_admin_url']);
    $login->set_var ('layout_url', $_CONF['layout_url']);
    $login->set_var ('lang_login', $LANG_LOGIN[3]);
    $login->set_var ('lang_newuser', $LANG_LOGIN[4]);
    $login->parse ('output', 'login');
    $display .= $login->finish ($login->get_var('output'));
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    $display .= COM_siteFooter();
    COM_output($display);
    exit;
}
// Ensure user even has the rights to access this page
else if (!SEC_hasRights('repository.upload')) {
    $display .= COM_siteHeader('menu', $MESSAGE[30])
             . COM_showMessageText($MESSAGE[29], $MESSAGE[30])
             . COM_siteFooter();

   COM_output($display);

    exit;
}

$display .= COM_siteHeader('');
//$display .= COM_startBlock($LANG_RMANAGER['title'], '', COM_getBlockTemplate('_msg_block', 'header')); 
if ($_GET['msg']) {
    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[(int)$_GET['msg']]);
}
else if ($_GET['tmsg']) {
    $display .= ShowTMessageRManager((int)$_GET['tmsg']);
}

// So if the user got this far they are logged in, which is great

// What command do we have now?
if (isset($_GET['cmd'])) {
    // a command page, not the reply

    if ($_GET['cmd'] == 1) {
        $data = new Template($_CONF['path'].'plugins/repository/templates');
	$data->set_file(array('index'=>'uploadplugin.thtml'));
	// This instruction sets the javascript language variables
	$display .= "<script type='text/javascript'>
	var LANG_PLUPLOAD_MSG0 = '".$LANG_RMANAGER_UPLUGIN[18]."';
	var LANG_PLUPLOAD_MSG1 = '".$LANG_RMANAGER_UPLUGIN[19]."';
	var LANG_PLUPLOAD_MSG2 = '".$LANG_RMANAGER_UPLUGIN[20]."';
	var LANG_PLUPLOAD_MSG3 = '".$LANG_RMANAGER_UPLUGIN[21]."';
	var LANG_PLUPLOAD_MSG4 = '".$LANG_RMANAGER_UPLUGIN[22]."';
	</script>";
	
	// Get repositories from database	
        $data->set_var('store_0', tdisplay_formattedmessage(NULL,$LANG_RMANAGER_UPLUGIN[0],FALSE));
	$data->set_var('lang_1', $LANG_RMANAGER_UPLUGIN[1]);
	$data->set_var('lang_2', $LANG_RMANAGER_UPLUGIN[2]);
	$data->set_var('lang_3', $LANG_RMANAGER_UPLUGIN[3]);
	$data->set_var('lang_4', $LANG_RMANAGER_UPLUGIN[4]);
	$data->set_var('lang_5', $LANG_RMANAGER_UPLUGIN[5]);
	$data->set_var('lang_6', $LANG_RMANAGER_UPLUGIN[6]);
	$data->set_var('lang_7', $LANG_RMANAGER_UPLUGIN[7]);
	$data->set_var('lang_8', $LANG_RMANAGER_UPLUGIN[8]);
	$data->set_var('lang_9', $LANG_RMANAGER_UPLUGIN[9]);
	$data->set_var('lang_10', $LANG_RMANAGER_UPLUGIN[10]);
	$data->set_var('lang_11', $LANG_RMANAGER_UPLUGIN[11]);
	$data->set_var('lang_12', $LANG_RMANAGER_UPLUGIN[12]);
	$data->set_var('lang_13', $LANG_RMANAGER_UPLUGIN[13]);
	$data->set_var('lang_15', $LANG_RMANAGER_UPLUGIN[15]);
	$data->set_var('lang_16', $LANG_RMANAGER_UPLUGIN[16]);
	$data->set_var('lang_17', $LANG_RMANAGER_UPLUGIN[17]);
        $data->set_var('lang_26', $LANG_RMANAGER_UPLUGIN[26]);
        $data->set_var('lang_27', $LANG_RMANAGER_UPLUGIN[27]);
        $data->set_var('lang_28', $LANG_RMANAGER_UPLUGIN[28]);
        $data->set_var('lang_32', $LANG_RMANAGER_UPLUGIN[32]);
	$data->set_var('lang_33', $LANG_RMANAGER_UPLUGIN[33]);
        $data->set_var('lang_34', $LANG_RMANAGER_UPLUGIN[34]);
        $data->set_var('lang_35', $LANG_RMANAGER_UPLUGIN[35]);
        $data->set_var('lang_36', $LANG_RMANAGER_UPLUGIN[36]);
        $data->set_var('lang_37', $LANG_RMANAGER_UPLUGIN[37]);
        $data->set_var('lang_38', $LANG_RMANAGER_UPLUGIN[38]);
        $data->set_var('lang_132', $LANG_RMANAGER_UPLUGIN[132]);
	$data->parse('output','index');
	$display .= $data->finish($data->get_var('output'));
  
    }
    else if ($_GET['cmd'] == 2) {
        // Listing of all plugins assigned to your name OR set to moderate
        $data = new Template($_CONF['path'].'plugins/repository/templates');
	$data->set_file(array('index'=>'listplugins.thtml'));
        $display .= tdisplay_formattedmessage(NULL,$LANG_RMANAGER_DPLUGIN[0], FALSE);
        
        $tblname = $_TABLES['repository_maintainers'];
        // Lets get the list of plugin ids from the db that work with the client id
        $result = DB_query("SELECT plugin_id FROM {$tblname} WHERE maintainer_id = '{$_USER['uid']}';");
        $array_of_plugins = array();        

        // Loop through the results, and store the plugin ids in an array
        while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
            $array_of_plugins[] = $result2['plugin_id'];
        }

        $string_of_maintainer_code = "";

        // Get the plugins that match those ID's - These are the maintainer plugins
        $tblname = $_TABLES['repository_listing'];
        foreach ($array_of_plugins as $value) {
            $result = DB_query("SELECT name FROM {$tblname} WHERE id = '{$value}';");
            $result2 = DB_fetchArray($result);
            $string_of_maintainer_code .= "<tr><td class='name'>{$result2['name']}</td><td class='type'> {$LANG_RMANAGER_UPLUGIN[81]}</td><td class='opt'><a href='pupload.php?cmd=4&pid={$value}'> {$LANG_RMANAGER_UPLUGIN[77]} </a></td><td class='opt'></td><td class='opt'><a href='pupload.php?cmd=5&id={$value}'> {$LANG_RMANAGER_UPLUGIN[79]} </a></td><td class='opt'><a href='pupload.php?cmd=6&id={$result2['id']}'> {$LANG_RMANAGER_UPLUGIN[80]} </a></td></tr>";
        }

        // Now get the plugins that were uploaded by the user
        $result = DB_query("SELECT name,id FROM {$tblname} WHERE uploading_author = '{$_USER['uid']}';");
        $string_of_author_code = "";        

        while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
            $string_of_author_code .= "<tr><td class='name'>{$result2['name']}</td><td class='type'> {$LANG_RMANAGER_UPLUGIN[82]} </td><td class='opt'><a href='pupload.php?cmd=4&pid={$result2['id']}'> {$LANG_RMANAGER_UPLUGIN[77]} </a></td>
        <td class='opt'><a href='javascript:void();' onclick=\"javascript:delconfirm('pupload.php?cmd=3&pid={$result2['id']}', '{$LANG_RMANAGER_UPLUGIN[138]}');\"> {$LANG_RMANAGER_UPLUGIN[78]} </a></td><td class='opt'><a href='pupload.php?cmd=5&id={$result2['id']}'> {$LANG_RMANAGER_UPLUGIN[79]} </a></td><td class='opt'><a href='pupload.php?cmd=6&id={$result2['id']}'> {$LANG_RMANAGER_UPLUGIN[80]} </a></td></tr>";
        }
	
	// Was there any data
	if (($string_of_author_code == "") and ($string_of_maintainer_code == "")) {
            $data->set_var('lang_1', '<br /><br />'.$LANG_RMANAGER_UPLUGIN[83]);
            $data->set_var('lang_2', "");
            $data->parse('output','index');
	    $display .= $data->finish($data->get_var('output'));
	    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            $display .= COM_siteFooter();
            COM_output($display);
            exit();   	
	}
	else {
            $data->set_var('lang_1', $string_of_author_code);
            $data->set_var('lang_2', $string_of_maintainer_code);
	}
	
        $data->set_var('lang_84', $LANG_RMANAGER_UPLUGIN[84]);
        $data->set_var('lang_85', $LANG_RMANAGER_UPLUGIN[85]);
        $data->set_var('lang_86', $LANG_RMANAGER_UPLUGIN[86]);
        $data->set_var('lang_87', $LANG_RMANAGER_UPLUGIN[87]);
        $data->set_var('lang_88', $LANG_RMANAGER_UPLUGIN[88]);
        $data->set_var('lang_89', $LANG_RMANAGER_UPLUGIN[89]);
	$data->parse('output','index');
	$display .= $data->finish($data->get_var('output'));
         
    }
    else if ($_GET['cmd'] == 3) {
        // Delete Plugin :D hahahahahahaha
        // First things first - the user had better be the one who uploaded it, otherwise he gonna be kicked out
        $p_id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);

        // Check and make sure the plugin id is not 0. If it is 0, then lets throw an error and get out
        if ($p_id == 0) {
            header("Location: index.php?msg=133");
            exit();
        }

        // Get the author ID associated with that plugin id, make sure they match 
        $tblname = $_TABLES['repository_listing'];
        $result = DB_query("SELECT state,uploading_author,name,version,id,ext FROM {$tblname} WHERE id = '{$p_id}';");
        $author_id = DB_fetchArray($result);
        
        // If it is NULL, it means that the id entered was invalid (plugin does not exist)
        if ($author_id == NULL) {
            header("Location: index.php?msg=133");
            exit(); 
        }
        else if ($author_id['uploading_author'] !== $_USER['uid']) {
            header("Location: index.php?msg=134");
            exit(); 
        }

        // Obviously it is authenticated, lets delete the entry from the database (MUNCH)
        // But first, get the file path so we can delete it after the database has been deleted
        $filepath = $author_id['name'].'_'.$author_id['version'].'_'.$author_id['state'].'_'.$author_id['id'].$author_id['ext'];
        
        DB_query("DELETE FROM {$tblname} WHERE id = '{$author_id['id']}';");
        
        // Now remove from the repository listing
        $rmfile = unlink("main/".$filepath);

        // Did it fail? 
        if ($rmfile === FALSE) {
            header("Location: index.php?tmsg=135&enable_spf=1&file=main/{$filepath}");
            exit();
        }
        else {
           // Since everything has succeeded successfully, display any files that should be included, exit
           header("Location: pupload.php?cmd=2&msg=43");
           exit;
        }
 
    }
    else if ($_GET['cmd'] == 4) {
        // Edit the plugin data now.. 
        // So get data, and make sure ID is ok
        $p_id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);

        // Check and make sure the plugin id is not 0. If it is 0, then lets throw an error and get out
        if ($p_id == 0) {
            header("Location: index.php?msg=133");
            exit();
        }

        // Get the data for this plugin
        $tblname = $_TABLES['repository_listing'];
        $result = DB_query("SELECT * FROM {$tblname} WHERE id = '{$p_id}';");
        $row = DB_fetchArray($result);
        
        // If it is NULL, it means that the id entered was invalid (plugin does not exist)
        if ($row == FALSE) {
            header("Location: index.php?msg=133");
            exit();        
        }

        // Now we can load the template, adding the required fields in place
        $display .= "<script type='text/javascript'>
        var LANG_PLUPLOAD_MSG0 = '".$LANG_RMANAGER_UPLUGIN[18]."';
        var LANG_PLUPLOAD_MSG1 = '".$LANG_RMANAGER_UPLUGIN[19]."';
        var LANG_PLUPLOAD_MSG2 = '".$LANG_RMANAGER_UPLUGIN[20]."';
        var LANG_PLUPLOAD_MSG3 = '".$LANG_RMANAGER_UPLUGIN[21]."';
        var LANG_PLUPLOAD_MSG4 = '".$LANG_RMANAGER_UPLUGIN[22]."';
        </script>";
        
        // Get repositories from database       
        $data = new Template($_CONF['path'].'plugins/repository/templates');
        $data->set_file(array('index'=>'editplugin.thtml'));
        $data->set_var('store_0', tdisplay_formattedmessage(NULL,$LANG_RMANAGER_UPLUGIN[75],FALSE));
        $data->set_var('lang_1', $LANG_RMANAGER_UPLUGIN[1]);
        $data->set_var('lang_44', $LANG_RMANAGER_UPLUGIN[2]);
        $data->set_var('lang_3', $LANG_RMANAGER_UPLUGIN[3]);
        $data->set_var('lang_4', $LANG_RMANAGER_UPLUGIN[4]);
        $data->set_var('lang_5', $LANG_RMANAGER_UPLUGIN[5]);
        $data->set_var('lang_6', $LANG_RMANAGER_UPLUGIN[6]);
        $data->set_var('lang_7', $LANG_RMANAGER_UPLUGIN[7]);
        $data->set_var('lang_8', $LANG_RMANAGER_UPLUGIN[8]);
        $data->set_var('lang_9', $LANG_RMANAGER_UPLUGIN[9]);
        $data->set_var('lang_10', $LANG_RMANAGER_UPLUGIN[10]);
        $data->set_var('lang_11', $LANG_RMANAGER_UPLUGIN[11]);
        $data->set_var('lang_13', $LANG_RMANAGER_UPLUGIN[13]);
        $data->set_var('lang_15', $LANG_RMANAGER_UPLUGIN[15]);
        $data->set_var('lang_16', $LANG_RMANAGER_UPLUGIN[16]);
        $data->set_var('lang_17', $LANG_RMANAGER_UPLUGIN[17]);
        $data->set_var('lang_26', $LANG_RMANAGER_UPLUGIN[26]);
        $data->set_var('lang_27', $LANG_RMANAGER_UPLUGIN[27]);
        $data->set_var('lang_28', $LANG_RMANAGER_UPLUGIN[28]);
        $data->set_var('lang_32', $LANG_RMANAGER_UPLUGIN[32]);
        $data->set_var('lang_33', $LANG_RMANAGER_UPLUGIN[33]);
        $data->set_var('lang_34', $LANG_RMANAGER_UPLUGIN[34]);
        $data->set_var('lang_35', $LANG_RMANAGER_UPLUGIN[35]);
        $data->set_var('lang_36', $LANG_RMANAGER_UPLUGIN[36]);
        $data->set_var('lang_37', $LANG_RMANAGER_UPLUGIN[37]);
        $data->set_var('lang_38', $LANG_RMANAGER_UPLUGIN[38]);
        $data->set_var('lang_132', $LANG_RMANAGER_UPLUGIN[132]);
	
        // Set hard coded values now
	$data->set_var('value_0', $row['id']);
        $data->set_var('value_2', $row['name']);        
        $data->set_var('value_3', $row['version']);     
        $data->set_var('value_A', $row['fname']);
          
        // We have to figure out what databases are supported.. 
        // Since it is controlled using the bit method, we have to now &AND the value for each position, and find it its on or off
        $db = $row['db'];
  
        // MySQL is it supported?
        if ( ($db & 1) === 0) {
            $data->set_var('value_4a', '');
        }
        else {
            $data->set_var('value_4a', 'checked="checked"');            
        }

        // MSSQL
        if ( ($db & 2) === 0) {
            $data->set_var('value_4b', '');
        }
        else {
            $data->set_var('value_4b', 'checked="checked"');            
        }
   
        // PSGRE
        if ( ($db & 4) === 0) {
            $data->set_var('value_4c', '');
        }
        else {
            $data->set_var('value_4c', 'checked="checked"');            
        }
        
        $data->set_var('value_6', $row['soft_dep']);        
        $data->set_var('value_7', $row['short_des']);             
        $data->set_var('value_9', $row['credits']);        
        $data->set_var('value_10', $row['state']);        
        $data->parse('output','index');
        $display .= $data->finish($data->get_var('output'));

    }
    else if ($_GET['cmd'] == 5) {
        // Show Upload Patch
        $data = new Template($_CONF['path'].'plugins/repository/templates');
	$data->set_file(array('index'=>'addpatch.thtml'));
	// This instruction sets the javascript language variables
	
	// Get repositories from database	
	$data->set_var('store_0', tdisplay_formattedmessage(NULL, $LANG_RMANAGER_UPLUGIN[45], FALSE));
	$data->set_var('lang_1', $LANG_RMANAGER_UPLUGIN[1]);
	$data->set_var('lang_46', $LANG_RMANAGER_UPLUGIN[46]);
	$data->set_var('lang_47', $LANG_RMANAGER_UPLUGIN[47]);
	$data->set_var('lang_48', $LANG_RMANAGER_UPLUGIN[48]);
	$data->set_var('lang_49', $LANG_RMANAGER_UPLUGIN[49]);
	$data->set_var('lang_50', $LANG_RMANAGER_UPLUGIN[50]);
	$data->set_var('lang_51', $LANG_RMANAGER_UPLUGIN[51]);
	$data->set_var('lang_52', $LANG_RMANAGER_UPLUGIN[52]);
	$data->set_var('lang_53', $LANG_RMANAGER_UPLUGIN[53]);
	$data->set_var('lang_17', $LANG_RMANAGER_UPLUGIN[17]);
	$data->set_var('lang_54', $LANG_RMANAGER_UPLUGIN[54]);
	$data->set_var('lang_55', $LANG_RMANAGER_UPLUGIN[55]);
	$data->set_var('lang_56', $LANG_RMANAGER_UPLUGIN[56]);
	$data->set_var('lang_61', $LANG_RMANAGER_UPLUGIN[61]);
	$data->set_var('lang_62', $LANG_RMANAGER_UPLUGIN[62]);
	$data->set_var('lang_63', $LANG_RMANAGER_UPLUGIN[63]);
	$data->set_var('lang_90', $LANG_RMANAGER_UPLUGIN[90]);
        $data->set_var('value_0',(int) ( (isset($_GET['id'])) ? $_GET['id'] : 0));
	$data->parse('output','index');
	$display .= $data->finish($data->get_var('output'));
     
    }
    else if ($_GET['cmd'] == 6) {
        // Show Upgrade Announcement
        $data = new Template($_CONF['path'].'plugins/repository/templates');
	$data->set_file(array('index'=>'upgrade.thtml'));
	// This instruction sets the javascript language variables
	
	// Get repositories from database	
	$data->set_var('lang_1', $LANG_RMANAGER_UPLUGIN[1]);
	$data->set_var('store_0', tdisplay_formattedmessage(NULL, $LANG_RMANAGER_UPLUGIN[69], FALSE));
	$data->set_var('lang_70', $LANG_RMANAGER_UPLUGIN[70]);
        $data->set_var('lang_139', $LANG_RMANAGER_UPLUGIN[139]);
        $data->set_var('lang_2', $LANG_RMANAGER_UPLUGIN[2]);
        $data->set_var('lang_140', $LANG_RMANAGER_UPLUGIN[140]);
	$data->set_var('lang_71', $LANG_RMANAGER_UPLUGIN[71]);
	$data->set_var('lang_17', $LANG_RMANAGER_UPLUGIN[17]);
        $data->set_var('value_0',(int) ( (isset($_GET['id'])) ? $_GET['id'] : 0));
	$data->parse('output','index');
	$display .= $data->finish($data->get_var('output'));
    
    }
    else {
        $display = COM_siteHeader('');
        $display .= COM_showMessageText($LANG_RMANAGER['error_invalidget']);   
    }


}
else if (isset($_GET['ret'])) {
    if ($_GET['ret'] == 1) {
        // Return request from the plugin upload 
        // So first thing to check is if its a post form submit
        if (isset($_POST['submit_upload_plugin'])) {
            // Now, get variables
            $name = (isset($_POST['GEEKLOG_PLNAME'])) ? $_POST['GEEKLOG_PLNAME'] : "";
            $version = (isset($_POST['GEEKLOG_PLVERSION'])) ? $_POST['GEEKLOG_PLVERSION'] : "";
            $mysql = (isset($_POST['GEEKLOG_PLMYSQL'])) ? $_POST['GEEKLOG_PLMYSQL'] : false;
            $mssql = (isset($_POST['GEEKLOG_PLMSSQL'])) ? $_POST['GEEKLOG_PLMSSQL'] : false;
            $postgre = (isset($_POST['GEEKLOG_PLPOSTGRE'])) ? $_POST['GEEKLOG_PLPOSTGRE'] : false;
            $sys_dependencies = (isset($_POST['GEEKLOG_PLSOFTDEP'])) ? $_POST['GEEKLOG_PLSOFTDEP'] : "";
            $shrt_des = (isset($_POST['GEEKLOG_SHRTDES'])) ? $_POST['GEEKLOG_SHRTDES'] : "";
            $credits = (isset($_POST['GEEKLOG_CREDITS'])) ? $_POST['GEEKLOG_CREDITS'] : "";
            $state = (isset($_POST['GEEKLOG_STATE'])) ? $_POST['GEEKLOG_STATE'] : "stable";
            $fname = (isset($_POST['GEEKLOG_PLNAME2'])) ? $_POST['GEEKLOG_PLNAME2'] : "";

            // Check required variables for validity
            // And we also have to check to make sure that plugin already exists
            if (($name == "") or ($fname == "") or ($version == "") or ($shrt_des == "") or (($mysql == false) and ($mssql == false) and ($postgre == false))) {
                header("Location: pupload.php?cmd=1&msg=23");
                exit();
            }
            
           // Check if a plugin of the same name exists in the repository
	   $tblname = $_TABLES['repository_listing'];
           $result = DB_query("SELECT name FROM {$tblname} WHERE name = '{$name}';");
   
           // Get result, or if null, the plugin can be uploaded since it won't exist
	   $res = DB_fetchArray($result);
           
           if (($res !== FALSE) and ($update == "0")) {
               header("Location: pupload.php?cmd=1&msg=24");
               exit();            
           }
	   
	   // Is the file size too large (MAX_UPLOADED_FILE_SIZE)
	   if ($_FILES['GEEKLOG_FILE_PUPLOAD']['size'] > $_RM_CONF['max_pluginpatch_upload']) {
                header("Location: pupload.php?cmd=1&tmsg=30&enable_spf=1&ssp=InvalidFileSize");
                exit();
	   }
	   
	   // Have to make sure its an uploaded file, and not a trick to get to /etc/psswd etc
	   if ( (!(is_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']))) or ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_OK )) {
                header("Location: pupload.php?cmd=1&tmsg=30&enable_spf=1&errno={$_FILES['GEEKLOG_FILE_PUPLOAD']['error']}");
                exit();
	   }

           // The plugin does not already exist, try file formatting - get base file name
           $file_param = pathinfo($_FILES['GEEKLOG_FILE_PUPLOAD']['name']);

           // Check extension
           // The extension check will also check to make sure all the required files are there for auto installation
           $array_inc_files_cp = array();
           $is_zip = false;
           $full_ext = null;

           switch ($file_param['extension']) {
               case "gz": 
	           // Load listing into directory
                   include_once 'Archive/Tar.php';
		   $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], 'gz');
		   
                   $array_inc_files_cp = $comp->listContent();
		   $full_ext = ".tar.gz";
                   break;
               case "bz2": 
                   // Load listing into directory
                   include_once 'Archive/Tar.php';
	           $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], 'bz2');
		   
		   $array_inc_files_cp = $comp->listContent();
		   $full_ext = ".tar.bz2";
	           break;
	       case "tar":
                   // Load listing into directory
                   include_once 'Archive/Tar.php';
	           $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], null);
		   
		   $array_inc_files_cp = $comp->listContent();
                   $full_ext = ".tar";
                   break;
               case "zip":
                   // We are using the Zip Archiving extension
                   $comp = new ZipArchive();
                   $re = $comp->open($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']); 
                   
                   // We break if it failed since the empty $array_inc array will caught anyways later on
                   if ($re !== TRUE) {
                       break;
                   }
                   
                   // Loop over each file, trying to get data
                   for ($i = 0; $i < $comp->numFiles; $i++) {
                       $array_inc_files_cp[] = $comp->statIndex($i);
                   }
                   
                   $is_zip = true;
                   $full_ext = ".zip";
                   // Done now, we have our listing :)
                   break;
               default:
                   header("Location: pupload.php?cmd=1&msg=29");
                   exit();  
                   break;

           }

           // Make sure the files exist that we need
           // These files are auto_install.php, auto_uninstall.php, functions.inc, config.php, 
           if ($is_zip === TRUE) {
               $is_zip = "name";
           }
           else {
               $is_zip = "filename";
           }
          
           // This integer stores the value of the files required in the bits
           // Integer is a 32 bit, so 32 required file names
           // Bits are specified to files in BIG ENDIAN, in the following way:
           // bit #  mask#    :    filename
           //  0     1        :    autoinstall.php     
           //  1     2        :    autouninstall.php
           //  2     4        :    functions.inc
           //  3     8        :    config.php
           //  4              :    not set
           //  5              :    not set
           //  6              :    etc
           // As you can see, the mask goes up by power of 2, so 2^0, 2^1, 2^2, 2^3, etc
           // In the case that functions.inc is missing, the end integer (last 4 bits) would be 1011 or 11. All present is F or 15
           // Array is format: Mask #=>file name
           $required_fnames = array(1=> "autoinstall.php", 2=> "autouninstall.php", 4 => "functions.inc");
           $bitwise_integer_value = 0;           

           // Time to loop through the array, getting the file name's basename, and then uploading it
           foreach ($array_inc_files_cp as $key) {
               $tmp_fn = pathinfo($key[$is_zip]); 

               // Check against file name
               foreach ($required_fnames as $bitkey => $rfname) {
                   // Does the basename match any
                   if ($rfname == $tmp_fn['basename']) {
                       // Set bit key value
                       $bitwise_integer_value = $bitwise_integer_value | $bitkey; 
                   }  
               }
	   }

           // Now check to see if the file is missing
           // To do this we simply loop over the array, and OR each with the next key value to get the total number
           $bitwise_int_required_value = 0; // Value if everything is OK
           $bitkey = 0;               

           foreach ($required_fnames as $bitkey => $rfname) {
               $bitwise_int_required_value = $bitwise_int_required_value | $bitkey;
           }

           // Now we have the real value that is needed, lets find what files are missing using simple math
           // We are going to AND the value of the key with the required value, and then if that value is then 0,
           // That file has not been found, else if it is non zero (the value of the key), it has been found
           $missing_files = array();
	   
           foreach ($required_fnames as $bitkey => $rfname) {
              if (($bitwise_integer_value & $bitkey) === 0) {
                   $missing_files[] = $rfname;
               }     
           }

	   // Now check to see if we can offer this one for automatic install or not
	   if ($bitwise_integer_value === $bitwise_int_required_value) {
	       $automatic_installer = 1;    
	   }
	   else {
	       $automatic_installer = 0;
	   }

           // Get database bit value
           $database_bit_value = 0;
           if ($mysql !== FALSE) {
	       $database_bit_value = $database_bit_value | 1;
           }

           if ($mssql !== FALSE) {
	       $database_bit_value = $database_bit_value | 2;
           }

           if ($postgre !== FALSE) {
	       $database_bit_value = $database_bit_value | 4;
           }

           // Insert values into the database
           #http://wiki.geeklog.net/index.php/Using_COM_applyFilter
           $name = COM_applyFilter($name);
           $version = COM_applyFilter($version);
           $dependencies = "";
           $sys_dependencies = COM_applyFilter($sys_dependencies);
           $shrt_des = COM_applyFilter($shrt_des);
           $credits = COM_applyFilter($credits);
           $state = COM_applyFilter($state);
           $fname = COM_applyFilter($fname);
	  
	   // Send query to the database
           $tblname = $_TABLES['repository_listing'];
	   // This type of string format needs to be against the 'wall' and not indented for it to work -- 
$qstr = <<<HETERO
INSERT INTO {$tblname}(ext, name, version, db, dependencies, soft_dep, short_des, credits, uploading_author, install, state, moderation, fname) 
VALUES('{$full_ext}', '{$name}','{$version}','{$database_bit_value}','{$dependencies}','{$sys_dependencies}','{$shrt_des}','{$credits}','{$_USER['uid']}','{$automatic_installer}','{$state}', '{$_RM_CONF['repository_moderated']}', '{$fname}');
HETERO;

           $result = DB_query($qstr);
	       
	   $MYSQL_ID = DB_insertId();    
     
           // Continue with the upload, if the user figured in error, he can update the plugin later when the message says he can
           // Time to move the file over to the repository directory
	   // The upload path can either be the tmp upload or the real repository
	   if ($_RM_CONF['repository_moderated'] === 1) {
	       $output_repository = "tmp_uploads/".$name.'_'.$version.'_'.$state.'_'.$MYSQL_ID.$full_ext;  
	   }
	   else {
	       $output_repository = "main/".$name.'_'.$version.'_'.$state.'_'.$MYSQL_ID.$full_ext;
	   }
	   
	   // Move the archive now
	   if (!(move_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], $output_repository))) {
               header("Location: pupload.php?cmd=1&msg=31");
               exit();  
	   }
	   
	   // Make a message saying if any files are missing
	   $filesmissing_msg = "";
	   if ($bitwise_integer_value !== $bitwise_int_required_value) {
	       $filesmissing_msg = "<br /><br />".$LANG_RMANAGER_UPLUGIN[41].'<br />'.$LANG_RMANAGER_UPLUGIN[42]."<br /><span style='color:red'>";
	       foreach ($missing_files as $hkey) {
	           $filesmissing_msg .= $hkey."<br />";        
	       }
	       
	       $filesmissing_msg .= "</span>";
	   }
	   
	   // Since everything has succeeded successfully, display any files that should be included, exit
           $display = COM_siteHeader('');
	   if ($_RM_CONF['repository_moderated'] == TRUE) {
               $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[40]."</b>".$filesmissing_msg));        
	   }
	   else {
	       $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[39]."</b><br /><br /><a href='{$output_repository}'>{$name}_{$version}_{$state}_{$MYSQL_ID}{$full_ext}</a>".$filesmissing_msg));     
	   }
        }
        else if (isset($_POST['submit_edit_plugin'])) {
            // Now, get variables
            $name = (isset($_POST['GEEKLOG_PLNAME'])) ? $_POST['GEEKLOG_PLNAME'] : "";
            $version = (isset($_POST['GEEKLOG_PLVERSION'])) ? $_POST['GEEKLOG_PLVERSION'] : "";
            $mysql = (isset($_POST['GEEKLOG_PLMYSQL'])) ? $_POST['GEEKLOG_PLMYSQL'] : false;
            $mssql = (isset($_POST['GEEKLOG_PLMSSQL'])) ? $_POST['GEEKLOG_PLMSSQL'] : false;
            $postgre = (isset($_POST['GEEKLOG_PLPOSTGRE'])) ? $_POST['GEEKLOG_PLPOSTGRE'] : false;
            $sys_dependencies = (isset($_POST['GEEKLOG_PLSOFTDEP'])) ? $_POST['GEEKLOG_PLSOFTDEP'] : "";
            $shrt_des = (isset($_POST['GEEKLOG_SHRTDES'])) ? $_POST['GEEKLOG_SHRTDES'] : "";
            $credits = (isset($_POST['GEEKLOG_CREDITS'])) ? $_POST['GEEKLOG_CREDITS'] : "";
            $state = (isset($_POST['GEEKLOG_STATE'])) ? $_POST['GEEKLOG_STATE'] : "stable";
            $fname = (isset($_POST['GEEKLOG_PLNAME2'])) ? $_POST['GEEKLOG_PLNAME2'] : "";
            
            // Check required variables for validity
            // And we also have to check to make sure that plugin already exists
            if (($name == "") or ($version == "") or ($shrt_des == "") or (($mysql == false) and ($mssql == false) and ($postgre == false))) {
                header("Location: pupload.php?cmd=2&msg=23");
                exit();
            }	    

	    // New file, or same file and just edit Database
	    if ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_NO_FILE ) {
	        // Move the new file to overwrite the existing one
		
		// Is the file size too large (MAX_UPLOADED_FILE_SIZE)
	        if ($_FILES['GEEKLOG_FILE_PUPLOAD']['size'] > $_RM_CONF['max_pluginpatch_upload']) {
                header("Location: pupload.php?cmd=2&tmsg=30&enable_spf=1&ssp=InvalidFileSize");
                exit();    
	        }
		
	        // Have to make sure its an uploaded file, and not a trick to get to /etc/psswd etc
	        if ( (!(is_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']))) or ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_OK )) {
                    header("Location: pupload.php?cmd=2&tmsg=30&enable_spf=1&errno={$_FILES['GEEKLOG_FILE_PUPLOAD']['error']}");
                    exit();
                }
		
                // The plugin does not already exist, try file formatting - get base file name
                $file_param = pathinfo($_FILES['GEEKLOG_FILE_PUPLOAD']['name']);

                // Check extension
                // The extension check will also check to make sure all the required files are there for auto installation
                $array_inc_files_cp = array();
                $is_zip = false;
                $full_ext = null;

                switch ($file_param['extension']) {
                    case "gz": 
	                // Load listing into directory
                        include_once 'Archive/Tar.php';
		        $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], 'gz');
		   
                        $array_inc_files_cp = $comp->listContent();
                        $full_ext = ".tar.gz";
                        break;
                    case "bz2": 
                        // Load listing into directory
                        include_once 'Archive/Tar.php';
	                $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], 'bz2');
		   
		        $array_inc_files_cp = $comp->listContent();
		        $full_ext = ".tar.bz2";
	                break;
                    case "tar":
                        // Load listing into directory
                        include_once 'Archive/Tar.php';
	                $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], null);
		   
		        $array_inc_files_cp = $comp->listContent();
                        $full_ext = ".tar";
                        break;
                    case "zip":
                        // We are using the Zip Archiving extension
                        $comp = new ZipArchive();
                        $re = $comp->open($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']); 
                   
                        // We break if it failed since the empty $array_inc array will caught anyways later on
                        if ($re !== TRUE) {
                            break;
                        }
                   
                        // Loop over each file, trying to get data
                        for ($i = 0; $i < $comp->numFiles; $i++) {
                           $array_inc_files_cp[] = $comp->statIndex($i);
                        }
                   
                        $is_zip = true;
                        $full_ext = ".zip";
                        // Done now, we have our listing :)
                        break;
                    default:
                        header("Location: pupload.php?cmd=2&msg=29");
                        exit();  
                        break;

                }

                // Make sure the files exist that we need
                // These files are auto_install.php, auto_uninstall.php, functions.inc, config.php, 
                if ($is_zip === TRUE) {
                    $is_zip = "name";
                }
                else {
                    $is_zip = "filename";
                }
          
                // This integer stores the value of the files required in the bits
                // Integer is a 32 bit, so 32 required file names
                // Bits are specified to files in BIG ENDIAN, in the following way:
                // bit #  mask#    :    filename
                //  0     1        :    autoinstall.php     
                //  1     2        :    autouninstall.php
                //  2     4        :    functions.inc
                //  3     8        :    config.php
                //  4              :    not set
                //  5              :    not set
                //  6              :    etc
                // As you can see, the mask goes up by power of 2, so 2^0, 2^1, 2^2, 2^3, etc
                // In the case that functions.inc is missing, the end integer (last 4 bits) would be 1011 or 11. All present is F or 15
                // Array is format: Mask #=>file name
                $required_fnames = array(1=> "autoinstall.php", 2=> "autouninstall.php", 4 => "functions.inc");
                $bitwise_integer_value = 0;           

                // Time to loop through the array, getting the file name's basename, and then uploading it
                foreach ($array_inc_files_cp as $key) {
                    $tmp_fn = pathinfo($key[$is_zip]); 

                    // Check against file name
                    foreach ($required_fnames as $bitkey => $rfname) {
                        // Does the basename match any
                        if ($rfname == $tmp_fn['basename']) {
                            // Set bit key value
                            $bitwise_integer_value = $bitwise_integer_value | $bitkey; 
                        }  
                    }
	        }

                // Now check to see if the file is missing
                // To do this we simply loop over the array, and OR each with the next key value to get the total number
                $bitwise_int_required_value = 0; // Value if everything is OK
                $bitkey = 0;               

                foreach ($required_fnames as $bitkey => $rfname) {
                    $bitwise_int_required_value = $bitwise_int_required_value | $bitkey;
                }

                // Now we have the real value that is needed, lets find what files are missing using simple math
                // We are going to AND the value of the key with the required value, and then if that value is then 0,
                // That file has not been found, else if it is non zero (the value of the key), it has been found
                $missing_files = array();
	   
                foreach ($required_fnames as $bitkey => $rfname) {
                   if (($bitwise_integer_value & $bitkey) === 0) {
                        $missing_files[] = $rfname;
                    }     
                }

                // Now check to see if we can offer this one for automatic install or not
	        if ($bitwise_integer_value === $bitwise_int_required_value) {
	            $automatic_installer = 1;    
	        }
	        else {
	            $automatic_installer = 0;
	        }
            }    
		
           // Get database bit value
           $database_bit_value = 0;
           if ($mysql !== FALSE) {
	       $database_bit_value = $database_bit_value | 1;
           }

           if ($mssql !== FALSE) {
	       $database_bit_value = $database_bit_value | 2;
           }

           if ($postgre !== FALSE) {
	       $database_bit_value = $database_bit_value | 4;
           }

            // Insert values into the database
            #http://wiki.geeklog.net/index.php/Using_COM_applyFilter
            $name = COM_applyFilter($name);
            $version = COM_applyFilter($version);
            $dependencies = "";
            $sys_dependencies = COM_applyFilter($sys_dependencies);
            $shrt_des = COM_applyFilter($shrt_des);
            $credits = COM_applyFilter($credits);
            $state = COM_applyFilter($state);
	    $id = (int)((isset($_GET['pid'])) ? $_GET['pid'] : 0);
            $fname = COM_applyFilter($fname);
           
            // Send query to the database
            $tblname = $_TABLES['repository_listing'];

            // Does the user have permissions for this plugin?
            // Check if author
            $result = DB_query("SELECT id FROM {$tblname} WHERE uploading_author = {$_USER['uid']};");
            
            if (DB_fetchArray($result) === FALSE) {
                // Is maintainer
                $tbl2 = $_TABLES['repository_maintainer'];
                $result = DB_query("SELECT * FROM {$tbl2} WHERE maintainer_id = {$_USER['uid']} AND plugin_id = {$id};");
                if ($result === NULL) {
                    header("Location: pupload.php?cmd=2&msg=74");
                    exit();                     
                }
            }
           	  
	    // This type of string format needs to be against the 'wall' and not indented for it to work -- 
$qstr = <<<HETERO
UPDATE {$tblname} SET ext = '{$full_ext}', name = '{$name}', version = '{$version}', db = '{$database_bit_value}', dependencies = '{$dependencies}', soft_dep = '{$sys_dependencies}', short_des = '{$shrt_des}', credits = '{$credits}', install = '{$automatic_installer}', state = '{$state}', fname = '{$fname}', moderation = '{$_RM_CONF['repository_moderated']}' WHERE id = '{$id}';
HETERO;

            // Run Query
            $result = DB_query($qstr);
	       
	    $MYSQL_ID = DB_insertId();    
	    
	    // New file, or same file and just edit Database
  	    if ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_NO_FILE ) {
                // Continue with the upload, if the user figured in error, he can update the plugin later when the message says he can
                // Time to move the file over to the repository directory
                // The upload path can either be the tmp upload or the real repository
	        if ($_RM_CONF['repository_moderated'] === 1) {
	            $output_repository = "tmp_uploads/".$name.'_'.$version.'_'.$state.'_'.$id.$full_ext;  
	        }
	        else {
	            $output_repository = "main/".$name.'_'.$version.'_'.$state.'_'.$id.$full_ext;
	        }
	   
	        // Move the archive now
	        if (!(move_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], $output_repository))) {
                    header("Location: pupload.php?cmd=2&msg=31");
                    exit();  
     	        }
	   
	        // Make a message saying if any files are missing
	        $filesmissing_msg = "";
	        if ($bitwise_integer_value !== $bitwise_int_required_value) {
	            $filesmissing_msg = "<br /><br />".$LANG_RMANAGER_UPLUGIN[41].'<br />'.$LANG_RMANAGER_UPLUGIN[42]."<br /><span style='color:red'>";
	            foreach ($missing_files as $hkey) {
	                $filesmissing_msg .= $hkey."<br />";        
	            }
	       
	            $filesmissing_msg .= "</span>";
	        }

	    
	    }
	    
	   // Since everything has succeeded successfully, display any files that should be included, exit
           $display = COM_siteHeader('');
	   if ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] ===  UPLOAD_ERR_NO_FILE) {
	       $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[76].'</b>'));    
	   }
	   else if ($_RM_CONF['repository_moderated'] == TRUE) {
               $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[40]."</b>".$filesmissing_msg));               
	   }

	   else {
               $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[39]."</b><br /><br /><a href='{$output_repository}'>{$name}_{$version}_{$state}_{$MYSQL_ID}{$full_ext}</a>".$filesmissing_msg));     	   
           }


	}
        // Is patch submittal
        else if (isset($_POST['submit_upload_patch'])) {
	    // Get values
	    $name = (isset($_POST['GEEKLOG_PLNAME'])) ? $_POST['GEEKLOG_PLNAME'] : "";
	    $vtype = (isset($_POST['GEEKLOG_PGT'])) ? $_POST['GEEKLOG_PGT'] : "";
	    $version = (isset($_POST['GEEKLOG_PLVERSION'])) ? $_POST['GEEKLOG_PLVERSION'] : "";
	    $id = (int) ( (isset($_GET['id'])) ? $_GET['id'] : 0);
	    $severity = (isset($_POST['GEEKLOG_SEV'])) ? $_POST['GEEKLOG_SEV'] : "low";
	    $des = (isset($_POST['GEEKLOG_DES'])) ? $_POST['GEEKLOG_DES'] : "";
	    
            // Check required variables for validity
            // And we also have to check to make sure that plugin already exists
            if (($name == "") or ($vtype == "") or ($vtype == "") or ($id == 0) or ($des == "")) {
                header("Location: pupload.php?cmd=2&msg=57");
                exit();
            }
            // Send query to the database
            $tblname = $_TABLES['repository_listing'];

            // Does the user have permissions for this plugin?
            // Check if author
            $result = DB_query("SELECT id FROM {$tblname} WHERE uploading_author = {$_USER['uid']};");
            
            if (DB_fetchArray($result) === FALSE) {
                // Is maintainer
                $tbl2 = $_TABLES['repository_maintainer'];
                $result = DB_query("SELECT * FROM {$tbl2} WHERE maintainer_id = {$_USER['uid']} AND plugin_id = {$id};");
                if ($result === NULL) {
                    header("Location: pupload.php?cmd=2&msg=74");
                    exit();
                }
            }

           $name = COM_applyFilter($name);
           
           // Check if a patch of the same name exists in the repository
	   $tblname = $_TABLES['repository_patches'];
           $result = DB_query("SELECT name FROM {$tblname} WHERE name = '{$name}';");
   
           // Get result, or if null, the patch can be uploaded since it won't exist
	   $res = DB_fetchArray($result);
           
           if (($res !== FALSE) and ($update == "0")) {
                header("Location: pupload.php?cmd=2&msg=58");
                exit();           
           }
	   
	   // Is the file size too large (MAX_UPLOADED_FILE_SIZE)
	   if ($_FILES['GEEKLOG_FILE_PUPLOAD']['size'] > $_RM_CONF['max_pluginpatch_upload']) {
                header("Location: pupload.php?cmd=2&msg=30");
                exit();       
	   }
	   
	   // Have to make sure its an uploaded file, and not a trick to get to /etc/psswd etc
	   if ( (!(is_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']))) or ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_OK )) {
                header("Location: pupload.php?cmd=2&msg=30");
                exit();  
	   }

           // The patch does not already exist, try file formatting - get base file name
           $file_param = pathinfo($_FILES['GEEKLOG_FILE_PUPLOAD']['name']);

           // Check extension
           // The extension check will also check to make sure all the required files are there for auto installation
           $array_inc_files_cp = array();
           $is_zip = false;
           $full_ext = null;

           switch ($file_param['extension']) {
               case "gz": 
	           // Load listing into directory
                   include_once 'Archive/Tar.php';
		   $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], 'gz');
		   
                   $array_inc_files_cp = $comp->listContent();
		   $full_ext = ".tar.gz";
                   break;
               case "bz2": 
                   // Load listing into directory
                   include_once 'Archive/Tar.php';
	           $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], 'bz2');
		   
		   $array_inc_files_cp = $comp->listContent();
		   $full_ext = ".tar.bz2";
	           break;
	       case "tar":
                   // Load listing into directory
                   include_once 'Archive/Tar.php';
	           $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], null);
		   
		   $array_inc_files_cp = $comp->listContent();
                   $full_ext = ".tar";
                   break;
               case "zip":
                   // We are using the Zip Archiving extension
                   $comp = new ZipArchive();
                   $re = $comp->open($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']); 
                   
                   // We break if it failed since the empty $array_inc array will caught anyways later on
                   if ($re !== TRUE) {
                       break;
                   }
                   
                   // Loop over each file, trying to get data
                   for ($i = 0; $i < $comp->numFiles; $i++) {
                       $array_inc_files_cp[] = $comp->statIndex($i);
                   }
                   
                   $is_zip = true;
                   $full_ext = ".zip";
                   // Done now, we have our listing :)
                   break;
               default:
                   header("Location: pupload.php?cmd=2&msg=30");
                   exit();  
                   break;

           }

           // Make sure the files exist that we need
           // These files are install_patch.php
           if ($is_zip === TRUE) {
               $is_zip = "name";
           }
           else {
               $is_zip = "filename";
           }

           // This integer stores the value of the files required in the bits
           // Integer is a 32 bit, so 32 required file names
           // Bits are specified to files in BIG ENDIAN, in the following way:
           // bit #  mask#    :    filename
           //  0     1        :    autoinstall.php     
           //  1     2        :    autouninstall.php
           //  2     4        :    functions.inc
           //  3     8        :    config.php
           //  4              :    not set
           //  5              :    not set
           //  6              :    etc
           // As you can see, the mask goes up by power of 2, so 2^0, 2^1, 2^2, 2^3, etc
           // In the case that functions.inc is missing, the end integer (last 4 bits) would be 1011 or 11. All present is F or 15
           // Array is format: Mask #=>file name
           $required_fnames = array(1=> "update.php");
           $bitwise_integer_value = 0;           

           // Time to loop through the array, getting the file name's basename, and then uploading it
           foreach ($array_inc_files_cp as $key) {
               $tmp_fn = pathinfo($key[$is_zip]); 

               // Check against file name
               foreach ($required_fnames as $bitkey => $rfname) {
                   // Does the basename match any
                   if ($rfname == $tmp_fn['basename']) {
                       // Set bit key value
                       $bitwise_integer_value = $bitwise_integer_value | $bitkey; 
                   }  
               }
	   }

           // Now check to see if the file is missing
           // To do this we simply loop over the array, and OR each with the next key value to get the total number
           $bitwise_int_required_value = 0; // Value if everything is OK
           $bitkey = 0;               

           foreach ($required_fnames as $bitkey => $rfname) {
               $bitwise_int_required_value = $bitwise_int_required_value | $bitkey;
           }

           // Now we have the real value that is needed, lets find what files are missing using simple math
           // We are going to AND the value of the key with the required value, and then if that value is then 0,
           // That file has not been found, else if it is non zero (the value of the key), it has been found
           $missing_files = array();
	   
           foreach ($required_fnames as $bitkey => $rfname) {
              if (($bitwise_integer_value & $bitkey) === 0) {
                   $missing_files[] = $rfname;
               }     
           }

	   // Now check to see if we can offer this one for automatic install or not
	   if ($bitwise_integer_value === $bitwise_int_required_value) {
	       $automatic_installer = 1;    
	   }
	   else {
	       $automatic_installer = 0;
	   }

           // Insert values into the database
           #http://wiki.geeklog.net/index.php/Using_COM_applyFilter
           $version = COM_applyFilter($version);
           $vtype = COM_applyFilter($vtype);
           $severity = COM_applyFilter($severity);
	   $des = COM_applyFilter($des);
	   $update_number = 0;
           $tblname = $_TABLES['repository_patches'];
           
           // Now get the update number for that plugin
           $result = DB_query("SELECT update_number FROM {$tblname} WHERE plugin_id = '{$id}';");
           
           while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
               if ($result2['update_number'] > $update_number) {
                   $update_number = $result2['update_number'];
               }
           }
           
           $update_number++;
           
	   // Send query to the database
	   // This type of string format needs to be against the 'wall' and not indented for it to work -- 
$qstr = <<<HETERO
INSERT INTO {$tblname}(name, plugin_id, uploading_author, applies_num, version, ext, severity, automatic_install, moderation, description, update_number) 
VALUES('{$name}','{$id}','{$_USER['uid']}','{$vtype}','{$version}','{$full_ext}','{$severity}','{$automatic_installer}', '{$_RM_CONF['repository_moderated']}', '{$des}', '{$update_number}');
HETERO;

           $result = DB_query($qstr);
	       
	   $MYSQL_ID = DB_insertId();    
     
           // Continue with the upload, if the user figured in error, he can update the patch later when the message says he can
           // Time to move the file over to the repository directory
	   // The upload path can either be the tmp upload or the real repository
	   if ($_RM_CONF['repository_moderated'] === 1) {
	       $output_repository = "tmp_uploads/patches/".$name.'_'.$version.'_'.$vtype.'_'.$MYSQL_ID.$full_ext;  
	   }
	   else {
	       $output_repository = "main/patches/".$name.'_'.$version.'_'.$vtype.'_'.$MYSQL_ID.$full_ext;
	   }
	   
	   // Move the archive now
	   if (!(move_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], $output_repository))) {
                header("Location: pupload.php?cmd=2&msg=31");
                exit(); 
	   }
	 
	   // Make a message saying if any files are missing
	   $filesmissing_msg = "";
	   if ($bitwise_integer_value !== $bitwise_int_required_value) {
	       $filesmissing_msg = "<br /><br />".$LANG_RMANAGER_UPLUGIN[67].'<br />'.$LANG_RMANAGER_UPLUGIN[68]."<br /><span style='color:red'>";
	       foreach ($missing_files as $hkey) {
	           $filesmissing_msg .= $hkey."<br />";        
	       }
	       
	       $filesmissing_msg .= "</span>";
	   }
	   
	   // Since everything has succeeded successfully, display any files that should be included, exit
           $display = COM_siteHeader('');
	   if ($_RM_CONF['repository_moderated'] == TRUE) {
               $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[147]."</b>".$filesmissing_msg));        
	   }
	   else {
	       $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[146]."</b><br /><br /><a href='{$output_repository}'>{$name}_{$version}_{$state}_{$MYSQL_ID}{$full_ext}</a>".$filesmissing_msg));     
	   }

	    
	}
        // Is patch submittal
        else if (isset($_POST['submit_upload_upgrade'])) {
            // Get values
            $version = (isset($_POST['GEEKLOG_PLVERSION'])) ? $_POST['GEEKLOG_PLVERSION'] : "";
            $version2 = (isset($_POST['GEEKLOG_PLVERSION2'])) ? $_POST['GEEKLOG_PLVERSION2'] : "";
            $id = (int) ( (isset($_GET['id'])) ? $_GET['id'] : 0);
            $des = (isset($_POST['GEEKLOG_DES'])) ? $_POST['GEEKLOG_DES'] : "";
            
            // Check required variables for validity
            // And we also have to check to make sure that plugin already exists
            if (($version == "") or ($version2 == "") or ($id == 0) or ($des == "")) {
                header("Location: pupload.php?cmd=2&msg=57");
                exit();
            }
            // Send query to the database
            $tblname = $_TABLES['repository_listing'];

            // Does the user have permissions for this plugin?
            // Check if author
            $result = DB_query("SELECT id FROM {$tblname} WHERE uploading_author = {$_USER['uid']};");
            
            if (DB_fetchArray($result) === FALSE) {
                // Is maintainer
                $tbl2 = $_TABLES['repository_maintainer'];
                $result = DB_query("SELECT * FROM {$tbl2} WHERE maintainer_id = {$_USER['uid']} AND plugin_id = {$id};");
                if ($result === NULL) {
                    header("Location: pupload.php?cmd=2&msg=74");
                    exit();
                }
            }

           // Insert values into the database
           #http://wiki.geeklog.net/index.php/Using_COM_applyFilter
           $version = COM_applyFilter($version);
           $version2 = COM_applyFilter($version2);
           $des = COM_applyFilter($des);
           
           // Check if a patch of the same name exists in the repository
           $tblname = $_TABLES['repository_upgrade'];
           $result = DB_query("SELECT version FROM {$tblname} WHERE version = '{$version}' AND version2 = '{$version2}';");
   
           // Get result, or if null, the patch can be uploaded since it won't exist
           $res = DB_fetchArray($result);
           
           if (($res !== FALSE) and ($update == "0")) {
                header("Location: pupload.php?cmd=2&msg=141");
                exit();           
           }
           
           // Is the file size too large (MAX_UPLOADED_FILE_SIZE)
           if ($_FILES['GEEKLOG_FILE_PUPLOAD']['size'] > $_RM_CONF['max_pluginpatch_upload']) {
                header("Location: pupload.php?cmd=2&msg=30");
                exit();       
           }
           
           // Have to make sure its an uploaded file, and not a trick to get to /etc/psswd etc
           if ( (!(is_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']))) or ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_OK )) {
                header("Location: pupload.php?cmd=2&msg=30");
                exit();  
           }

           // The patch does not already exist, try file formatting - get base file name
           $file_param = pathinfo($_FILES['GEEKLOG_FILE_PUPLOAD']['name']);

           // Check extension
           // The extension check will also check to make sure all the required files are there for auto installation
           $array_inc_files_cp = array();
           $is_zip = false;
           $full_ext = null;

           switch ($file_param['extension']) {
               case "gz": 
                   // Load listing into directory
                   include_once 'Archive/Tar.php';
                   $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], 'gz');
                   
                   $array_inc_files_cp = $comp->listContent();
                   $full_ext = ".tar.gz";
                   break;
               case "bz2": 
                   // Load listing into directory
                   include_once 'Archive/Tar.php';
                   $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], 'bz2');
                   
                   $array_inc_files_cp = $comp->listContent();
                   $full_ext = ".tar.bz2";
                   break;
               case "tar":
                   // Load listing into directory
                   include_once 'Archive/Tar.php';
                   $comp = new Archive_Tar($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], null);
                   
                   $array_inc_files_cp = $comp->listContent();
                   $full_ext = ".tar";
                   break;
               case "zip":
                   // We are using the Zip Archiving extension
                   $comp = new ZipArchive();
                   $re = $comp->open($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']); 
                   
                   // We break if it failed since the empty $array_inc array will caught anyways later on
                   if ($re !== TRUE) {
                       break;
                   }
                   
                   // Loop over each file, trying to get data
                   for ($i = 0; $i < $comp->numFiles; $i++) {
                       $array_inc_files_cp[] = $comp->statIndex($i);
                   }
                   
                   $is_zip = true;
                   $full_ext = ".zip";
                   // Done now, we have our listing :)
                   break;
               default:
                   header("Location: pupload.php?cmd=2&msg=30");
                   exit();  
                   break;

           }

           // Make sure the files exist that we need
           // These files are install_patch.php
           if ($is_zip === TRUE) {
               $is_zip = "name";
           }
           else {
               $is_zip = "filename";
           }

           // This integer stores the value of the files required in the bits
           // Integer is a 32 bit, so 32 required file names
           // Bits are specified to files in BIG ENDIAN, in the following way:
           // bit #  mask#    :    filename
           //  0     1        :    autoinstall.php     
           //  1     2        :    autouninstall.php
           //  2     4        :    functions.inc
           //  3     8        :    config.php
           //  4              :    not set
           //  5              :    not set
           //  6              :    etc
           // As you can see, the mask goes up by power of 2, so 2^0, 2^1, 2^2, 2^3, etc
           // In the case that functions.inc is missing, the end integer (last 4 bits) would be 1011 or 11. All present is F or 15
           // Array is format: Mask #=>file name
           $required_fnames = array(1=> "update.php");
           $bitwise_integer_value = 0;           

           // Time to loop through the array, getting the file name's basename, and then uploading it
           foreach ($array_inc_files_cp as $key) {
               $tmp_fn = pathinfo($key[$is_zip]); 

               // Check against file name
               foreach ($required_fnames as $bitkey => $rfname) {
                   // Does the basename match any
                   if ($rfname == $tmp_fn['basename']) {
                       // Set bit key value
                       $bitwise_integer_value = $bitwise_integer_value | $bitkey; 
                   }  
               }
           }

           // Now check to see if the file is missing
           // To do this we simply loop over the array, and OR each with the next key value to get the total number
           $bitwise_int_required_value = 0; // Value if everything is OK
           $bitkey = 0;               

           foreach ($required_fnames as $bitkey => $rfname) {
               $bitwise_int_required_value = $bitwise_int_required_value | $bitkey;
           }

           // Now we have the real value that is needed, lets find what files are missing using simple math
           // We are going to AND the value of the key with the required value, and then if that value is then 0,
           // That file has not been found, else if it is non zero (the value of the key), it has been found
           $missing_files = array();
           
           foreach ($required_fnames as $bitkey => $rfname) {
              if (($bitwise_integer_value & $bitkey) === 0) {
                   $missing_files[] = $rfname;
               }     
           }

           // Now check to see if we can offer this one for automatic install or not
           if ($bitwise_integer_value === $bitwise_int_required_value) {
               $automatic_installer = 1;    
           }
           else {
               $automatic_installer = 0;
           }
           
           $tblname = $_TABLES['repository_upgrade'];          

           // Send query to the database
           // This type of string format needs to be against the 'wall' and not indented for it to work -- 
$qstr = <<<HETERO
INSERT INTO {$tblname}(plugin_id, version, version2, description, ext, moderation, automatic_install) 
VALUES('{$id}', '{$version}','{$version2}','{$des}','{$full_ext}', '{$_RM_CONF['repository_moderated']}', '{$automatic_installer}');
HETERO;

           $result = DB_query($qstr);
               
           $MYSQL_ID = DB_insertId();    
     
           // Continue with the upload, if the user figured in error, he can update the patch later when the message says he can
           // Time to move the file over to the repository directory
           // The upload path can either be the tmp upload or the real repository
           if ($_RM_CONF['repository_moderated'] === 1) {
               $output_repository = "tmp_uploads/upgrades/".$version.'_from_'.$version2.'_'.$id.'_'.$MYSQL_ID.$full_ext;  
           }
           else {
               $output_repository = "main/upgrades/".$version.'_from_'.$version2.'_'.$id.'_'.$MYSQL_ID.$full_ext;
           }
           
           // Move the archive now
           if (!(move_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], $output_repository))) {
                header("Location: pupload.php?cmd=2&msg=31");
                exit(); 
           }
         
           // Make a message saying if any files are missing
           $filesmissing_msg = "";
           if ($bitwise_integer_value !== $bitwise_int_required_value) {
               $filesmissing_msg = "<br /><br />".$LANG_RMANAGER_UPLUGIN[142].'<br />'.$LANG_RMANAGER_UPLUGIN[143]."<br /><span style='color:red'>";
               foreach ($missing_files as $hkey) {
                   $filesmissing_msg .= $hkey."<br />";        
               }
               
               $filesmissing_msg .= "</span>";
           }
           
           // Since everything has succeeded successfully, display any files that should be included, exit
           $display = COM_siteHeader('');
           if ($_RM_CONF['repository_moderated'] == TRUE) {
               $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[145]."</b>".$filesmissing_msg));        
           }
           else {
               $display .= tdisplay_formattedmessage(array('<b>'.$LANG_RMANAGER_UPLUGIN[144]."</b><br /><br /><a href='{$output_repository}'>{$name}_{$version}_{$state}_{$MYSQL_ID}{$full_ext}</a>".$filesmissing_msg));     
           }

            
        }
        else {
           header("Location: index.php?cmd=2&msg=136");
           exit();  
        }
    }
    else {
       header("Location: index.php?cmd=2&msg=136");
       exit(); 
    }
    

}
else {
    // Load main page
    #$display .= tdisplay_formattedmessage();
    header("Location: pupload.php?cmd=2");
    exit();
}

$display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
$display .= COM_siteFooter();
COM_output($display);
?>
