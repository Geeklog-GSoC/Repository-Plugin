<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Repository Management                                                     |
// +---------------------------------------------------------------------------+
// | autouninstall.php                                                           |
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


function plugin_autouninstall_rmanager ()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('repository_listing', 'repository_maintainers', 'repository_upgrade', 'repository_patches'),

        /* give the full name of the group, as in the db */
        'groups' => array('Repository Manager Admin'),

        /* give the full name of the feature, as in the db */
        'features' => array('rmanager.manage'),

        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(''),

        /* give all vars with their name */
        'vars'=> array()
    );

    return $out;
}

?>