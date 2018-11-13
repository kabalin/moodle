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
 * @package   core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

$id         = optional_param('id', 0, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$type       = optional_param('type', null, PARAM_COMPONENT);

admin_externalpage_setup('course_customfield');

if ($id) {
    $record  = new \core_customfield\field($id);
    $handler = \core_customfield\handler::get_handler_for_field($record);
    $typestr = get_string('pluginname', 'customfield_'.$record->get('type'));
    $title   = get_string('editingfield', 'core_customfield', $typestr);
} else {
    $category = new \core_customfield\category_controller($categoryid);
    $handler  = \core_customfield\handler::get_handler_for_category($category);
    $record   = $handler->new_field($category, $type);
    $typestr  = get_string('pluginname', 'customfield_'.$type);
    $title    = get_string('addingnewcustomfield', 'core_customfield', $typestr);
}

$url = new \moodle_url('/customfield/edit.php',
                       ['component'  => $handler->get_component(), 'area' => $handler->get_area(),
                        'itemid'     => $handler->get_itemid(),
                        'id'         => $record->get('id'), 'type' => $record->get('type'),
                        'categoryid' => $record->get('categoryid')]);

$PAGE->set_url($url);
if (!$handler->can_configure()) {
    print_error('nopermissionconfigure', 'core_customfield');
}

$mform = $handler->get_field_config_form($record);
// Process Form data.
if ($mform->is_cancelled()) {
    redirect($handler->get_configuration_url());
} else if ($data = $mform->get_data()) {
    $handler->save_field($record, $data);
    redirect($handler->get_configuration_url());
}

$PAGE->set_title($title);
$PAGE->navbar->add($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$mform->display();

echo $OUTPUT->footer();
