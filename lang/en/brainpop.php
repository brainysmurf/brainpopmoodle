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
 * Strings for component 'brainpop', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    mod
 * @subpackage brainpop
 * @copyright  2013 Anthony Kuske (anthonykuske.com). Based on URL module copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['clicktoopen'] = 'Click {$a} link to watch the video.';

$string['configusername'] = 'BrainPOP Username';
$string['configusernamedesc'] = 'Enter the username for your BrainPOP account.';
$string['configpassword'] = 'BrainPOP Password';
$string['configpassworddesc'] = 'Enter the password for your BrainPOP account.';


$string['configdisplayoptions'] = 'Select all options that should be available, existing settings are not modified. Hold CTRL key to select multiple fields.';
$string['configframesize'] = 'When the video is displayed in a frame, this value is the height (in pixels) of the top frame (which contains the navigation).'; //but this isn't used
$string['configrolesinparams'] = 'Enable if you want to include localized role names in list of available parameter variables.';
$string['contentheader'] = 'Content';

$string['displayoptions'] = 'Available display options';
$string['displayselect'] = 'Display';
$string['displayselect_help'] = 'This setting determines how the video will be displayed. Options may include:

* In frame - The video is displayed within a page and is surrounded by the normal page features, such as the navigation, title and description of the content.
* New window - The video is displayed in a new browser window *with* menus and an address bar.
* Open - Only the video is displayed in the current browser window, like clicking on a normal link.
* In pop-up - The video is displayed in a new browser window *without* menus or an address bar.
';
$string['displayselectexplain'] = 'Choose display type, unfortunately not all types are suitable for all URLs.';
$string['externalurl'] = 'External URL';
$string['brainpopurl'] = 'BrainPOP Video Page URL';
$string['framesize'] = 'Frame height';
$string['invalidstoredurl'] = 'Cannot display this resource, URL is invalid.';
$string['chooseavariable'] = 'Choose a variable...';
$string['invalidurl'] = 'Entered URL is invalid';
$string['modulename'] = 'BrainPOP Video';
$string['modulename_help'] = 'The BrainPOP module enables a teacher to show a video from the brainpop.com website. Find the video you want to show on brainpop.com and copy the link here to display it. The student will be logged in automatically and therefore able to view the video when they click on it.

There are a number of ways to display the video, such as showing it in a frame on the page or opening it in a pop-up window.';
$string['modulename_link'] = 'mod/brainpop/view';
$string['modulenameplural'] = 'BrainPOP Videos';
$string['neverseen'] = 'Never seen';
$string['page-mod-url-x'] = 'Any URL module page';

$string['parameterinfo'] = '&amp;parameter=variable';
$string['parametersheader'] = 'URL variables';
$string['parametersheader_help'] = 'Some internal Moodle variables may be automatically appended to the URL. Type your name for the parameter into each text box(es) and then select the required matching variable.';

$string['pluginadministration'] = 'BrainPOP module administration';

$string['pluginname'] = 'BrainPOP';
$string['popupheight'] = 'Pop-up height (in pixels)';
$string['popupheightexplain'] = 'Specifies default height of popup windows.';
$string['popupwidth'] = 'Pop-up width (in pixels)';
$string['popupwidthexplain'] = 'Specifies default width of popup windows.';

$string['printheading'] = 'Display URL name';
$string['printheadingexplain'] = 'Display URL name above content? Some display types may not display URL name even if enabled.';
$string['printintro'] = 'Display URL description';
$string['printintroexplain'] = 'Display URL description below content? Some display types may not display description even if enabled.';

$string['rolesinparams'] = 'Include role names in parameters';

$string['serverurl'] = 'Server URL';
$string['url:addinstance'] = 'Add a new BrainPOP video resource';
$string['url:view'] = 'View Video';
