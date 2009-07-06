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

$display .= COM_siteHeader('');
$display .= COM_startBlock($LANG_RMANAGER['title'], '', COM_getBlockTemplate('_msg_block', 'header'));

#DEBUG VARIABLE later in config file. set at 2MB
define("MAX_FILE_UPLOAD_SIZE", 2000000); 
// Are plugins moderated or not
$plugin_moderated = $_CONF['rmanager_moderated'];

// So if the user got this far they are logged in, which is great

// What command do we have now?
if (isset($_GET['cmd'])) {
    // a command page, not the reply

    if ($_GET['cmd'] == 1) {
        $data = new Template($_CONF['path'].'plugins/rmanager/templates');
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
	$data->set_var('lang_0', $LANG_RMANAGER_UPLUGIN[0]);
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
	$data->parse('output','index');
	$display .= $data->finish($data->get_var('output'));
  
    }
    else if ($_GET['cmd'] == 2) {
        // Listing of all plugins assigned to your name OR set to moderate
        $data = new Template($_CONF['path'].'plugins/rmanager/templates');
	$data->set_file(array('index'=>'listplugins.thtml'));
        
        $tblname = $_DB_table_prefix.'repository_maintainers';
        // Lets get the list of plugin ids from the db that work with the client id
        $result = DB_query("SELECT plugin_id FROM {$tblname} WHERE maintainer_id = '{$_USER['uid']}';");
        $array_of_plugins = array();        

        // Loop through the results, and store the plugin ids in an array
        while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
            $array_of_plugins[] = $result2['plugin_id'];
        }

        $string_of_maintainer_code = "";

        // Get the plugins that match those ID's - These are the maintainer plugins
        $tblname = $_DB_table_prefix.'repository_listing';
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
        <td class='opt'><a href='pupload.php?cmd=3&pid={$result2['id']}'> {$LANG_RMANAGER_UPLUGIN[78]} </a></td><td class='opt'><a href='pupload.php?cmd=5&id={$result2['id']}'> {$LANG_RMANAGER_UPLUGIN[79]} </a></td><td class='opt'><a href='pupload.php?cmd=6&id={$result2['id']}'> {$LANG_RMANAGER_UPLUGIN[80]} </a></td></tr>";
        }

        $data->set_var('lang_0', $LANG_RMANAGER_DPLUGIN[0]);
	
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
            $display = COM_siteHeader('');
            $display .= COM_showMessageText($LANG_RMANAGER['error_invalpluginid']);            
            $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            $display .= COM_siteFooter();
            COM_output($display);
            exit();
        }

        // Get the author ID associated with that plugin id, make sure they match 
        $tblname = $_DB_table_prefix.'repository_listing';
        $result = DB_query("SELECT state,uploading_author,name,version,id,ext FROM {$tblname} WHERE id = '{$p_id}';");
        $author_id = DB_fetchArray($result);
        
        // If it is NULL, it means that the id entered was invalid (plugin does not exist)
        if ($author_id == NULL) {
            $display = COM_siteHeader('');
            $display .= COM_showMessageText($LANG_RMANAGER['error_invalpluginid']);            
            $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            $display .= COM_siteFooter();
            COM_output($display);
            exit();            
        }
        else if ($author_id['uploading_author'] !== $_USER['uid']) {
            $display = COM_siteHeader('');
            $display .= COM_showMessageText($LANG_RMANAGER['error_pdel_noperm']);            
            $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            $display .= COM_siteFooter();
            COM_output($display);
            exit();    
        }

        // Obviously it is authenticated, lets delete the entry from the database (MUNCH)
        // But first, get the file path so we can delete it after the database has been deleted
        $filepath = $author_id['name'].'_'.$author_id['version'].'_'.$author_id['state'].'_'.$author_id['id'].$author_id['ext'];
        
        DB_query("DELETE FROM {$tblname} WHERE id = '{$author_id['id']}';");
        
        // Now remove from the repository listing
        $rmfile = unlink("../repository/".$filepath);

        // Did it fail? 
        if ($rmfile === FALSE) {
            $display = COM_siteHeader('');
            $display .= COM_showMessageText($LANG_RMANAGER['error_pdel_erm'].$filepath);            
            $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            $display .= COM_siteFooter();
            COM_output($display);
            exit();  
        }
        else {
           // Since everything has succeeded successfully, display any files that should be included, exit
           $display = COM_siteHeader('');
           $display .= COM_startBlock($LANG_RMANAGER['title'], '', COM_getBlockTemplate('_msg_block', 'header'));
           $display .= $LANG_RMANAGER[43];
        }
 
    }
    else if ($_GET['cmd'] == 4) {
        // Edit the plugin data now.. 
        // So get data, and make sure ID is ok
        $p_id = (int) ( (isset($_GET['pid'])) ? $_GET['pid'] : 0);

        // Check and make sure the plugin id is not 0. If it is 0, then lets throw an error and get out
        if ($p_id == 0) {
            $display = COM_siteHeader('');
            $display .= COM_showMessageText($LANG_RMANAGER['error_invalpluginid']);            
            $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            $display .= COM_siteFooter();
            COM_output($display);
            exit();
        }

        // Get the data for this plugin
        $tblname = $_DB_table_prefix.'repository_listing';
        $result = DB_query("SELECT * FROM {$tblname} WHERE id = '{$p_id}';");
        $row = DB_fetchArray($result);
        
        // If it is NULL, it means that the id entered was invalid (plugin does not exist)
        if ($row == FALSE) {
            $display = COM_siteHeader('');
            $display .= COM_showMessageText($LANG_RMANAGER['error_invalpluginid']);            
            $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
            $display .= COM_siteFooter();
            COM_output($display);
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
        $data = new Template($_CONF['path'].'plugins/rmanager/templates');
        $data->set_file(array('index'=>'editplugin.thtml'));
        $data->set_var('lang_0', $LANG_RMANAGER_UPLUGIN[75]);
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
	
        // Set hard coded values now
	$data->set_var('value_0', $row['id']);
        $data->set_var('value_2', $row['name']);        
        $data->set_var('value_3', $row['version']);     
          
        // We have to figure out what databases are supported.. 
        // Since it is controlled using the bit method, we have to now &AND the value for each position, and find it its on or off
        $db = $row['db'];
  
        // MySQL is it supported?
        if ( ($db & 1) === 0) {
            $data->set_var('value_4a', 'no');
        }
        else {
            $data->set_var('value_4a', 'yes');            
        }

        // MSSQL
        if ( ($db & 2) === 0) {
            $data->set_var('value_4b', 'no');
        }
        else {
            $data->set_var('value_4b', 'yes');            
        }
   
        // PSGRE
        if ( ($db & 4) === 0) {
            $data->set_var('value_4c', 'no');
        }
        else {
            $data->set_var('value_4c', 'yes');            
        }

	$data->set_var('value_5', $row['dependencies']);        
        $data->set_var('value_6', $row['soft_dep']);        
        $data->set_var('value_7', $row['short_des']);             
        $data->set_var('value_9', $row['credits']);        
        $data->set_var('value_10', $row['state']);        
        $data->parse('output','index');
        $display .= $data->finish($data->get_var('output'));

    }
    else if ($_GET['cmd'] == 5) {
        // Show Upload Patch
        $data = new Template($_CONF['path'].'plugins/rmanager/templates');
	$data->set_file(array('index'=>'addpatch.thtml'));
	// This instruction sets the javascript language variables
	
	// Get repositories from database	
	$data->set_var('lang_45', $LANG_RMANAGER_UPLUGIN[45]);
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
        $data = new Template($_CONF['path'].'plugins/rmanager/templates');
	$data->set_file(array('index'=>'upgrade.thtml'));
	// This instruction sets the javascript language variables
	
	// Get repositories from database	
	$data->set_var('lang_1', $LANG_RMANAGER_UPLUGIN[1]);
	$data->set_var('lang_69', $LANG_RMANAGER_UPLUGIN[69]);
	$data->set_var('lang_70', $LANG_RMANAGER_UPLUGIN[70]);
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
            $mysql = (isset($_POST['GEEKLOG_PLMYSQL'])) ? $_POST['GEEKLOG_PLMYSQL'] : "no";
            $mssql = (isset($_POST['GEEKLOG_PLMSSQL'])) ? $_POST['GEEKLOG_PLMSSQL'] : "no";
            $postgre = (isset($_POST['GEEKLOG_PLPOSTGRE'])) ? $_POST['GEEKLOG_PLPOSTGRE'] : "no";
            $dependencies = (isset($_POST['GEEKLOG_PLDEPENDENCIES'])) ? $_POST['GEEKLOG_PLDEPENDENCIES'] : "";
            $sys_dependencies = (isset($_POST['GEEKLOG_PLSOFTDEP'])) ? $_POST['GEEKLOG_PLSOFTDEP'] : "";
            $shrt_des = (isset($_POST['GEEKLOG_SHRTDES'])) ? $_POST['GEEKLOG_SHRTDES'] : "";
            $credits = (isset($_POST['GEEKLOG_CREDITS'])) ? $_POST['GEEKLOG_CREDITS'] : "";
            $update = (isset($_POST['GEEKLOG_UPDATE'])) ? $_POST['GEEKLOG_UPDATE'] : "0";
            $state = (isset($_POST['GEEKLOG_STATE'])) ? $_POST['GEEKLOG_STATE'] : "stable";

            // Check required variables for validity
            // And we also have to check to make sure that plugin already exists
            if (($name == "") or ($version == "") or ($shrt_des == "") or (($mysql == "no") and ($mssql == "no") and ($postgre == "no"))) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[23]); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
                exit();
            }
            
           // Check if a plugin of the same name exists in the repository
	   $tblname = $_DB_table_prefix.'repository_listing';
           $result = DB_query("SELECT name FROM {$tblname} WHERE name = '{$name}';");
   
           // Get result, or if null, the plugin can be uploaded since it won't exist
	   $res = DB_fetchArray($result);
           
           if (($res !== FALSE) and ($update == "0")) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[24]); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
                exit();               
           }
	   
	   // Is the file size too large (MAX_UPLOADED_FILE_SIZE)
	   if ($_FILES['GEEKLOG_FILE_PUPLOAD']['size'] > MAX_FILE_UPLOAD_SIZE) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[30]. ' invalsize )'); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
                exit();    	       
	   }
	   
	   // Have to make sure its an uploaded file, and not a trick to get to /etc/psswd etc
	   if ( (!(is_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']))) or ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_OK )) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[30]. ' '.$_FILES['GEEKLOG_FILE_PUPLOAD']['error']. ')'); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
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
                   $display = COM_siteHeader('');
                   $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[29]); 
		   $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                   $display .= COM_siteFooter();
                   COM_output($display);
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
           $required_fnames = array(1=> "autoinstall.php", 2=> "autouninstall.php", 4 => "functions.inc", 8 => "config.php");
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
           if ($mysql == "yes") {
	       $database_bit_value = $database_bit_value | 1;
           }

           if ($mssql == "yes") {
	       $database_bit_value = $database_bit_value | 2;
           }

           if ($postgre == "yes") {
	       $database_bit_value = $database_bit_value | 4;
           }

           // Insert values into the database
           #http://wiki.geeklog.net/index.php/Using_COM_applyFilter
           $name = COM_applyFilter($name);
           $version = COM_applyFilter($version);
           $dependencies = COM_applyFilter($dependencies);
           $sys_dependencies = COM_applyFilter($sys_dependencies);
           $shrt_des = COM_applyFilter($shrt_des);
           $credits = COM_applyFilter($credits);
           $state = COM_applyFilter($state);
	  
	   // Send query to the database
           $tblname = $_DB_table_prefix.'repository_listing';
	   // This type of string format needs to be against the 'wall' and not indented for it to work -- 
$qstr = <<<HETERO
INSERT INTO {$tblname}(ext, name, version, db, dependencies, soft_dep, short_des, credits, uploading_author, install, state, moderation) 
VALUES('{$full_ext}', '{$name}','{$version}','{$database_bit_value}','{$dependencies}','{$sys_dependencies}','{$shrt_des}','{$credits}','{$_USER['uid']}','{$automatic_installer}','{$state}', '{$plugin_moderated}');
HETERO;

           $result = DB_query($qstr);
	       
	   $MYSQL_ID = DB_insertId();    
     
           // Continue with the upload, if the user figured in error, he can update the plugin later when the message says he can
           // Time to move the file over to the repository directory
	   // The upload path can either be the tmp upload or the real repository
	   if ($plugin_moderated === 1) {
	       $output_repository = "tmp_uploads/".$name.'_'.$version.'_'.$state.'_'.$MYSQL_ID.$full_ext;  
	   }
	   else {
	       $output_repository = "../repository/".$name.'_'.$version.'_'.$state.'_'.$MYSQL_ID.$full_ext;
	   }
	   
	   // Move the archive now
	   if (!(move_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], $output_repository))) {
               $display = COM_siteHeader('');
               $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[31]); 
               $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
               $display .= COM_siteFooter();
               COM_output($display);
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
	   $display .= COM_startBlock($LANG_RMANAGER['title'], '', COM_getBlockTemplate('_msg_block', 'header'));
	   if ($plugin_moderated == TRUE) {
               $display .= $LANG_RMANAGER_UPLUGIN[39]."<br /><br />".$LANG_RMANAGER_UPLUGIN[40].$filesmissing_msg;        
	   }
	   else {
	       $display .= $LANG_RMANAGER_UPLUGIN[39]."<br /><br /><a href='{$output_repository}'>{$name}_{$version}_{$state}_{$MYSQL_ID}{$full_ext}</a>".$filesmissing_msg;     
	   }
        }
        else if (isset($_POST['submit_edit_plugin'])) {
            // Now, get variables
            $name = (isset($_POST['GEEKLOG_PLNAME'])) ? $_POST['GEEKLOG_PLNAME'] : "";
            $version = (isset($_POST['GEEKLOG_PLVERSION'])) ? $_POST['GEEKLOG_PLVERSION'] : "";
            $mysql = (isset($_POST['GEEKLOG_PLMYSQL'])) ? $_POST['GEEKLOG_PLMYSQL'] : "no";
            $mssql = (isset($_POST['GEEKLOG_PLMSSQL'])) ? $_POST['GEEKLOG_PLMSSQL'] : "no";
            $postgre = (isset($_POST['GEEKLOG_PLPOSTGRE'])) ? $_POST['GEEKLOG_PLPOSTGRE'] : "no";
            $dependencies = (isset($_POST['GEEKLOG_PLDEPENDENCIES'])) ? $_POST['GEEKLOG_PLDEPENDENCIES'] : "";
            $sys_dependencies = (isset($_POST['GEEKLOG_PLSOFTDEP'])) ? $_POST['GEEKLOG_PLSOFTDEP'] : "";
            $shrt_des = (isset($_POST['GEEKLOG_SHRTDES'])) ? $_POST['GEEKLOG_SHRTDES'] : "";
            $credits = (isset($_POST['GEEKLOG_CREDITS'])) ? $_POST['GEEKLOG_CREDITS'] : "";
            $update = (isset($_POST['GEEKLOG_UPDATE'])) ? $_POST['GEEKLOG_UPDATE'] : "0";
            $state = (isset($_POST['GEEKLOG_STATE'])) ? $_POST['GEEKLOG_STATE'] : "stable";

            // Check required variables for validity
            // And we also have to check to make sure that plugin already exists
            if (($name == "") or ($version == "") or ($shrt_des == "") or (($mysql == "no") and ($mssql == "no") and ($postgre == "no"))) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[23]); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
                exit();
            }	    

	    // New file, or same file and just edit Database
	    if ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_NO_FILE ) {
	        // Move the new file to overwrite the existing one
		
		// Is the file size too large (MAX_UPLOADED_FILE_SIZE)
	        if ($_FILES['GEEKLOG_FILE_PUPLOAD']['size'] > MAX_FILE_UPLOAD_SIZE) {
                    $display = COM_siteHeader('');
                    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[30]. ' invalsize )'); 
                    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                    $display .= COM_siteFooter();
                    COM_output($display);
                    exit();    	       
	        }
		
	        // Have to make sure its an uploaded file, and not a trick to get to /etc/psswd etc
	        if ( (!(is_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']))) or ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_OK )) {
                    $display = COM_siteHeader('');
                    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[30]. ' '.$_FILES['GEEKLOG_FILE_PUPLOAD']['error']. ')'); 
                    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                    $display .= COM_siteFooter();
                    COM_output($display);
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
                        $display = COM_siteHeader('');
                        $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[29]); 
		        $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                        $display .= COM_siteFooter();
                        COM_output($display);
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
                $required_fnames = array(1=> "autoinstall.php", 2=> "autouninstall.php", 4 => "functions.inc", 8 => "config.php");
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
           if ($mysql == "yes") {
	       $database_bit_value = $database_bit_value | 1;
           }

           if ($mssql == "yes") {
	       $database_bit_value = $database_bit_value | 2;
           }

           if ($postgre == "yes") {
	       $database_bit_value = $database_bit_value | 4;
           }

            // Insert values into the database
            #http://wiki.geeklog.net/index.php/Using_COM_applyFilter
            $name = COM_applyFilter($name);
            $version = COM_applyFilter($version);
            $dependencies = COM_applyFilter($dependencies);
            $sys_dependencies = COM_applyFilter($sys_dependencies);
            $shrt_des = COM_applyFilter($shrt_des);
            $credits = COM_applyFilter($credits);
            $state = COM_applyFilter($state);
	    $id = (int)((isset($_GET['pid'])) ? $_GET['pid'] : 0);

            // Send query to the database
            $tblname = $_DB_table_prefix.'repository_listing';

            // Does the user have permissions for this plugin?
            // Check if author
            $result = DB_query("SELECT id FROM {$tblname} WHERE uploading_author = {$_USER['uid']};");
            
            if (DB_fetchArray($result) === FALSE) {
                // Is maintainer
                $tbl2 = $_DB_table_prefix.'repository_maintainer';
                $result = DB_query("SELECT * FROM {$tbl2} WHERE maintainer_id = {$_USER['uid']} AND plugin_id = {$id};");
                if ($result === NULL) {
                    $display = COM_siteHeader('');
                    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[74]); 
                    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                    $display .= COM_siteFooter();
                    COM_output($display);
                    exit();    
                }
            }
           	  
	    // This type of string format needs to be against the 'wall' and not indented for it to work -- 
