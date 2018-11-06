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
 * @copyright 2018 Daniel Neis Araujo <daniel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_text;

defined('MOODLE_INTERNAL') || die;

use core_customfield\api;

/**
 * Class data
 *
 * @package customfield_text
 */
class data_controller extends \core_customfield\data_controller {

    /**
     * Add fields for editing a text profile field.
     *
     * @param \MoodleQuickForm $mform
     * @throws \coding_exception
     */
    public function edit_field_add(\MoodleQuickForm $mform) {

    }

    /**
     * Validates data for this field.
     *
     * @param \stdClass $data
     * @param array $files
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function validate_data(\stdClass $data, array $files): array {

        $errors = parent::validate_data($data, $files);
        $maxlength = $this->get_field()->get_configdata_property('maxlength');
        if (($maxlength > 0) && ($maxlength < \core_text::strlen($data->{api::field_inputname($this->get_field())}))) {
            $errors[api::field_inputname($this->get_field())] = get_string('errormaxlength', 'customfield_text', $maxlength);
        }
        return $errors;
    }
}
