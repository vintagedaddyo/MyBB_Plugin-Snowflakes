<?php
/*
 * MyBB: Snowflakes
 *
 * File: snowflakes.php
 * 
 * Author: Vintagedaddyo
 *
 * MyBB Version: 1.8
 *
 * Plugin Version: 1.0
 * 
 * Based on the script from: https://www.go4u.de/snowflakes.htm
 *
 */

// Disallow direct access to this file for security reasons

if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("usercp_options_end", "snowflakes_usercp");
$plugins->add_hook("usercp_do_options_end", "snowflakes_usercp");
$plugins->add_hook('pre_output_page','snowflakes');

function snowflakes_info()
{
   global $lang;

    $lang->load("snowflakes");
    
    $lang->snowflakes_Desc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->snowflakes_Desc;

    return Array(
        'name' => $lang->snowflakes_Name,
        'description' => $lang->snowflakes_Desc,
        'website' => $lang->snowflakes_Web,
        'author' => $lang->snowflakes_Auth,
        'authorsite' => $lang->snowflakes_AuthSite,
        'version' => $lang->snowflakes_Ver,
        'compatibility' => $lang->snowflakes_Compat
    );
}

function snowflakes_install() {
    global $db;
    
    // Add field for user option
    
    $db->query("ALTER TABLE ".TABLE_PREFIX."users ADD showSnowflakes int NOT NULL default '1'");
}

function snowflakes_is_installed()
{
    global $db;
    
    if($db->field_exists("showSnowflakes", "users"))
    {
        return true;
    }
    else 
    {
        return false;
    }
}

function snowflakes_uninstall()
{
    global $db;
    
    if($db->field_exists("showSnowflakes", "users"))
        $db->query("ALTER TABLE ".TABLE_PREFIX."users DROP COLUMN showSnowflakes");
}

function snowflakes_usercp() {
    global $db, $mybb, $templates, $user, $lang;
    $lang->load('snowflakes');
    
    if($mybb->request_method == "post")
    {
        $update_array = array(
            "showSnowflakes" => intval($mybb->input['showSnowflakes'])
        );      
        $db->update_query("users", $update_array, "uid = '".$user['uid']."'");
    }
    
    $add_option = '</tr><tr>
<td valign="top" width="1"><input type="checkbox" class="checkbox" name="showSnowflakes" id="showSnowflakes" value="1" {$GLOBALS[\'$showSnowflakesChecked\']} /></td>
<td><span class="smalltext"><label for="showSnowflakes">{$lang->snowflakes_show_question}</label></span></td>';

    $find = '{$lang->show_codebuttons}</label></span></td>';
    $templates->cache['usercp_options'] = str_replace($find, $find.$add_option, $templates->cache['usercp_options']);
    
    $GLOBALS['$showSnowflakesChecked'] = '';
    if($user['showSnowflakes'])
        $GLOBALS['$showSnowflakesChecked'] = "checked=\"checked\"";
}

function snowflakes($page)
{
    global $mybb;
    
    if($mybb->user['showSnowflakes']) {
        $page=str_replace('</head>','<script type="text/javascript" src="'.$mybb->settings['bburl'].'/inc/plugins/snowflakes/snowflakes.js"></script></head>',$page);
    }
    
    return $page;
}

?>