<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Repository Manager Plugin 1.0.0                                           |
// +---------------------------------------------------------------------------+
// | mysql_install.php                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2009 by the following authors:                         |
// |                                                                           |
// | Authors: Tim Patrick    - timpatrick12 AT gmail DOT com                   |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+


$_SQL[] = "
CREATE TABLE [{$_TABLES['repository_listing']}] (
   [id] [int] NOT NULL IDENTITY(1,1),
   [name] [varchar] (255) DEFAULT (''),
   [fname] [varchar] (255) DEFAULT (''),
   [version] [varchar] (255) DEFAULT (null),
   [db] [tinyint] NOT NULL, 
   [dependencies] [text] DEFAULT (null),
   [soft_dep] [text] DEFAULT (null),
   [short_des] [text] DEFAULT (null),
   [credits] [text] DEFAULT (null),
   [uploading_author] [int] NOT NULL,
   [vett] [int] DEFAULT (0),
   [downloads] [int] DEFAULT (0),
   [install] [int] (1) DEFAULT (0),
   [state] [varchar] (255) DEFAULT (null),
   [ext] [varchar] (100) DEFAULT (null),
   [moderation] [tinyint] DEFAULT (1)
)  ON [PRIMARY]
";


$_SQL[] = "
CREATE TABLE [{$_TABLES['repository_maintainers']}]
(
    [plugin_id] [int] NOT NULL,
    [maintainer_id] [int] NOT NULL
) ON [PRIMARY]
";


$_SQL[] = "
CREATE TABLE [{$_TABLES['repository_patches']}]
(
    [id] [int] NOT NULL IDENTITY(1,1),
    [name] [varchar] (255),
    [plugin_id] [int],
    [uploading_author] [int],
    [applies_num] [varchar] (255),
    [version] [varchar] (255),
    [ext] [varchar] (100),
    [severity] [varchar] (255),
    [automatic_install] [tinyint],
    [description] [text],
    [moderation] [tinyint],
    [update_number] [int] 
) ON [PRIMARY]
";


$_SQL[] = "
CREATE TABLE [{$_TABLES['repository_upgrade']}]
(
    [id] [int] NOT NULL IDENTITY(1,1),
    [plugin_id] [int],
    [version] [varchar] (255),
    [version2] [varchar] (255),
    [description] [text],
    [ext] [varchar] (100),
    [moderation] [tinyint],
    [automatic_install] [tinyint]
) ON [PRIMARY]
";

?>
