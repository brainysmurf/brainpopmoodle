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
 * Brainpop module main user interface
 *
 * @package    mod
 * @subpackage brainpop
 * @copyright  2013 Anthony Kuske (anthonykuske.com). Based on URL module copyright 2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/mod/brainpop/locallib.php");
require_once($CFG->libdir . '/completionlib.php');

$id       = optional_param('id', 0, PARAM_INT);        // Course module ID
$u        = optional_param('u', 0, PARAM_INT);         // brainpop instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($u) {  // Two ways to specify the module
    $item = $DB->get_record('brainpop', array('id'=>$u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('brainpop', $item->id, $item->course, false, MUST_EXIST);

} else
{
    $cm = get_coursemodule_from_id('brainpop', $id, 0, false, MUST_EXIST);
    $item = $DB->get_record('brainpop', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/brainpop:view', $context);

add_to_log($course->id, 'brainpop', 'view', 'view.php?id='.$cm->id, $item->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/brainpop/view.php', array('id' => $cm->id));

	// Make sure URL exists before generating output - some older sites may contain empty urls
	// Do not use PARAM_URL here, it is too strict and does not support general URIs!
	$exturl = trim($item->externalurl);
	if (empty($exturl) or $exturl === 'http://')
	{
	    brainpop_print_header($item, $cm, $course);
	    brainpop_print_heading($item, $cm, $course);
	    brainpop_print_intro($item, $cm, $course);
	    notice(get_string('invalidstoredurl', 'brainpop'), new moodle_url('/course/view.php', array('id'=>$cm->course)));
	    die;
	}
	unset($exturl);
	
	$login_url = brainpop_get_login_url( $item );
	$displaytype = brainpop_get_final_display_type( $item );


	if ( $displaytype == RESOURCELIB_DISPLAY_OPEN )
	{
	    // For 'open' links, we always redirect to the content - except if the user
	    // just chose 'save and display' from the form then that would be confusing
	    
	    //If there's no referer or the referer is NOT modedit.php then redirect
	    if ( !isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'modedit.php') === false )
	    {
	        $redirect = true;
	    }
	}

	if ( $redirect )
	{
	    // coming from course page or module index page,
	    // the redirection is needed for completion tracking and logging
	    redirect( $login_url );
	}

	//If we haven't been redirected by now then we display it inside Moodle
	switch ($displaytype)
	{
	    case RESOURCELIB_DISPLAY_EMBED:
	        brainpop_display_embed($item, $cm, $course);
	        break;
	    case RESOURCELIB_DISPLAY_FRAME:
	        brainpop_display_frame($item, $cm, $course);
	        break;
	    default:
	        brainpop_print_workaround($item, $cm, $course);
	        break;
	}
