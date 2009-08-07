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

$tblname = $_DB_table_prefix."repository_listing";

$_SQL[] = "
CREATE TABLE {$tblname} (
   id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
   name VARCHAR(255) DEFAULT '',
   fname VARCHAR(255) DEFAULT '',
   version VARCHAR(255) DEFAULT NULL,
   db TINYINT NOT NULL, 
   dependencies TEXT DEFAULT NULL,
   soft_dep TEXT DEFAULT NULL,
   short_des TEXT DEFAULT NULL,
   credits TEXT DEFAULT NULL,
   uploading_author INT NOT NULL,
   vett INT DEFAULT 0  DEFAULT 0,
   downloads INT DEFAULT 0,
   install INT(1) DEFAULT 0,
   state VARCHAR(255) DEFAULT NULL,
   ext VARCHAR(100) DEFAULT NULL,
   moderation TINYINT DEFAULT 1,
   INDEX(id)
) TYPE=MyISAM;
";

$tblname = $_DB_table_prefix."repository_maintainers";

$_SQL[] = "
CREATE TABLE {$tblname}
(
    plugin_id INT NOT NULL,
    maintainer_id INT NOT NULL
) TYPE=MyISAM;
";

$tblname = $_DB_table_prefix."repository_patches";

$_SQL[] = "
CREATE TABLE {$tblname}
(
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name VARCHAR(255),
    plugin_id INT,
    uploading_author INT,
    applies_num VARCHAR(255),
    version VARCHAR(255),
    ext VARCHAR(100),
    severity VARCHAR(255),
    automatic_install TINYINT,
    description TEXT,
    moderation TINYINT,
    update_number INT 
) TYPE=MyISAM;
";

$tblname = $_DB_table_prefix."repository_upgrade";

$_SQL[] = "
CREATE TABLE {$tblname}
(
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    plugin_id INT,
    version VARCHAR(255),
    version2 VARCHAR(255),
    description TEXT,
    ext VARCHAR(100),
    moderation TINYINT,
    automatic_install TINYINT
) TYPE=MyISAM;
";

?>
