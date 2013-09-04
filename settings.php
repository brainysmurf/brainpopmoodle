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
 * brainpop module admin settings and defaults
 *
 * @package    mod
 * @subpackage brainpop
 * @copyright  2013 Anthony Kuske (anthonykuske.com). Based on URL module copyright 2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_FRAME,
                                                           RESOURCELIB_DISPLAY_OPEN,
                                                           RESOURCELIB_DISPLAY_NEW,
                                                           RESOURCELIB_DISPLAY_POPUP,
                                                          ));
    $defaultdisplayoptions = array(
		RESOURCELIB_DISPLAY_FRAME,
		RESOURCELIB_DISPLAY_NEW,
		RESOURCELIB_DISPLAY_POPUP,
		RESOURCELIB_DISPLAY_OPEN,
	);

    //--- general settings -----------------------------------------------------------------------------------
    #$settings->add(new admin_setting_configtext('brainpop/framesize',
     #   get_string('framesize', 'brainpop'), get_string('configframesize', 'brainpop'), 130, PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('brainpop/requiremodintro',
        get_string('requiremodintro', 'admin'), get_string('configrequiremodintro', 'admin'), 1));

//brainpop login
    $settings->add(new admin_setting_configtext('brainpop/brainpopusername', get_string('configusername','brainpop'),
        get_string('configusernamedesc', 'brainpop'), ''));

    $settings->add(new admin_setting_configpasswordunmask('brainpop/brainpoppassword', get_string('configpassword','brainpop'),
        get_string('configpassworddesc', 'brainpop'), ''));

#    $settings->add(new admin_setting_configcheckbox('brainpop/rolesinparams',
#        get_string('rolesinparams', 'brainpop'), get_string('configrolesinparams', 'brainpop'), false));

    $settings->add(new admin_setting_configmultiselect('brainpop/displayoptions',
        get_string('displayoptions', 'brainpop'), get_string('configdisplayoptions', 'brainpop'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('urlmodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

#    $settings->add(new admin_setting_configcheckbox('brainpop/printheading',
#        get_string('printheading', 'brainpop'), get_string('printheadingexplain', 'brainpop'), 0));
  
#    $settings->add(new admin_setting_configcheckbox('brainpop/printintro',
#        get_string('printintro', 'brainpop'), get_string('printintroexplain', 'brainpop'), 1));

    $settings->add(new admin_setting_configselect('brainpop/display',
        get_string('displayselect', 'brainpop'), get_string('displayselectexplain', 'brainpop'), RESOURCELIB_DISPLAY_FRAME, $displayoptions));

    $settings->add(new admin_setting_configtext('brainpop/popupwidth',
        get_string('popupwidth', 'brainpop'), get_string('popupwidthexplain', 'brainpop'), 700, PARAM_INT, 7));
        
    $settings->add(new admin_setting_configtext('brainpop/popupheight',
        get_string('popupheight', 'brainpop'), get_string('popupheightexplain', 'brainpop'), 600, PARAM_INT, 7));

}
