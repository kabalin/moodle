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
 * @copyright 2018 Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield;

defined('MOODLE_INTERNAL') || die;

/**
 * Class field
 *
 * @package core_customfield
 */
abstract class field_controller {
    /**
     * Field persistent
     */
    protected $field;

    /**
     * @var category
     */
    protected $category;

    /**
     * category constructor.
     *
     * @param int $id
     * @param \stdClass|null $record
     */
    public function __construct(int $id = 0, \stdClass $record = null) {
        $this->field = new field($id, $record);

        if ($record) {
            $customfieldtype = "\\customfield_{$record->type}\\field_controller";
            if (!class_exists($customfieldtype) || !is_subclass_of($customfieldtype, field_controller::class)) {
                throw new \coding_exception(get_string('errorfieldtypenotfound', 'core_customfield', s($record->type)));
            }
        }
    }

    /**
     * Validate the data from the config form.
     * Sub classes must reimplement it.
     *
     * @param array $data from the add/edit profile field form
     * @param array $files
     * @return array associative array of error messages
     */
    public function validate_config_form(array $data, $files = array()): array {
        return array();
    }


    /**
     * Persistent getter parser.
     *
     * @param $property
     * @return mixed
     * @throws \coding_exception
     */
    final public function get($property) {
        return $this->field->get($property);
    }

    /**
     * Persistent setter parser.
     *
     * @param $property
     * @param $value
     * @return field
     * @throws \coding_exception
     */
    final public function set($property, $value) {
        return $this->field->set($property, $value);
    }

    /**
     * Persistent delete parser.
     *
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    final public function delete() {
        $this->delete_data();
        $response = $this->field->delete();
        $this->clear_cache();
        return $response;
    }

    /**
     * Persistent save parser.
     *
     * @return void
     */
    final public function save() {
        $this->clear_cache();
        $this->field->save();
    }

    /**
     * Persistent record_exist parser.
     *
     * @param int $id
     * @return bool
     */
    public static function record_exists(int $id) {
        return field::record_exists($id);
    }

    /**
     * Bulk data delete
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function delete_data() {
        global $DB;
        $DB->delete_records('customfield_data', ['fieldid' => $this->get('id')]);
    }

    // TODO: Review from here.


    /**
     * Set the category associated with this field
     *
     * @param category $category
     */
    public function set_category(category $category) {
        $this->category = $category;
    }

    /**
     * Get the category associated with this field
     *
     * @return category
     * @throws \moodle_exception
     */
    public function get_category(): category {
        if (!$this->category) {
            $this->category = new category($this->raw_get('categoryid'));
        }
        return $this->category;
    }

    /**
     * Custom getter for configdata, decoded
     *
     * @return array
     */
    protected function get_configdata(): array {
        return json_decode($this->raw_get('configdata'), true) ?? array();
    }

    /**
     * @param string $property
     * @return mixed
     * @throws \moodle_exception
     */
    public function get_configdata_property(string $property) {
        $configdata = $this->get('configdata');
        if ( !isset($configdata[$property]) ) {
            return null;
        }
        return $configdata[$property];
    }

    /**
     * @param int $categoryid
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_fields_from_category_array(int $categoryid): array {
        global $DB;

        $fields  = array();

        $plugins = \core\plugininfo\customfield::get_enabled_plugins();
        // No plugins enabled.
        if (empty($plugins)) {
            return $plugins;
        }

        list($sqlfields, $params) = $DB->get_in_or_equal(array_keys($plugins), SQL_PARAMS_NAMED);
        $sql = "SELECT *
                  FROM {customfield_field}
                 WHERE categoryid = :categoryid
                   AND type {$sqlfields}
              ORDER BY sortorder";
        $records = $DB->get_records_sql($sql, $params + ['categoryid' => $categoryid]);
        foreach ($records as $fielddata) {
            $fields[] = new field($fielddata->id, $fielddata);
        }

        return $fields;
    }
}
