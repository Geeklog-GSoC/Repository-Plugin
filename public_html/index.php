<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Repository Management                                                     |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
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


// So if the user got this far they are logged in, which is great
// So display upload form
$display .= COM_siteHeader('');

if ($_GET['msg']) {
    $display .= COM_showMessageText($LANG_RMANAGER_UPLUGIN[(int)$_GET['msg']]);
}
else if ($_GET['tmsg']) {
    $display .= ShowTMessageRManager((int)$_GET['tmsg']);
}

$display .= COM_startBlock($LANG_RMANAGER['title'], '', COM_getBlockTemplate('_msg_block', 'header'));

$data = new Template($_CONF['path'].'plugins/repository/templates');
$data->set_file(array('index'=>'indexpiece.thtml'));
$data->set_var('lang_91', $LANG_RMANAGER_UPLUGIN[91]);
$data->set_var('lang_92', $LANG_RMANAGER_UPLUGIN[92]);
$data->set_var('lang_93', $LANG_RMANAGER_UPLUGIN[93]);
$data->parse('output','index');
$display .= $data->finish($data->get_var('output'));

$display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
$display .= COM_siteFooter();
COM_output($display);



	
?>
