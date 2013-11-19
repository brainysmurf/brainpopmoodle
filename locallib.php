<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Private brainpop module utility functions
 *
 * @package    mod
 * @subpackage brainpop
 * @copyright  2013 Anthony Kuske (anthonykuske.com). Based on URL module copyright 2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/brainpop/lib.php");


/**
*	Ensure the given URL links to one of:
*		brainpop.com
*		brainpopjr.com
*		brainpopesl.com
*		brainpop.fr
*		brainpop.co.uk
* @param $url
* @return bool true is seems valid, false if definitely not valid URL
*/
function brainpop_appears_valid_url($url)
{
	return (bool)preg_match('%^http(s)?\:\/\/(www\.|esp\.|esl\.)?brainpop(esl|jr)?\.(com|fr|co\.uk)%' , $url );
}

function brainpop_get_domain($url)
{ 
	$url = parse_url($url);
	return $url['host'];
}

/**
 * Fix common URL problems that we want teachers to see fixed
 * the next time they edit the resource.
 *
 * This function does not include any XSS protection.
 *
 * @param string $url
 * @return string
 */
function brainpop_fix_submitted_url($url)
{
    // note: empty urls are prevented in form validation
    $url = trim($url);

    // remove encoded entities - we want the raw URI here
    $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');

    if ( !preg_match('|^[a-z]+:|i', $url) and !preg_match('|^/|', $url) )
    {
        // invalid URI, try to fix it by making it normal URL,
        // please note relative urls are not allowed, /xx/yy links are ok
        $url = 'http://'.$url;
    }
    
    return $url;
}

/*
	Returns the full URL to the item along with the login parameters to the user is logged in and taken to the video
*/
function brainpop_get_login_url( $item )
{
	$config = get_config('brainpop');
	
	$u = $config->brainpopusername;
	$p = $config->brainpoppassword;
	
	$domain = brainpop_get_domain($item->externalurl);
	
	$url = 'http://'.$domain.'/user/loginDo.weml?user='.$u.'&password='.$p.'&targetPage=';
		$url .= urlencode($item->externalurl);

	return $url;
}


/**
 * Return full url with all extra parameters
 *
 * This function does not include any XSS protection.
 *
 * @param string $item
 * @param object $cm
 * @param object $course
 * @param object $config
 * @return string url with & encoded as &amp;
 */
function brainpop_get_full_url($item, $cm, $course, $config=null) {

    $parameters = empty($item->parameters) ? array() : unserialize($item->parameters);

    // make sure there are no encoded entities, it is ok to do this twice
    $fullurl = html_entity_decode($item->externalurl, ENT_QUOTES, 'UTF-8');

    if ( preg_match('/^(\/|https?:|ftp:)/i', $fullurl) or preg_match('|^/|', $fullurl) )
    {
        // encode extra chars in URLs - this does not make it always valid, but it helps with some UTF-8 problems
        $allowed = "a-zA-Z0-9".preg_quote(';/?:@=&$_.+!*(),-#%', '/');
        $fullurl = preg_replace_callback("/[^$allowed]/", 'brainpop_filter_callback', $fullurl);
    }
    else
    {
        // encode special chars only
        $fullurl = str_replace('"', '%22', $fullurl);
        $fullurl = str_replace('\'', '%27', $fullurl);
        $fullurl = str_replace(' ', '%20', $fullurl);
        $fullurl = str_replace('<', '%3C', $fullurl);
        $fullurl = str_replace('>', '%3E', $fullurl);
    }

    // add variable url parameters
    if (!empty($parameters))
    {
        if (!$config) {
            $config = get_config('brainpop');
        }
        $paramvalues = brainpop_get_variable_values($item, $cm, $course, $config);

        foreach ($parameters as $parse=>$parameter)
        {
            if (isset($paramvalues[$parameter])) {
                $parameters[$parse] = rawurlencode($parse).'='.rawurlencode($paramvalues[$parameter]);
            } else {
                unset($parameters[$parse]);
            }
        }

        if (!empty($parameters))
        {    
	        $join = (strpos($fullurl, '?') === false) ? '?' : '&';
	        $fullurl = $fullurl.$join.implode('&', $parameters);
        }
        
    }

    // encode all & to &amp; entity
    $fullurl = str_replace('&', '&amp;', $fullurl);

    return $fullurl;
}

/**
 * Unicode encoding helper callback
 * @internal
 * @param array $matches
 * @return string
 */
function brainpop_filter_callback($matches) {
    return rawurlencode($matches[0]);
}

/**
 * Print item header.
 * @param object $item
 * @param object $cm
 * @param object $course
 * @return void
 */
function brainpop_print_header($item, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname.': '.$item->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($item);
    echo $OUTPUT->header();
}

