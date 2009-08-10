<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Repository Manager 1.0                                                    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API method and 2) implements all the common code needed by the Rmanager   |
// | plugin' PHP files.                                                        |
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
* Implementation of the Plugin API for the Repository Manager plugin
*
* @package RManager
*/

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die('This file can not be used on its own.');
}

$plugin_path = $_CONF['path'] . 'plugins/repository/';

/**
* Language file include
*/

$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';
if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

/*
* Check and see if we need to load the plugin configuration
*/
if (!isset($_RM_CONF['repository_moderated'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $ca_config = config::get_instance();
    $_RM_CONF = $ca_config->get_config('repository');
}

// Declare $_TABLE variable array
// Plugin Repository 
$_TABLES['repository_listing']                    = $_DB_table_prefix . 'repository_listing';
$_TABLES['repository_patches']                    = $_DB_table_prefix . 'repository_patches';
$_TABLES['repository_maintainers']                    = $_DB_table_prefix . 'repository_maintainers';
$_TABLES['repository_upgrade']                    = $_DB_table_prefix . 'repository_upgrade';
        
// Name says all
function plugin_getheadercode_repository()
{
    global $_CONF;

    $str = null;
    $str = '<link rel="stylesheet" type="text/css" href="'
        . $_CONF['site_url'] . '/repository/style.css"' . XHTML . '>';

    $str .= '<script type="text/javascript" src="'
        . $_CONF['site_url'] . '/repository/main.js"></script>';
	
    return $str;
}

/**
* Returns the items for this plugin that should appear on the main menu & user menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
*/
function plugin_getmenuitems_repository ()
{
    global $_CONF, $_USER, $LANG_RMANAGER_UPLUGIN;

    $anon = (empty ($_USER['uid']) || ($_USER['uid'] <= 1)) ? true : false;

    if ($anon) {
        return false;
    }
    
    $menuitems[$LANG_RMANAGER_UPLUGIN[137]] = $_CONF['site_url'] . '/repository/index.php';

    return $menuitems;
}

function plugin_getuseroption_repository()
{
    global $_CONF, $LANG_RMANAGER_UPLUGIN;
    
    if (!SEC_hasRights ('repository.upload')) {
        return FALSE;
    }
    
    $url = $_CONF['site_url'] . '/repository/index.php';
    return array ($LANG_RMANAGER_UPLUGIN[137], $url, '');
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_repository()
{
    global $_CONF, $_TABLES, $LANG_RMANAGER_UPLUGIN;
    
    if (!SEC_hasRights ('repository.manage')) {
        return FALSE;
    }
 
    return array ($LANG_RMANAGER_UPLUGIN[137],
                  $_CONF['site_admin_url'] . '/plugins/repository/index.php',
                  plugin_submissioncount_repository());
}

/**
* This will put an option for the repository manager in the command and control block on
* moderation.php
*
*/
function plugin_cclabel_repository()
{
    global $_CONF, $LANG_RMANAGER_UPLUGIN;
    
    if (!SEC_hasRights ('repository.manage')) {
        return FALSE;
    }
    
    return array ($LANG_RMANAGER_UPLUGIN[137],
            $_CONF['site_admin_url'] . '/plugins/repository/index.php',
            plugin_geticon_repository ());

    return false;
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_repository ($largeimg=false)
{
    global $_CONF;
    if ($largeimg) {
        return $_CONF['site_url'] . '/repository/images/repository_1.png';
    }
    else {
        return $_CONF['site_url'] . '/repository/images/repository_s.png';    
    }
}


/**
* Displays a return message in a nice format box, with links to more data functions
* 
* @param    array     $msg=NULL     Message (s) to display, each element is a line
* @param    string    $rmsg=NULL    Main display message, defaults to Repository Manager if not set
* @param    bool      $display_announce=TRUE    If set to false, this will prevent the display of the message ($msg), by default it will try to
* @param    bool      $displaylinks=TRUE    If set to false, this will not display the links on the page. 
* @param    array     $links=NULL           An array of Link Name => Href values to use instead of the default links
*
*/
function tdisplay_formattedmessage($msg=NULL, $rmsg=NULL, $display_announce=TRUE, $displaylinks=TRUE, $links=NULL)
{
    global $LANG_RMANAGER_UPLUGIN;
    
    if ($rmsg == NULL) {
        $rmsg = $LANG_RMANAGER_UPLUGIN[137];
    }
    
    if ($displaylinks == TRUE) {
        if ($links !== NULL) {
            // $links array and for each key (name) => value (href)
            $displaylinks = "";
            foreach ($links as $name => $href) {
                $displaylinks .= '<a style="font-size:12px;font-weight:normal;" href="'.$href.'">'.$name.'</a><br />';         
            }
            
            $displaylinks .= '</div>';
        }
        else {
            $displaylinks = <<<DLINKS
<a style="font-size:12px;font-weight:normal;" href="pupload.php?cmd=1">{$LANG_RMANAGER_UPLUGIN[92]}</a><br /><a style="font-size:12px;font-weight:normal;" href="pupload.php?cmd=2">{$LANG_RMANAGER_UPLUGIN[93]}</a></div>        
DLINKS;
        }
    }
    else {
        $displaylinks = "</div>";
    }
    
    // If the display announce is false, it means that dont display the last part
    if ($display_announce === FALSE) {
        //
        $display_announce = "";
    }
    else {
        // Preliminary Check - is msg an array?
        if (!is_array($msg)) {
            $array = array($msg);
        }
        else {
            $array = $msg;
        }
    
        $str_r = "";
    
        // Loop over each message, and display data
        foreach ($array as $string) {
            $str_r .= $string . '<br />';
        }
        
        $display_announce = '<div style="margin-top:135px;">'.$str_r.'</div>';
    }
    
    $img = plugin_geticon_repository(true);
$EDATA = <<<EDATA
<br /><img src="{$img}" alt="" style="width:256px;height:256px;float:left;" /><div style="margin-top:100px;font-weight:bold;font-size:16px;">{$rmsg}<br /><br />{$displaylinks}
{$display_announce}
EDATA;

   return $EDATA;

}


// Admin list function
function ADMIN_getListField_listrepositorypl_plugins($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_RMANAGER_UPLUGIN, $_TABLES;

    $retval = '';
    switch($fieldname) {
        case 'name':
            // Check to see if the repository is ok
            $retval = $A['name'] . ' ' . $A['state'] . ' ' . $A['version'];
            break;
        case 'uploader':
            // Get uploading author name from database
            $result = DB_query("SELECT username FROM {$_TABLES['users']} WHERE uid = '{$A['uploading_author']}';");
            $result2 = DB_fetchArray($result);
            
            // Does it exist?
            if ($result === FALSE) {
                $retval = "N/A";
            }
            else {
                $retval = $result2['username'];
            }
            break;
        case 'download':
            if ($A['moderation'] == 1) {
                $retval = '<a href="index.php?cmd=6&ret=1&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[96].'</a>';                        
            }
            else {
                $retval = '<a href="index.php?cmd=6&ret=7&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[96].'</a>';            
            }
            break;
        case 'approve':
            $retval = '<a href="index.php?cmd=6&ret=2&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[97].'</a>';
        case 'add_maintainer':
            $retval = '<a href="index.php?cmd=5&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[111].'</a>';
            break;
        case 'delete':
            if ($A['moderation'] == 1) {
                $retval = '<a href="javascript:void();" onclick="javascript:delconfirm(\'index.php?cmd=6&ret=3&pid='.$A['id'].'\', \''.$LANG_RMANAGER_UPLUGIN[138].'\');">'.$LANG_RMANAGER_UPLUGIN[98].'</a>';
              
            }
            else {
                $retval = '<a href="javascript:void();" onclick="javascript:delconfirm(\'index.php?cmd=6&ret=8&pid='.$A['id'].'\', \''.$LANG_RMANAGER_UPLUGIN[138].'\');">'.$LANG_RMANAGER_UPLUGIN[98].'</a>';
            }        
            break;
        case 'show_patches':
            $retval = '<a href="index.php?cmd=4&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[131].'</a>';
            break;
        case 'show_upgrades':
            $retval = '<a href="index.php?cmd=11&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[151].'</a>';
            break;            
        default:
            $retval = $fieldvalue;
            break;
    }
    return $retval;
}

// Patches
function ADMIN_getListField_listrepositorypl_patches($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_RMANAGER_UPLUGIN, $_TABLES;

    $retval = '';
    switch($fieldname) {
        case 'name':
            // Check to see if the repository is ok
            $retval = $A['name'] . ' ' . $A['applies_num'] . ' ' . $A['version'] . ' ' . $LANG_RMANAGER_UPLUGIN[103] . ' ' .$A['plugin_id'];
            break;
        case 'uploader':
            // Get uploading author name from database
            $result = DB_query("SELECT username FROM {$_TABLES['users']} WHERE uid = '{$A['uploading_author']}';");
            $result2 = DB_fetchArray($result);
            
            // Does it exist?
            if ($result === FALSE) {
                $retval = "N/A";
            }
            else {
                $retval = $result2['username'];
            }
            break;
        case 'approve':
            $retval = '<a href="index.php?cmd=6&ret=5&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[97].'</a>';
            break;
        case 'download':
           if ($A['moderation'] == 1) {
                $retval = '<a href="index.php?cmd=6&ret=4&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[96].'</a>';              
            }
            else {
                $retval = '<a href="index.php?cmd=6&ret=9&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[96].'</a>';
            }       
            break;
       case 'delete':
           if ($A['moderation'] == 1) {
                $retval = '<a href="javascript:void();" onclick="javascript:delconfirm(\'index.php?cmd=6&ret=6&pid='.$A['id'].'\', \''.$LANG_RMANAGER_UPLUGIN[138].'\');">'.$LANG_RMANAGER_UPLUGIN[98].'</a>';           
            }
            else {
                $retval = '<a href="javascript:void();" onclick="javascript:delconfirm(\'index.php?cmd=6&ret=10&pid='.$A['id'].'\', \''.$LANG_RMANAGER_UPLUGIN[138].'\');">'.$LANG_RMANAGER_UPLUGIN[98].'</a>';
            }   
            break;
        default:
            $retval = $fieldvalue;
            break;
    }
    return $retval;
}


// Upgrades
function ADMIN_getListField_listrepositorypl_upgrade($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_RMANAGER_UPLUGIN, $_TABLES;

    $retval = '';
    switch($fieldname) {
        case 'name':
            // Get uploading author name from database
            $result = DB_query("SELECT name FROM {$_TABLES['repository_listing']} WHERE id = '{$A['plugin_id']}';");
            $result2 = DB_fetchArray($result);
            $tmp = ' ' . $A['version'] . ' => ' . $A['version2'];
            // Does it exist?
            if ($result === FALSE) {
                $retval = "N/A" . $tmp;
            }
            else {
                $retval = $result2['name'] . $tmp;
            }
            break;
        case 'download':
           if ($A['moderation'] == 1) {
                $retval = '<a href="index.php?cmd=6&ret=21&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[96].'</a>';            
            }
            else {
                $retval = '<a href="index.php?cmd=6&ret=12&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[96].'</a>';
            }                  
            break;
        case 'approve':
            $retval = '<a href="index.php?cmd=6&ret=20&pid='.$A['id'].'">'.$LANG_RMANAGER_UPLUGIN[97].'</a>';
            break;
        case 'delete':
           if ($A['moderation'] == 1) {
                $retval = '<a href="javascript:void();" onclick="javascript:delconfirm(\'index.php?cmd=6&ret=23&pid='.$A['id'].'\', \''.$LANG_RMANAGER_UPLUGIN[138].'\');">'.$LANG_RMANAGER_UPLUGIN[98].'</a>';            
            }
            else {
                $retval = '<a href="javascript:void();" onclick="javascript:delconfirm(\'index.php?cmd=6&ret=13&pid='.$A['id'].'\', \''.$LANG_RMANAGER_UPLUGIN[138].'\');">'.$LANG_RMANAGER_UPLUGIN[98].'</a>';
            }              
            break;
        default:
            $retval = $fieldvalue;
            break;
    }
    return $retval;
}

// Maintainers
function ADMIN_getListField_listrepositorypl_maintainers($fieldname, $fieldvalue, $A, $icon_arr)
{
    global $_CONF, $LANG_RMANAGER_UPLUGIN, $_TABLES;

    $retval = '';
    switch($fieldname) {
        case 'name':
            // Get uploading author name from database
            $result = DB_query("SELECT username FROM {$_TABLES['users']} WHERE uid = '{$A['maintainer_id']}';");
            $result2 = DB_fetchArray($result);
            // Does it exist?
            if ($result === FALSE) {
                $retval = "N/A";
            }
            else {
                $retval = $result2['username'];
            }
            break;
       case 'delete':
            $retval = '<a href="javascript:void();" onclick="javascript:delconfirm(\'index.php?cmd=6&ret=14&pid='.$A['plugin_id'].'&mid='.$A['maintainer_id'].'\', \''.$LANG_RMANAGER_UPLUGIN[138].'\');">'.$LANG_RMANAGER_UPLUGIN[98].'</a>';
            break;
        default:
            $retval = $fieldvalue;
            break;
    }
    return $retval;
}


/**
*
* Counts the items that are submitted
*
*/
function plugin_submissioncount_repository()
{
    global $_TABLES;

    $num = 0;

    // Get plugin count
    $result = DB_query("SELECT COUNT(id) FROM {$_TABLES['repository_listing']} WHERE moderation = 1;");
    $result2 = DB_fetchArray($result);
    if ($result2 !== FALSE)
    {
        $num += $result2['COUNT(id)'];
    }
    
    // Get patch count
    $result = DB_query("SELECT COUNT(id) FROM {$_TABLES['repository_patches']} WHERE moderation = 1;");
    $result2 = DB_fetchArray($result);
    if ($result2 !== FALSE)
    {
        $num += $result2['COUNT(id)'];
    }
    
    // Get upgrade count
    $result = DB_query("SELECT COUNT(id) FROM {$_TABLES['repository_upgrade']} WHERE moderation = 1;");
    $result2 = DB_fetchArray($result);
    if ($result2 !== FALSE)
    {
        $num += $result2['COUNT(id)'];
    }

    return $num;
}


