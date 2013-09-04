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
 * Mandatory public API of brainpop module
 *
 * @package    mod
 * @subpackage brainpop
 * @copyright  2013 Anthony Kuske (anthonykuske.com). Based on URL module copyright 2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in brainpop module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function brainpop_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Returns all other caps used in module
 * @return array
 */
function brainpop_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function brainpop_reset_userdata($data) {
    return array();
}

/**
 * List of view style log actions
 * @return array
 */
function brainpop_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List of update style log actions
 * @return array
 */
function brainpop_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add brainpop instance.
 * @param object $data
 * @param object $mform
 * @return int new brainpop instance id
 */
function brainpop_add_instance($data, $mform) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/mod/brainpop/locallib.php');

    $parameters = array();
    for ($i=0; $i < 100; $i++) {
        $parameter = "parameter_$i";
        $variable  = "variable_$i";
        if (empty($data->$parameter) or empty($data->$variable)) {
            continue;
        }
        $parameters[$data->$parameter] = $data->$variable;
    }
    $data->parameters = serialize($parameters);

    $displayoptions = array();
    if ($data->display == RESOURCELIB_DISPLAY_POPUP) {
        $displayoptions['popupwidth']  = $data->popupwidth;
        $displayoptions['popupheight'] = $data->popupheight;
    }
    if (in_array($data->display, array(RESOURCELIB_DISPLAY_AUTO, RESOURCELIB_DISPLAY_EMBED, RESOURCELIB_DISPLAY_FRAME))) {
        $displayoptions['printheading'] = (int)!empty($data->printheading);
        $displayoptions['printintro']   = (int)!empty($data->printintro);
    }
    $data->displayoptions = serialize($displayoptions);

    $data->externalurl = brainpop_fix_submitted_url($data->externalurl);

    $data->timemodified = time();
    $data->id = $DB->insert_record('brainpop', $data);

    return $data->id;
}

/**
 * Update brainpop instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function brainpop_update_instance($data, $mform) {
    global $CFG, $DB;

    require_once($CFG->dirroot.'/mod/brainpop/locallib.php');

    $parameters = array();
    for ($i=0; $i < 100; $i++) {
        $parameter = "parameter_$i";
        $variable  = "variable_$i";
        if (empty($data->$parameter) or empty($data->$variable)) {
            continue;
        }
        $parameters[$data->$parameter] = $data->$variable;
    }
    $data->parameters = serialize($parameters);

    $displayoptions = array();
    if ($data->display == RESOURCELIB_DISPLAY_POPUP) {
        $displayoptions['popupwidth']  = $data->popupwidth;
        $displayoptions['popupheight'] = $data->popupheight;
    }
    if (in_array($data->display, array(RESOURCELIB_DISPLAY_AUTO, RESOURCELIB_DISPLAY_EMBED, RESOURCELIB_DISPLAY_FRAME))) {
        $displayoptions['printheading'] = (int)!empty($data->printheading);
        $displayoptions['printintro']   = (int)!empty($data->printintro);
    }
    $data->displayoptions = serialize($displayoptions);

    $data->externalurl = brainpop_fix_submitted_url($data->externalurl);

    $data->timemodified = time();
    $data->id           = $data->instance;

    $DB->update_record('brainpop', $data);

    return true;
}

/**
 * Delete brainpop instance.
 * @param int $id
 * @return bool true
 */
function brainpop_delete_instance($id)
{
    global $DB;

    if ( !$item = $DB->get_record('brainpop', array('id'=>$id)) )
    {
        return false;
    }

    // note: all context files are deleted automatically

    $DB->delete_records('brainpop', array('id'=>$item->id));

    return true;
}

/**
 * Return use outline
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $item
 * @return object|null
 */
function brainpop_user_outline($course, $user, $mod, $item)
{
    global $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'brainpop',
                                              'action'=>'view', 'info'=>$item->id), 'time ASC')) {

        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $result = new stdClass();
        $result->info = get_string('numviews', '', $numviews);
        $result->time = $lastlog->time;

        return $result;
    }
    return NULL;
}

/**
 * Return use complete
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $item
 */
function brainpop_user_complete($course, $user, $mod, $item) {
    global $CFG, $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'brainpop',
                                              'action'=>'view', 'info'=>$item->id), 'time ASC')) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string('mostrecently');
        $strnumviews = get_string('numviews', '', $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string('neverseen', 'brainpop');
    }
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param object $coursemodule
 * @return cached_cm_info info
 */
function brainpop_get_coursemodule_info($coursemodule)
{
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/brainpop/locallib.php");

    if ( !$item = $DB->get_record('brainpop', array('id'=>$coursemodule->instance), 'id, name, display, displayoptions, externalurl, parameters, intro, introformat') )
    {
		return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $item->name;

    //note: there should be a way to differentiate links from normal resources
    $info->icon = brainpop_guess_icon($item->externalurl, 24);

    $display = brainpop_get_final_display_type($item);

    if ($display == RESOURCELIB_DISPLAY_POPUP)
    {
        $fullurl = "$CFG->wwwroot/mod/brainpop/view.php?id=$coursemodule->id&amp;redirect=1";
        $options = empty($item->displayoptions) ? array() : unserialize($item->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";

    }
    else if ($display == RESOURCELIB_DISPLAY_NEW)
    {
        $fullurl = "$CFG->wwwroot/mod/brainpop/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->onclick = "window.open('$fullurl'); return false;";

    }

    if ($coursemodule->showdescription)
    {
		// Convert intro to html. Do not filter cached version, filters run at display time.
		$info->content = format_module_intro('brainpop', $item, $coursemodule->id, false);
    }

    return $info;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function brainpop_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-brainpop-*'=>get_string('page-mod-brainpop-x', 'brainpop'));
    return $module_pagetype;
}

/**
 * Export brainpop resource contents
 *
 * @return array of file content
 */
function brainpop_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/brainpop/locallib.php");
    $contents = array();
    $context = context_module::instance($cm->id);

    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $DBitem = $DB->get_record('brainpop', array('id'=>$cm->instance), '*', MUST_EXIST);

    $fullurl = str_replace('&amp;', '&', brainpop_get_full_url($DBitem, $cm, $course));
    $isurl = clean_param($fullurl, PARAM_URL);
    if (empty($isurl)) {
        return null;
    }

    $item = array();
    $item['type'] = 'brainpop';
    $item['filename']     = $DBitem->name;
    $item['filepath']     = null;
    $item['filesize']     = 0;
    $item['fileurl']      = $fullurl;
    $item['timecreated']  = null;
    $item['timemodified'] = $DBitem->timemodified;
    $item['sortorder']    = null;
    $item['userid']       = null;
    $item['author']       = null;
    $item['license']      = null;
    $contents[] = $item;

    return $contents;
}