/**
 * Print item heading.
 * @param object $item
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function brainpop_print_heading($item, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($item->displayoptions) ? array() : unserialize($item->displayoptions);

    if ($ignoresettings or !empty($options['printheading'])) {
        echo $OUTPUT->heading(format_string($item->name), 2, 'main', 'urlheading');
    }
}

/**
 * Print item introduction.
 * @param object $item
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function brainpop_print_intro($item, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($item->displayoptions) ? array() : unserialize($item->displayoptions);
    if ($ignoresettings or !empty($options['printintro'])) {
        if (trim(strip_tags($item->intro))) {
            echo $OUTPUT->box_start('mod_introbox', 'urlintro');
            echo format_module_intro('brainpop', $item, $cm->id);
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Display brainpop video in an iframe in a Moodle page
 * @param object $item
 * @param object $cm
 * @param object $course
 * @return does not return
 */
function brainpop_display_frame($item, $cm, $course)
{
    global $PAGE, $OUTPUT, $CFG;
    
	brainpop_print_header($item, $cm, $course);
	brainpop_print_heading($item, $cm, $course, true);
	brainpop_print_intro($item, $cm, $course, true);

	$options = empty($item->displayoptions) ? array() : unserialize($item->displayoptions);
	$width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
	$height = empty($options['popupheight']) ? 450 : $options['popupheight'];

	//Get vid URL
	$login_url = brainpop_get_login_url($item);

	echo '<div class="brainPopFrameContainer">';
		echo '<iframe class="brainPopFrame" src="'.$login_url.'"></iframe>';
	echo '</div>';

    echo $OUTPUT->footer();
    die;    
    
} 
 
/*function brainpop_display_frame($item, $cm, $course)
{
    global $PAGE, $OUTPUT, $CFG;

    $frame = optional_param('frameset', 'main', PARAM_ALPHA);

    if ($frame === 'top')
    {
        $PAGE->set_pagelayout('frametop');
        bainpop_print_header($item, $cm, $course);
        brainpop_print_heading($item, $cm, $course);
        brainpop_print_intro($item, $cm, $course);
        echo $OUTPUT->footer();
        die;

    }
    else
    {
        $config = get_config('brainpop');
        $context = context_module::instance($cm->id);
        $exteurl = brainpop_get_full_url($item, $cm, $course, $config);
        $navurl = "$CFG->wwwroot/mod/brainpop/view.php?id=$cm->id&amp;frameset=top";
        $coursecontext = context_course::instance($course->id);
        $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
        $title = strip_tags($courseshortname.': '.format_string($item->name));
        $framesize = $config->framesize;
        $modulename = s(get_string('modulename','brainpop'));
        $contentframetitle = format_string($item->name);
        $dir = get_string('thisdirection', 'langconfig');

        $extframe = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html dir="$dir">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>$title</title>
  </head>
  <frameset rows="$framesize,*">
    <frame src="$navurl" title="$modulename"/>
    <frame src="$exteurl" title="$contentframetitle"/>
  </frameset>
</html>
EOF;

        @header('Content-Type: text/html; charset=utf-8');
        echo $extframe;
        die;
    }
} */

/**
 * Print brainpop info and link.
 * @param object $item brainpop video
 * @param object $cm
 * @param object $course
 * @return does not return
 */
