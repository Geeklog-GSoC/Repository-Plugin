<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Repository Management                                                     |
// +---------------------------------------------------------------------------+
// | autoinstall.php                                                           |
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

function plugin_autoinstall_repository($pi_name) 
{
    $pi_name = 'repository';
    $pi_display_name = 'Repository Manager';
    $pi_admin = $pi_display_name.' Admin';
    $pi_user = $pi_display_name.' User';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '1.0.0',
        'pi_gl_version'   => '1.6.0',
        'pi_homepage'     => 'http://www.geeklog.net/'
    );
    
    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features',
        $pi_user => 'Is able to upload plugins to the repository'
    );

    $features = array(
        $pi_name . '.manage'  => 'Ability to manage the repository',
        $pi_name . '.upload' => 'Ability to upload plugins'
    );
    
    $mappings = array(
        $pi_name . '.manage'  => array($pi_admin),
        $pi_name . '.upload' => array($pi_admin, $pi_user);
    );

    $tables = array(
        'repository_listing',
	'repository_patches',
	'repository_maintainers',
	'repository_upgrade'
    );

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
        'tables'    => $tables
    );

    return $inst_parms;
 


}


function plugin_load_configuration_repository($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_repository();
}



?>
