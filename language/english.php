<?php

###############################################################################
# english.php
#
# This is the English language file for the Geeklog Repository Management plugin
#
# Copyright (C) 2009 Tim Patrick
# timpatrick AT gmail DOT com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################

global $LANG32;

$LANG_RMANAGER = array(
    'title'             => 'Geeklog Repository Manager - User plugin management',
    'title2'            => 'Geeklog Repository Manager - Admin plugin management',
    'error_invalidget'  => 'Invalid Page Request - Please try again',
    'error_invalpluginid' => 'Invalid Plugin ID',
    'error_pdel_noperm' => 'Error: You are not authorized to delete this plugin from the repository',
    'error_pdel_erm' => 'Error: Cannot remove plugin file from repository. Please do so manually.<br />File is: '
 );
 
 $LANG_RMANAGER_UPLUGIN = array(
    0 => 'Upload a new plugin (or new version) to the repository',
    1 => 'All fields are required (Unless otherwise stated)',
    2 => 'Plugin Archive (Accepted Format: tar.gz, tar.bz2, tar, zip)',
    3 => 'Plugin Name (Max 100 chars)<br />The `Display Name`, eg. Repository Manager',
    4 => 'Version Number (Pref. 1.0.0 format)',
    5 => 'And now please answer some questions about your plugin',
    6 => 'Does your plugin support MySQL Databases:',
    7 => 'No',
    8 => 'Yes',
    9 => 'What about MSSQL Databases:',
    10 => 'And finally POSTRGRE Databases:',
    11 => 'Please list any plugins that must be installed for this plugin to operate.<br />Separate each plugin name with a comma. Eg. MyPlugin,MyPlugin1.<br />Make sure these are the exact plugin names:',
    12 => 'Please list any software dependencies that are needed for this plugin to operate.<br />Separate each software name with a comma. Eg. PHP-APC, PHP-XML<br />Make sure these are the exact software names:',
    13 => 'Please enter a detailed description of your plugin',
    14 => 'And now a longer description of your plugin, more indepth.<br />(If 100 characters is all you need, leave this blank)',
    15 => 'Last of all, please list any credits or developers for this plugin',
    16 => 'Upload Plugin',
    17 => 'Reset Form',
    18 => 'Error: Plugin name must exist and be greater than 3 and less than 100 characters',
    19 => 'Error: Plugin version must be one or more characters',
    20 => 'Error: You have to support at least ONE database :)',
    21 => 'Error: I am going to have to insist that you have at least a small description',
    22 => 'An error occurred - Please try again! Error',
    23 => 'The following fields are required - Plugin Full Name, Plugin Small Name, Plugin Version, at least one database supported, and a short description!',
    24 => 'A plugin with this name already exists in the database - please choose another name. If you are trying to overwrite an existing plugin, use the edit function instead ',
    25 => 'Only tarballs (.tar) or zipped (.zip) files allowed',
    26 => 'Is this a new plugin or do you want to overwrite an existing one with this new name?',
    27 => 'New Plugin',
    28 => 'Overwrite Existing (Update)',
    29 => 'Invalid File Format - Must be tarball or zipped file (.tar.gz, .tar.bz2, .zip, .tar)',
    30 => 'File Upload Error - Please try again (Ref: ',
    31 => 'Error moving uploaded file - Please try again',
    32 => 'What state is this plugin uploaded as?',
    33 => 'stable',
    34 => 'dev',
    35 => 'depreciated',
    36 => 'insecure',
    37 => 'beta',
    38 => 'final',
    39 => 'Plugin was successfully uploaded! The direct path to your plugin: ',
    40 => 'Your plugin must be approved by a moderator before it is uploaded completely.',
    41 => 'Warning: Your plugin is not able to be offered for auto - install because of the following files missing: ',
    42 => 'Once you have created these files, simply re upload your plugin to overwrite it.',
    43 => 'Plugin has been deleted successfully!',
    44 => 'Plugin Archive (Accepted Format: tar.gz, tar.bz2, tar, zip)<br />If you wish to keep the existing plugin, do not upload a new plugin',
    45 => 'Upload a new patch for the selected plugin',
    46 => 'Patch Archive (Accepted Format: tar.gz, tar.bz2, tar, zip)',
    47 => 'Patch Name (Usually plugin.path_number)',
    48 => 'This patch is for versions',
    49 => 'All Versions',
    50 => 'Less than',
    51 => 'Less than &amp; equal to',
    52 => 'Equal to',
    53 => 'Greater than &amp; equal to',
    54 => 'Greater than',
    55 => 'this version:',
    56 => 'Upload Patch',
    57 => 'The following fields are required - Patch Name, Patch Version, Description and a valid patch id!',
    58 => 'A patch with this name already exists - please choose another name. ',
    59 => 'Patch was successfully uploaded! The direct path to your plugin:',
    60 => 'Your patch must be approved by a moderator before it is uploaded completely.',
    61 => 'The severity of the patch',
    62 => 'Low',
    63 => 'High',
    64 => 'Is this an update or an upgrade?<br />Update is a patch for an existing installed plugin<br />Upgrade will upgrade the existing installed plugin to the newer version',
    65 => 'Update',
    66 => 'Upgrade',
    67 => 'Warning: Your patch is not able to be offered for auto - update because of the following files missing: ',
    68 => 'Once you have created these files, simply re upload your patch to overwrite',
    69 => 'Announce a new upgrade for your plugin',
    70 => 'Plugin Version Number this upgrade applies to',
    71 => 'Send Upgrade Announcement',
    72 => 'Invalid version # / plugin id',
    73 => 'Upgrade Announcement has been recorded. Site admins will be notified upon next login!',
    74 => 'Error: You do not have the required permissions to perform this action',
    75 => 'Edit your existing plugin',
    76 => 'Your plugin was successfully edited!',
    77 => 'Edit',
    78 => 'Delete',
    79 => 'Patch',
    80 => 'Upgrade',
    81 => 'Maintainer',
    82 => 'Author',
    83 => 'Notice: You do not have any plugins that you are maintaining or have created!',
    84 => 'Plugin Name',
    85 => 'You are a/the',
    86 => 'Edit',
    87 => 'Delete',
    88 => 'Patch',
    89 => 'Upgrade',
    90 => 'Short description of the patch',
    91 => 'Here you can manage any plugins you have created, by selecting one of the links below to get started',
    92 => '>Upload a new plugin',
    93 => 'List and Manage Plugins',
    94 => 'Here you are able to administrate all plugins and patches in the repository',
    95 => 'List of all plugins that require approval before being allowed in the official repository',
    96 => 'Download',
    97 => 'Approve',
    98 => 'Delete',
    99 => 'Title',
    100 => 'Uploader',
    101 => 'Error: Invalid plugin id',
    102 => 'The item does not exist',
    103 => 'for plugin #',
    104 => 'Plugin was successfully approved',
    105 => 'Plugin was successfully deleted',
    106 => 'Plugin removed from database, however error removing from file system - path is at:<br />',
    107 => 'Patch was successfully deleted',
    108 => 'Patch removed from database, however error removing from file system - path is at:<br />',
    109 => 'Patch was successfully approved',
    110 => 'List of all patches that require approval before being allowed in the official repository',
    111 => 'Add Maintainer',
    112 => 'All plugins currently in the repository',
    113 => '<br />You currently have no items in your repository',
    114 => 'All patches currently in the repository',
    115 => 'List of all plugins in the repository',
    116 => 'List of all patches in the repository',
    117 => 'Add a maintainer to this plugin',
    118 => 'Username',
    119 => 'Add Maintainer',
    120 => 'Invalid username or plugin',
    121 => 'The username you entered does not exist',
    122 => 'Maintainer added successfully',
    123 => 'The user you entered is already a maintainer for this plugin',
    124 => 'Search by plugin name or version for a plugin',
    125 => 'Name',
    126 => 'Limit results',
    127 => 'Search',
    128 => 'Search',
    129 => 'Version',
    130 => 'Search for a patch by patch name or version',
    131 => 'Show Patches',
    132 => 'Plugin Name (Directory Name), eg. rmanager',
    133 => 'Error: Invalid Plugin ID',
    134 => 'Error: You are not authorized to delete this plugin from the repository',
    135 => 'Error: Cannot remove plugin file from repository. Please do so manually.<br />File is: %s',
    136 => 'Invalid Page Request - Please try again',
    137 => 'Repository Manager'

 );

 // Display Plugins
 $LANG_RMANAGER_DPLUGIN = array(
     0 => "List of all plugins associated with you (Author and/or Maintainer)"


 );

 // Admin Data
 $LANG_RMANAGER_ADMIN = array(
     0 => 'You have ',
     1 => ' plugin(s) waiting for approval! (Click to load)',
     2 => 'You have ',
     3 => ' patch(es) waiting for approval! (Click to load)'
 );
 
$LANG_configselects['repository'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    10 => array('Plugin must be approved' => 'repository_moderated', 'Maximum file size for a patch (MB) - Value must include M after integer' => 'max_pluginpatch_upload'),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3)
);

$LANG_confignames['repository'] = array(
    'repository_moderated' => 'Plugin must be approved before being available to public', 
    'max_pluginpatch_upload' => 'Maximum file size for a patch (Bytes)'
    );

$LANG_configsubgroups['repository'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['repository'] = array(
    'fs_main' => 'General Repository Settings',
    'fs_permissions' => 'Default Permissions'
);

?>
