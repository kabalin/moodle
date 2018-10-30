<?php
//// This file is part of Moodle - http://moodle.org/
////
//// Moodle is free software: you can redistribute it and/or modify
//// it under the terms of the GNU General Public License as published by
//// the Free Software Foundation, either version 3 of the License, or
//// (at your option) any later version.
////
//// Moodle is distributed in the hope that it will be useful,
//// but WITHOUT ANY WARRANTY; without even the implied warranty of
//// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//// GNU General Public License for more details.
////
//// You should have received a copy of the GNU General Public License
//// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
///**
// * @package   core_customfield
// * @copyright 2018 Toni Barbera <toni@moodle.com>
// * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
// */
//
//namespace core_customfield;
//
//defined('MOODLE_INTERNAL') || die;
//
///**
// * Class field
// *
// * @package core_customfield
// */
//abstract class field_controller {
//    /**
//     * Field persistent
//     */
//    protected $field;
//
//    /**
//     * category constructor.
//     *
//     * @param int $id
//     * @param \stdClass|null $record
//     */
//    public function __construct(int $id = 0, \stdClass $record = null) {
//
//        $this->field = new data($id, $record);
//    }
//
//    /**
//     * Persistent getter parser.
//     *
//     * @param $property
//     * @return mixed
//     * @throws \coding_exception
//     */
//    final public function get($property) {
//        return $this->field->get($property);
//    }
//
//    /**
//     * Persistent setter parser.
//     *
//     * @param $property
//     * @param $value
//     * @return field
//     * @throws \coding_exception
//     */
//    final public function set($property, $value) {
//        return $this->field->set($property, $value);
//    }
//
//    /**
//     * Persistent delete parser.
//     *
//     * @return bool
//     * @throws \coding_exception
//     * @throws \dml_exception
//     */
//    final public function delete() {
//        $this->delete_data();
//        $response = $this->field->delete();
//        $this->clear_cache();
//        return $response;
//    }
//
//    /**
//     * Persistent save parser.
//     *
//     * @return void
//     */
//    final public function save() {
//        $this->clear_cache();
//        $this->field->save();
//    }
//
//    /**
//     * Persistent record_exist parser.
//     *
//     * @param int $id
//     * @return bool
//     */
//    public static function record_exists(int $id) {
//        return field::record_exists($id);
//    }
//
//    /**
//     * Bulk data delete
//     *
//     * @throws \coding_exception
//     * @throws \dml_exception
//     */
//    public function delete_data() {
//        global $DB;
//        $DB->delete_records('customfield_data', ['fieldid' => $this->get('id')]);
//    }
//}
