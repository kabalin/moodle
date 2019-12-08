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
 * Callbacks.
 *
 * @package    filter_glossary
 * @copyright  2019 Ruslan Kabalin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Fragment to obtain glossary concept.
 *
 * @param array $args
 * @return null|string
 */
function filter_glossary_output_fragment_get_concept(array $args) {
    global $PAGE, $DB;

    if (empty($args['eid'])) {
        throw new \invalid_parameter_exception('Invalid glossary entry id.');
    }

    // Get and validate the glossary concept.
    $entry = $DB->get_record('glossary_entries', array('id'=>$args['eid']), '*', MUST_EXIST);
    $glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid), '*', MUST_EXIST);
    list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');

    $context = context_module::instance($cm->id);
    if (!has_capability('mod/glossary:view', $context)) {
        throw new invalid_parameter_exception('invalidentry');
    }
    $PAGE->set_context($context);

    if (!glossary_can_view_entry($entry, $cm)) {
        throw new invalid_parameter_exception('invalidentry');
    }

    // Prepare concept definition.
    ob_start();
    glossary_print_entry_definition($entry, $glossary, $cm);
    $definition = html_writer::tag('p', ob_get_clean());

    if (!empty($entry->attachment)) {
        $attachments = glossary_print_attachments($entry, $cm, 'html');
        $definition .= html_writer::tag('p', $attachments);
    }

    if (core_tag_tag::is_enabled('mod_glossary', 'glossary_entries')) {
        $output = $PAGE->get_renderer('core');
        $definition .= $output->tag_list(core_tag_tag::get_item_tags('mod_glossary', 'glossary_entries', $entry->id),
            null, 'glossary-tags');
    }

    // Trigger view.
    glossary_entry_view($entry, $context);

    return $definition;
}