function brainpop_print_workaround($item, $cm, $course) {
    global $OUTPUT;

    brainpop_print_header($item, $cm, $course);
    brainpop_print_heading($item, $cm, $course, true);
    brainpop_print_intro($item, $cm, $course, true);

    $fullurl = brainpop_get_full_url($item, $cm, $course);

    $display = brainpop_get_final_display_type($item);
    if ($display == RESOURCELIB_DISPLAY_POPUP) {
        $jsfullurl = addslashes_js($fullurl);
        $options = empty($item->displayoptions) ? array() : unserialize($item->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $extra = "onclick=\"window.open('$jsfullurl', '', '$wh'); return false;\"";

    } else if ($display == RESOURCELIB_DISPLAY_NEW) {
        $extra = "onclick=\"this.target='_blank';\"";

    } else {
        $extra = '';
    }

    echo '<div class="urlworkaround">';
    print_string('clicktoopen', 'brainpop', "<a href=\"$fullurl\" $extra>$fullurl</a>");
    echo '</div>';

    echo $OUTPUT->footer();
    die;
}

/**
 * Display embedded brainpop video.
 * @param object $url
 * @param object $cm
 * @param object $course
 * @return does not return
 */
function brainpop_display_embed($item, $cm, $course)
{
    global $CFG, $PAGE, $OUTPUT;

//Change this to scrape the brainpop page and get the embed code

    echo $OUTPUT->footer();
    die;
}

/**
 * Decide the best display format.
 * @param object $item
 * @return int display type constant
 */
function brainpop_get_final_display_type($item)
{
	return $item->display;
	#return RESOURCELIB_DISPLAY_FRAME;
	#return RESOURCELIB_DISPLAY_OPEN;
}

/**
 * Get the parameters that may be appended to URL
 * @param object $config brainpop module config options
 * @return array array describing opt groups
 */
function brainpop_get_variable_options($config) {
    global $CFG;

    $options = array();
    $options[''] = array('' => get_string('chooseavariable', 'brainpop'));

    $options[get_string('course')] = array(
        'courseid'        => 'id',
        'coursefullname'  => get_string('fullnamecourse'),
        'courseshortname' => get_string('shortnamecourse'),
        'courseidnumber'  => get_string('idnumbercourse'),
        'coursesummary'   => get_string('summary'),
        'courseformat'    => get_string('format'),
    );

    $options[get_string('modulename', 'brainpop')] = array(
        'urlinstance'     => 'id',
        'urlcmid'         => 'cmid',
        'urlname'         => get_string('name'),
        'urlidnumber'     => get_string('idnumbermod'),
    );

    $options[get_string('miscellaneous')] = array(
        'sitename'        => get_string('fullsitename'),
        'serverurl'       => get_string('serverurl', 'brainpop'),
        'currenttime'     => get_string('time'),
        'lang'            => get_string('language'),
    );


    $options[get_string('user')] = array(
        'userid'          => 'id',
        'userusername'    => get_string('username'),
        'useridnumber'    => get_string('idnumber'),
        'userfirstname'   => get_string('firstname'),
        'userlastname'    => get_string('lastname'),
        'userfullname'    => get_string('fullnameuser'),
        'useremail'       => get_string('email'),
        'usericq'         => get_string('icqnumber'),
        'userphone1'      => get_string('phone').' 1',
        'userphone2'      => get_string('phone2').' 2',
        'userinstitution' => get_string('institution'),
        'userdepartment'  => get_string('department'),
        'useraddress'     => get_string('address'),
        'usercity'        => get_string('city'),
        'usertimezone'    => get_string('timezone'),
        'userurl'         => get_string('webpage'),
    );

    if ($config->rolesinparams) {
        $roles = role_fix_names(get_all_roles());
        $roleoptions = array();
        foreach ($roles as $role) {
            $roleoptions['course'.$role->shortname] = get_string('yourwordforx', '', $role->localname);
        }
        $options[get_string('roles')] = $roleoptions;
    }

    return $options;
}

/**
 * Get the parameter values that may be appended to URL
 * @param object $item module instance
 * @param object $cm
 * @param object $course
 * @param object $config module config options
 * @return array of parameter values
 */
function brainpop_get_variable_values($item, $cm, $course, $config) {
    global $USER, $CFG;

    $site = get_site();

    $coursecontext = context_course::instance($course->id);

    $values = array (
        'courseid'        => $course->id,
        'coursefullname'  => format_string($course->fullname),
        'courseshortname' => format_string($course->shortname, true, array('context' => $coursecontext)),
        'courseidnumber'  => $course->idnumber,
        'coursesummary'   => $course->summary,
        'courseformat'    => $course->format,
        'lang'            => current_language(),
        'sitename'        => format_string($site->fullname),
        'serverurl'       => $CFG->wwwroot,
        'currenttime'     => time(),
        'urlinstance'     => $item->id,
        'urlcmid'         => $cm->id,
        'urlname'         => format_string($item->name),
        'urlidnumber'     => $cm->idnumber,
    );

    if (isloggedin()) {
        $values['userid']          = $USER->id;
        $values['userusername']    = $USER->username;
        $values['useridnumber']    = $USER->idnumber;
        $values['userfirstname']   = $USER->firstname;
        $values['userlastname']    = $USER->lastname;
        $values['userfullname']    = fullname($USER);
        $values['useremail']       = $USER->email;
        $values['usericq']         = $USER->icq;
        $values['userphone1']      = $USER->phone1;
        $values['userphone2']      = $USER->phone2;
        $values['userinstitution'] = $USER->institution;
        $values['userdepartment']  = $USER->department;
        $values['useraddress']     = $USER->address;
        $values['usercity']        = $USER->city;
        $values['usertimezone']    = get_user_timezone_offset();
        $values['userurl']         = $USER->url;
    }

	/*
    //hmm, this is pretty fragile and slow, why do we need it here??
    if ($config->rolesinparams) {
        $coursecontext = context_course::instance($course->id);
        $roles = role_fix_names(get_all_roles($coursecontext), $coursecontext, ROLENAME_ALIAS);
        foreach ($roles as $role) {
            $values['course'.$role->shortname] = $role->localname;
        }
    } */

    return $values;
}


/**
 * Optimised mimetype detection from general URL
 * @param $fullurl
 * @param int $size of the icon.
 * @return string|null mimetype or null when the filetype is not relevant.
 */
function brainpop_guess_icon($fullurl, $size = null) {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");

    if (substr_count($fullurl, '/') < 3 or substr($fullurl, -1) === '/') {
        // Most probably default directory - index.php, index.html, etc. Return null because
        // we want to use the default module icon instead of the HTML file icon.
        return null;
    }

    $icon = file_extension_icon($fullurl, $size);
    $htmlicon = file_extension_icon('.htm', $size);
    $unknownicon = file_extension_icon('', $size);

    // We do not want to return those icon types, the module icon is more appropriate.
    if ($icon === $unknownicon || $icon === $htmlicon) {
        return null;
    }

    return $icon;
}
