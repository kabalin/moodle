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
// * @copyright 2018, Toni Barbera <toni@moodle.com>
// * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
// */
//
//namespace core_customfield;
//
//defined('MOODLE_INTERNAL') || die;
//
///**
// * Class category
// *
// * @package core_customfield
// */
//abstract class category_controller {
//
//    /**
//     * Category persistent
//     */
//    protected $category;
//
//    /**
//     * category constructor.
//     *
//     * @param int $id
//     * @param \stdClass|null $record
//     */
//    public function __construct(int $id = 0, \stdClass $record = null) {
//
//        $this->category = new category($id, $record);
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
//        return $this->category->get($property);
//    }
//
//    /**
//     * Persistent setter parser.
//     *
//     * @param $property
//     * @param $value
//     * @return category_model
//     * @throws \coding_exception
//     */
//    final public function set($property, $value) {
//        return $this->category->set($property, $value);
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
//        $this->delete_fields();
//        $response = $this->category->delete();
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
//        $this->category->save();
//    }
//
//    /**
//     * @param int $id
//     * @return bool
//     */
//    public static function record_exists(int $id) {
//        return category_model::record_exists($id);
//    }
//
//    /**
//     * Bulk fields delete
//     *
//     * @throws \coding_exception
//     * @throws \dml_exception
//     */
//    public function delete_fields() {
//        global $DB;
//        $DB->delete_records('customfield_field', ['categoryid' => $this->get('id')]);
//    }
//}
