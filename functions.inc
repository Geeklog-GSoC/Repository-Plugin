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
 
    return array ($LANG_RMANAGER_UPLUGIN[137],
                  $_CONF['site_admin_url'] . '/plugins/repository/index.php',
                  0);
}

/**
* This will put an option for the repository manager in the command and control block on
* moderation.php
*
*/
function plugin_cclabel_repository()
{
    global $_CONF, $LANG_RMANAGER_UPLUGIN;

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
function plugin_geticon_repository ()
{
    global $_CONF;

    return $_CONF['site_url'] . '/repository/images/repository.png';
}