$qstr = <<<HETERO
UPDATE {$tblname} SET ext = '{$full_ext}', name = '{$name}', version = '{$version}', db = '{$database_bit_value}', dependencies = '{$dependencies}', soft_dep = '{$sys_dependencies}', short_des = '{$shrt_des}', credits = '{$credits}', install = '{$automatic_installer}', state = '{$state}', moderation = '{$plugin_moderated}' WHERE id = '{$id}';
HETERO;

            // Run Query
            $result = DB_query($qstr);
	       
	    $MYSQL_ID = DB_insertId();    
	    
	    // New file, or same file and just edit Database
  	    if ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_NO_FILE ) {
                // Continue with the upload, if the user figured in error, he can update the plugin later when the message says he can
                // Time to move the file over to the repository directory
                // The upload path can either be the tmp upload or the real repository
	        if ($plugin_moderated === 1) {
	            $output_repository = "tmp_uploads/".$name.'_'.$version.'_'.$state.'_'.$id.$full_ext;  
	        }
	        else {
	            $output_repository = "../repository/".$name.'_'.$version.'_'.$state.'_'.$id.$full_ext;
	        }
	   
	        // Move the archive now
	        if (!(move_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], $output_repository))) {
                    $display = COM_siteHeader('');
                    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[31]); 
                    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                    $display .= COM_siteFooter();
                    COM_output($display);
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
	   $display .= COM_startBlock($LANG_RMANAGER['title'], '', COM_getBlockTemplate('_msg_block', 'header'));
	   if ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] ===  UPLOAD_ERR_NO_FILE) {
	       $display .= $LANG_RMANAGER_UPLUGIN[76];    
	   }
	   else if ($plugin_moderated == TRUE) {
               $display .= $LANG_RMANAGER_UPLUGIN[39]."<br /><br />".$LANG_RMANAGER_UPLUGIN[40].$filesmissing_msg;        
	   }

	   else {
	       $display .= $LANG_RMANAGER_UPLUGIN[39]."<br /><br /><a href='{$output_repository}'>{$name}_{$version}_{$state}_{$MYSQL_ID}{$full_ext}</a>".$filesmissing_msg;     
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
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[57]); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
                exit();
            }
            // Send query to the database
            $tblname = $_DB_table_prefix.'repository_listing';

            // Does the user have permissions for this plugin?
            // Check if author
            $result = DB_query("SELECT id FROM {$tblname} WHERE uploading_author = {$_USER['uid']};");
            
            if (DB_fetchArray($result) === FALSE) {
                // Is maintainer
                $tbl2 = $_DB_table_prefix.'repository_maintainer';
                $result = DB_query("SELECT * FROM {$tbl2} WHERE maintainer_id = {$_USER['uid']} AND plugin_id = {$id};");
                if ($result === NULL) {
                    $display = COM_siteHeader('');
                    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[74]); 
                    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                    $display .= COM_siteFooter();
                    COM_output($display);
                    exit();    
                }
            }
	    
           // Check if a patch of the same name exists in the repository
	   $tblname = $_DB_table_prefix.'repository_patches';
           $result = DB_query("SELECT name FROM {$tblname} WHERE name = '{$name}';");
   
           // Get result, or if null, the patch can be uploaded since it won't exist
	   $res = DB_fetchArray($result);
           
           if (($res !== FALSE) and ($update == "0")) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[58]); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
                exit();               
           }
	   
	   // Is the file size too large (MAX_UPLOADED_FILE_SIZE)
	   if ($_FILES['GEEKLOG_FILE_PUPLOAD']['size'] > MAX_FILE_UPLOAD_SIZE) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[30]. ' invalsize )'); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
                exit();    	       
	   }
	   
	   // Have to make sure its an uploaded file, and not a trick to get to /etc/psswd etc
	   if ( (!(is_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name']))) or ($_FILES['GEEKLOG_FILE_PUPLOAD']['error'] !==  UPLOAD_ERR_OK )) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[30]. ' '.$_FILES['GEEKLOG_FILE_PUPLOAD']['error']. ')'); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
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
                   $display = COM_siteHeader('');
                   $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[29]); 
		   $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                   $display .= COM_siteFooter();
                   COM_output($display);
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
           $required_fnames = array(1=> "install_patch.php");
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
           $name = COM_applyFilter($name);
           $version = COM_applyFilter($version);
           $vtype = COM_applyFilter($vtype);
           $severity = COM_applyFilter($severity);
	   $des = COM_applyFilter($des);
	   
	   // Send query to the database
           $tblname = $_DB_table_prefix.'repository_patches';
	   // This type of string format needs to be against the 'wall' and not indented for it to work -- 
$qstr = <<<HETERO
INSERT INTO {$tblname}(name, plugin_id, uploading_author, applies_num, version, ext, severity, automatic_install, moderation, description) 
VALUES('{$name}','{$id}','{$_USER['uid']}','{$vtype}','{$version}','{$full_ext}','{$severity}','{$automatic_installer}', '{$plugin_moderated}', '{$des}');
HETERO;

           $result = DB_query($qstr);
	       
	   $MYSQL_ID = DB_insertId();    
     
           // Continue with the upload, if the user figured in error, he can update the patch later when the message says he can
           // Time to move the file over to the repository directory
	   // The upload path can either be the tmp upload or the real repository
	   if ($plugin_moderated === 1) {
	       $output_repository = "tmp_uploads/patches/".$name.'_'.$version.'_'.$vtype.'_'.$MYSQL_ID.$full_ext;  
	   }
	   else {
	       $output_repository = "../repository/patches/".$name.'_'.$version.'_'.$vtype.'_'.$MYSQL_ID.$full_ext;
	   }
	   
	   // Move the archive now
	   if (!(move_uploaded_file($_FILES['GEEKLOG_FILE_PUPLOAD']['tmp_name'], $output_repository))) {
               $display = COM_siteHeader('');
               $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[31]); 
               $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
               $display .= COM_siteFooter();
               COM_output($display);
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
	   $display .= COM_startBlock($LANG_RMANAGER['title'], '', COM_getBlockTemplate('_msg_block', 'header'));
	   if ($plugin_moderated == TRUE) {
               $display .= $LANG_RMANAGER_UPLUGIN[39]."<br /><br />".$LANG_RMANAGER_UPLUGIN[40].$filesmissing_msg;        
	   }
	   else {
	       $display .= $LANG_RMANAGER_UPLUGIN[39]."<br /><br /><a href='{$output_repository}'>{$name}_{$version}_{$state}_{$MYSQL_ID}{$full_ext}</a>".$filesmissing_msg;     
	   }

	    
	}
        else if (isset($_POST['submit_upload_upgrade'])) {
            // Get the POST variables
            $version = (isset($_POST['GEEKLOG_PLVERSION'])) ? $_POST['GEEKLOG_PLVERSION'] : null;
            $id = (int) ( (isset($_GET['id'])) ? $_GET['id'] : 0);

            if( ($version == null) or ($id == 0)) {
                $display = COM_siteHeader('');
                $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[72]); 
                $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
                $display .= COM_siteFooter();
                COM_output($display);
                exit();               

            }

            // Insert into the database
            #http://wiki.geeklog.net/index.php/Using_COM_applyFilter
            $version = COM_applyFilter($version);

           // Send query to the database
           $tblname = $_DB_table_prefix.'repository_upgrade';
           // This type of string format needs to be against the 'wall' and not indented for it to work -- 
$qstr = <<<HETERO
INSERT INTO {$tblname}(plugin_id, version) VALUES('{$id}','{$version}');
HETERO;

           $result = DB_query($qstr);
            
           $display = COM_siteHeader('');
           $display .= COM_startBlock($LANG_RMANAGER['title'], '', COM_getBlockTemplate('_msg_block', 'header'));
           $display .= $LANG_RMANAGER_UPLUGIN[73];
         
              
        }
        else {
            $display = COM_siteHeader('');
            $display .= COM_showMessageText($LANG_RMANAGER['error_sinvalidget']);   
        }
    }
    else {
        $display = COM_siteHeader('');
        $display .= COM_showMessageText($LANG_RMANAGER['error_invalidget']);   
    }
    

}
else {
    $display = COM_siteHeader('');
    $display .= COM_showMessageText($LANG_RMANAGER['error_invalidget']);   
}

$display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
$display .= COM_siteFooter();
COM_output($display);
?>
