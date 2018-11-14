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
 * @package   customfield_date
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_date;

defined('MOODLE_INTERNAL') || die;

use core_customfield\api;
use core_customfield\plugin_base;

/**
 * Class data
 *
 * @package customfield_date
 */
class plugin extends plugin_base {

    const DATATYPE = 'intvalue';

    /**
     * Add fields for editing a date field.
     *
     * @param field $field
     * @param \MoodleQuickForm $mform
     * @throws \coding_exception
     */
    public static function add_field_to_config_form(\core_customfield\field_controller $field, \MoodleQuickForm $mform) {
        // Get the current calendar in use - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        // Create variables to store start and end.
        list($year, $month, $day) = explode('_', date('Y_m_d'));
        $currentdate = $calendartype->convert_from_gregorian($year, $month, $day);
        $currentyear = $currentdate['year'];

        $arryears = $calendartype->get_years();

        $config = $field->get('configdata');

        // Add elements.
        $mform->addElement('header', 'header_specificsettings', get_string('specificsettings', 'customfield_date'));
        $mform->setExpanded('header_specificsettings', true);

        $mform->addElement('select', 'configdata[startyear]', get_string('startyear', 'customfield_date'), $arryears);
        $mform->setType('configdata[startyear]', PARAM_INT);

        $defaultstart = isset($config['startyear']) ? $config['startyear'] : $currentyear;
        $mform->setDefault('configdata[startyear]', $defaultstart);

        $mform->addElement('select', 'configdata[endyear]', get_string('endyear', 'customfield_date'), $arryears);
        $mform->setType('configdata[endyear]', PARAM_INT);
        $defaultend = isset($config['endyear']) ? $config['endyear'] : $currentyear;
        $mform->setDefault('configdata[endyear]', $defaultend);

        $mform->addElement('checkbox', 'configdata[includetime]', get_string('includetime', 'customfield_date'));
        $mform->setDefault('configdata[includetime]', isset($config['includetime']));

        $mform->addElement('hidden', 'startday', '1');
        $mform->setType('startday', PARAM_INT);
        $mform->addElement('hidden', 'startmonth', '1');
        $mform->setType('startmonth', PARAM_INT);
        $mform->addElement('hidden', 'startyear', '1');
        $mform->setType('startyear', PARAM_INT);
        $mform->addElement('hidden', 'endday', '1');
        $mform->setType('endday', PARAM_INT);
        $mform->addElement('hidden', 'endmonth', '1');
        $mform->setType('endmonth', PARAM_INT);
        $mform->addElement('hidden', 'endyear', '1');
        $mform->setType('endyear', PARAM_INT);
    }

    // TODO: move to a trait.
    /**
     * @return string
     */
    public static function datafield() : string {
        return self::DATATYPE;
    }

    /**
     * Add fields for editing data of a textarea field on a context.
     *
     * @param \moodleform $mform
     * @throws \coding_exception
     */
    public static function edit_field_add(\core_customfield\field_controller $field, \MoodleQuickForm $mform) {
        // Get the current calendar in use - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        $config = $field->get('configdata');

        // Convert the year stored in the DB as gregorian to that used by the calendar type.
        $startdate = $calendartype->convert_from_gregorian($config['startyear'], 1, 1);
        $stopdate = $calendartype->convert_from_gregorian($config['endyear'], 1, 1);

        $attributes = ['startyear' => $startdate['year'],
                       'stopyear'  => $stopdate['year'],
                       'optional'  => $field->get_configdata_property('required') != 1];

        if (empty($config['includetime'])) {
            $element = 'date_selector';
        } else {
            $element = 'date_time_selector';
        }
        $mform->addElement($element, api::field_inputname($field), format_string($field->get('name')), $attributes);
        $mform->setType(api::field_inputname($field), PARAM_INT);
        $mform->setDefault(api::field_inputname($field), time());
    }
}
