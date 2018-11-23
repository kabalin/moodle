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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.+

/**
 * Database log store upgrade.
 *
 * @package    logstore_database
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_customfield_text_install() {
    global $DB;

    $dbman = $DB->get_manager();

    // Define table customfield_field to be created.
    $table = new xmldb_table('customfield_field');

    // Adding fields to table customfield_field.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
    $table->add_field('name', XMLDB_TYPE_CHAR, '400', null, XMLDB_NOTNULL, null, null);
    $table->add_field('type', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('configdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table customfield_field.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_key('categoryid', XMLDB_KEY_FOREIGN, ['categoryid'], 'category', ['id']);

    // Adding indexes to table customfield_field.
    $table->add_index('categoryid_shortname', XMLDB_INDEX_UNIQUE, ['categoryid', 'shortname']);

    // Conditionally launch create table for customfield_field.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define table customfield_category to be created.
    $table = new xmldb_table('customfield_category');

    // Adding fields to table customfield_category.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('name', XMLDB_TYPE_CHAR, '400', null, XMLDB_NOTNULL, null, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
    $table->add_field('area', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
    $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

    // Adding keys to table customfield_category.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);

    // Adding indexes to table customfield_category.
    $table->add_index('component_area_itemid', XMLDB_INDEX_NOTUNIQUE, ['component', 'area', 'itemid']);

    // Conditionally launch create table for customfield_category.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Define table customfield_data to be created.
    $table = new xmldb_table('customfield_data');

    // Adding fields to table customfield_data.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('fieldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('intvalue', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('decvalue', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
    $table->add_field('shortcharvalue', XMLDB_TYPE_CHAR, '255', null, null, null, null);
    $table->add_field('charvalue', XMLDB_TYPE_CHAR, '400', null, null, null, null);
    $table->add_field('value', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('valueformat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

    // Adding keys to table customfield_data.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    $table->add_key('fieldid', XMLDB_KEY_FOREIGN, ['fieldid'], 'customfield_field', ['id']);
    $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);

    // Adding indexes to table customfield_data.
    $table->add_index('instanceid-fieldid', XMLDB_INDEX_UNIQUE, ['instanceid', 'fieldid']);

    // Conditionally launch create table for customfield_data.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    return true;
}
