<?php
header("Content-Type: text/xml");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | List of plugins in repository                                             |
// +---------------------------------------------------------------------------+
// | list.php                                                                  |
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

require_once '../../../lib-common.php';

// Output preliminary XML
echo <<<XML
<?xml version="1.0"?>

<repository
xmlns="http://www.geeklog.com"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="http://www.geeklog.com ../../xml/repository_listing.xsd">
<!-- Start Plugin List -->
XML;

// Create query, lets go
$tblname = $_TABLES['repository_listing'];
$qstr = "SELECT * FROM {$tblname} WHERE moderation = '0';";

$result = DB_query($qstr);

// Loop until we reach a 0, outputting the XML every time
while ( ($result2 = DB_fetchArray($result)) !== FALSE) {
echo <<<EEPROM
<plugin>
<id>{$result2['id']}</id>
<name><![CDATA[{$result2['name']}]]></name>
<fname><![CDATA[{$result2['fname']}]]></fname>
<version><![CDATA[{$result2['version']}]]></version>
<db><![CDATA[{$result2['db']}]]></db>
<dependencies><![CDATA[{$result2['dependencies']}]]></dependencies>
<soft_dep><![CDATA[{$result2['soft_dep']}]]></soft_dep>
<short_des><![CDATA[{$result2['short_des']}]]></short_des>
<credits><![CDATA[{$result2['credits']}]]></credits>
<vett><![CDATA[{$result2['vett']}]]></vett>
<downloads><![CDATA[{$result2['downloads']}]]></downloads>
<install><![CDATA[{$result2['install']}]]></install>
<state><![CDATA[{$result2['state']}]]></state>
<ext><![CDATA[{$result2['ext']}]]></ext>
</plugin>
EEPROM;
}

echo <<<OMM
<!-- End Plugin List -->
</repository>
OMM;
?>
