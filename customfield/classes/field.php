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

use core\persistent;

defined('MOODLE_INTERNAL') || die;

/**
 * Class field
 *
 * @package core_customfield
 */
class field extends persistent {

    /**
     * Database table.
     */
    const TABLE = 'customfield_field';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return array(
                'shortname'         => [
                        'type' => PARAM_TEXT,
                ],
                'name'              => [
                        'type' => PARAM_TEXT,
                ],
                'type'              => [
                        'type' => PARAM_PLUGIN,
                ],
                'description'       => [
                        'type'     => PARAM_RAW,
                        'optional' => true,
                        'default'  => null,
                        'null'     => NULL_ALLOWED
                ],
                'descriptionformat' => [
                        'type' => PARAM_INT,
                        'default' => FORMAT_MOODLE,
                        'optional' => true
                ],
                'sortorder'         => [
                        'type'    => PARAM_INT,
                        'default' => 0,
                ],
                'categoryid'        => [
                        'type' => PARAM_INT
                ],
                'configdata'        => [
                        'type'     => PARAM_RAW,
                        'optional' => true,
                        'default'  => null,
                        'null'     => NULL_ALLOWED
                ],
        );
    }

    /**
     * Validate the user ID.
     *
     * @param int $value The value.
     * @return bool
     */
    protected function validate_shortname($value) {
        if (strpos($value, ' ') !== false) {
            throw new \dml_write_exception(get_string('invalidshortnameerror', 'core_customfield'));
        }

        return true;
    }

    /**
     * Validate if configdata have all required fields
     *
     * @param string $value
     * @return bool
     */
    protected function validate_configdata($value) {
        $fields = $this->get('configdata');

        if (!(isset($fields['required']) && isset($fields['uniquevalues']))) {
            throw new \moodle_exception('fieldrequired', 'core_customfield');
        }

        return true;
    }

    /**
     * Delete associated data before delete field
     *
     * @return void
     */
    protected function before_delete() {
        global $DB;
        // TODO execute callback from all plugins so they can delete data associated with this field.
        $DB->execute('DELETE from {' . data::TABLE . '} WHERE fieldid = ?', [$this->get('id')]);
        // TODO delete all files that are associated with field description that is about to be deleted.
    }

    /**
     * Update sort order after create
     */
    protected function after_create() {
        api::move_field($this, $this->get('categoryid'));
    }

    /**
     * Set the category associated with this field
     *
     * @param category_controller $category
     */
    public function set_category(category_controller $category) {
        $this->category = $category;
    }

    /**
     * Get the category associated with this field
     *
     * @return category_controller
     */
    public function get_category(): category_controller {
        if (!$this->category) {
            $this->category = new category_controller($this->raw_get('categoryid'));
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
}
