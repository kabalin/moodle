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
 * @package   customfield_select
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_select;

defined('MOODLE_INTERNAL') || die;

use core_customfield\api;
use core_customfield\plugin_base;

/**
 * Class data
 *
 * @package customfield_select
 */
class plugin extends plugin_base {

    const DATATYPE = 'intvalue';

    /**
     * Add fields for editing a select field.
     *
     * @param field_controller $field
     * @param \MoodleQuickForm $mform
     */
    public static function add_field_to_config_form(\core_customfield\field_controller $field, \MoodleQuickForm $mform) {
        $mform->addElement('header', 'header_specificsettings', get_string('specificsettings', 'customfield_select'));
        $mform->setExpanded('header_specificsettings', true);

        $mform->addElement('textarea', 'configdata[options]', 'Menu options (one per line)');

        $mform->addElement('text', 'configdata[defaultvalue]', get_string('defaultvalue', 'core_customfield'), 'size="50"');
        $mform->setType('configdata[defaultvalue]', PARAM_TEXT);
    }

    // TODO: move to a trait.
    /**
     * Return which column from mdl_customfield_data is used to store and retrieve data
     *
     * @return string
     */
    public static function datafield() : string {
        return self::DATATYPE;
    }

    /**
     * Add fields for editing a textarea field.
     *
     * @param \MoodleQuickForm $mform
     */
    public static function edit_field_add(\core_customfield\field_controller $field, \MoodleQuickForm $mform) {
        $config = $field->get('configdata');
        $options = self::get_options_array($field);
        $formattedoptions = array();
        foreach ($options as $key => $option) {
            // Multilang formatting with filters.
            $formattedoptions[$key] = format_string($option);
        }

        $mform->addElement('select', api::field_inputname($field), format_string($field->get('name')), $formattedoptions);

        if (is_null(api::datafield($field))) {
            $defaultkey = array_search($config['defaultvalue'], $options);
        } else {
            $defaultkey = api::datafield($field);
        }
        $mform->setDefault(api::field_inputname($field), $defaultkey);
    }

    /**
     * Returns the options available as an array.
     *
     * @param \core_customfield\field_controller $field
     * @return array
     */
    public static function get_options_array(\core_customfield\field_controller $field): array {
        if ($field->get_configdata_property('options')) {
            $options = explode("\r\n", $field->get_configdata_property('options'));
        } else {
            $options = array();
        }
        return $options;
    }
}