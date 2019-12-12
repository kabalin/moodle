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
 * Display autolinked glossary concept in modal.
 *
 * @module     filter_glossary/concept
 * @package    filter_glossary
 * @copyright  2019 Ruslan Kabalin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/modal_factory',
        'core/modal_events',
        'core/fragment'],
function($, ModalFactory, ModalEvents, Fragment) {

    /**
     * Element selectors.
     */
    var SELECTOR = {
        CONCEPT: "a.glossary.autolink.concept"
    };

    /**
     * Flag to prevent same event attachment multiple times.
     */
    var isInitialised = false;

    /**
     * Modal with glossary concept.
     *
     * @param {jQuery} triggerElement
     * @param {Number} contextId
     */
    var showConceptModal = function(triggerElement, contextId) {
        var params = {eid: triggerElement.data('eid')};
        ModalFactory.create({
            title: triggerElement.attr('title'),
            body: Fragment.loadFragment('filter_glossary', 'get_concept', contextId, params),
            large: true,
        }, triggerElement).done(function(modal) {
            // When we close the dialogue by clicking on X in the top right corner.
            modal.getRoot().on(ModalEvents.hidden, function() {
                modal.destroy();
            });
            modal.show();
            return null;
        }).fail(Notification.exception);
    };

    return /** @alias filter_glossary/concept */ {

        /**
         * Initialise the page.
         *
         * @param {Number} contextId
         */
        init: function(contextId) {
            if (!isInitialised) {
                $(document.body).on('click', SELECTOR.CONCEPT, function(e) {
                    e.preventDefault();
                    showConceptModal($(e.currentTarget), contextId);
                });
                isInitialised = true;
            }
        }
    };
});
