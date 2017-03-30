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
 * repository_antares class
 *
 * @since 2.0
 * @package    repository
 * @subpackage antares
 * @copyright  2015 Synergy Learning for Antares
 * @author     Yair Spielmann <yair.spielmann@synergy-learning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */#

require_once dirname(dirname(__FILE__)) . '/arix/arix.php';
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->libdir.'/filelib.php');

require_login();

$identifier = required_param('id', PARAM_TEXT);
$teacherid = required_param('teacher', PARAM_INT);
$kontext = required_param('kontext', PARAM_TEXT);


$cli = new ArixClient("http://arix.datenbank-bildungsmedien.net/", $kontext, "", "");
redirect($cli->getLink($identifier));
//Load teacher and school
//if (!$teacher = $DB->get_record('user', array('id' => $teacherid))) {
//    print_error('teachr_notfound', 'respository_antares');
//}
//profile_load_data($teacher);
//if(empty($teacher->profile_field_antarespw)) {
//    print_error('error_teachr_nopassword', 'respository_antares');
//}
//$schoolid = $teacher->profile_field_antaresid;
//$schoolpw = $teacher->profile_field_antarespw;