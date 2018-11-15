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
 * @copyright 2018 Daniel Neis Araujo <daniel@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customfield_checkbox;

use core\persistent;

defined('MOODLE_INTERNAL') || die;

/**
 * Class data
 *
 * @package customfield_checkbox
 */
class data_controller extends \core_customfield\data_controller {

    /**
     * Add fields for editing a textarea field.
     *
     * @param \MoodleQuickForm $mform
     */
    public function edit_field_add(\MoodleQuickForm $mform) {

    }

}
