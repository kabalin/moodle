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
 * @copyright 2018, Toni Barbera <toni@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield;

defined('MOODLE_INTERNAL') || die;

/**
 * Class data
 *
 * @package core_customfield
 */
abstract class data_controller {
    /**
     * Data persistent
     *
     * @var data
     */
    protected $data;

    /**
     * Field that this data belongs to.
     *
     * @var field
     */
    protected $field;

    /**
     * data_controller constructor.
     *
     * @param int $id
     * @param \stdClass|null $record
     */
    public function __construct(int $id = 0, \stdClass $record) {
        $this->data = new data($id, $record);
        return $this;
    }
    /**
     * Persistent getter parser.
     *
     * @param $property
     * @return mixed
     */
    final public function get($property) {
        $method = "get_{$property}";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return $this->data->get($property);
    }

    /**
     * Persistent setter parser.
     *
     * @param $property
     * @param $value
     * @return data
     */
    final public function set($property, $value) {
        $method = "get_{$property}";
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        return $this->data->set($property, $value);
    }

    /**
     * Persistent delete parser.
     *
     * @return bool
     */
    final public function delete() {
        return $this->data->delete();
    }

    /**
     * Persistent save parser.
     *
     * @return void
     */
    final public function save() {
        if (empty($this->data->get('id'))) {
            $this->data->set('id', 0);
        }
        $this->data->save();
    }

    /**
     * Persistent record_exist parser.
     *
     * @param int $id
     * @return bool
     */
    public static function record_exists(int $id) {
        return data::record_exists($id);
    }

    /**
     * @param int $fieldid
     * @return data_controller
     */
    protected static function fieldload(int $fieldid): self {
        global $DB;

        $dbdata = $DB->get_record(data::TABLE, ['fieldid' => $fieldid]);

        if ($dbdata) {
            return new static($dbdata->id);
        } else {
            return new static();
        }
    }

    /**
     * Set the field associated with this data
     *
     * @param field $field
     */
    public function set_field(field_controller $field) {
        $this->field = $field;
    }

    /**
     * Field associated with this data
     *
     * @return field_controller
     */
    public function get_field(): field_controller {
        return $this->field;
    }

    /**
     * Save the value to be used/submitted on form
     *
     * @param $value
     */
    public function set_formvalue($value) {
        $this->set(api::datafield($this->get_field()), $value->{api::datafield($this->get_field())});
    }

    /**
     * Save the value from backup
     *
     * @param $value
     */
    public function set_rawvalue($value) {
        $this->set(api::datafield($this->get_field()), $value);
    }

    /**
     * Return the default value if the field has not been set.
     *
     * @return mixed
     */
    protected function get_charvalue() {
        if ($this->data->get('id') == 0) {
            return $this->get_field()->get_configdata_property('defaultvalue');
        }
        return $this->data->get('charvalue');
    }

    /**
     * Return the default value if the field has not been set.
     * Work with checkbox field and select field.
     *
     * @return mixed
     */
    protected function get_intvalue() {
        $type = $this->field->get('type');
        if ($this->get('id') == 0 && $type == 'checkbox') {
            return $this->get_field()->get_configdata_property('checkbydefault');
        }
        if ($this->get('id') == 0 && $type == 'select') {
            $configoptions = $this->get_field()->get_configdata_property('options');
            $options = explode("\r\n", $configoptions);
            $defaultvalue = $this->get_field()->get_configdata_property('defaultvalue');
            return array_search($defaultvalue, $options);
        }
        return $this->data->get('intvalue');
    }

    /**
     * Return the default value if the field has not been set.
     *
     * @return mixed
     */
    protected function get_value() {
        $type = $this->field->get('type');
        if ($this->get('id') == 0 && $type == 'textarea') {
            $defaultvalue = $this->get_field()->get_configdata_property('defaultvalue');
            return $defaultvalue;
        }
        return $this->data->get('value');
    }

    /**
     * Saves the data coming from form
     *
     * @param \stdClass $datanew data coming from the form
     * @return mixed returns data id if success of db insert/update, false on fail, 0 if not permitted
     */
    public function edit_save_data(\stdClass $datanew) {
        $value = $datanew->{api::field_inputname($this->get_field())};
        $this->data->set(api::datafield($this->get_field()), $value);
        $this->data->set('value', $value);
        $this->save();
        return $this;
    }

    /**
     * Adds data in a given object on api::field_inputname() attribute.
     *
     * @param \stdClass $data
     */
    public function add_customfield_data_to_object(\stdClass $data) {
        $data->{api::field_inputname($this->get_field())} = $this->get(api::datafield($this->get_field()));
    }

    /**
     * Validates data for this field.
     *
     * @param \stdClass $data
     * @param array $files
     * @return array
     */
    public function validate_data(\stdClass $data, array $files): array {
        global $DB;

        $errors = [];
        if ($this->get_field()->get_configdata_property('uniquevalues') == 1) {

            $datafield = api::datafield($this->get_field());
            $where = "fieldid = ? AND {$datafield} = ?";
            $params = [$this->get_field()->get('id'), $data->{api::field_inputname($this->get_field())}];
            if (isset($data->id) && $data->id > 1) {
                $where .= ' AND instanceid != ?';
                $params[] = $data->id;
            }
            if ($DB->record_exists_select('customfield_data', $where, $params)) {
                $errors[api::field_inputname($this->get_field())] = get_string('erroruniquevalues', 'core_customfield');
            }
        }
        return $errors;
    }

    /**
     * Tweaks the edit form.
     *
     * @param \MoodleQuickForm $mform
     * @return bool
     */
    public function edit_after_data(\MoodleQuickForm $mform): bool {
        return true;
    }

    /**
     * Returns field as a renderable object. Used by handlers to display data on various places.
     * @return string
     */
    public function display() {
        $type = $this->get_field()->get('type');
        $classpath = "\\customfield_{$type}\\output\\display";
        return new $classpath($this);
    }

    /**
     * Return the context of the field
     *
     * @return \context
     */
    public function get_context() : \context {
        return \context::instance_by_id($this->get('contextid'));
    }

}
