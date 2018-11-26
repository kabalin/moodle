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
 * Tests for class \core_customfield\api.
 *
 * @package    core_customfield
 * @category   phpunit
 * @copyright  Toni Barbera <toni@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_customfield\api;
use \core_customfield\category_controller;

/**
 * Functional test for class \core_customfield\api
 */
class core_customfield_api_testcase extends advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Tests for \core_customfield\api::field_factory() behaviour.
     */
    public function test_field_factory() {
        // Create the category.
        $categorydata            = new stdClass();
        $categorydata->name      = 'aaaa';
        $categorydata->component = 'core_course';
        $categorydata->area      = 'course';
        $categorydata->itemid    = 0;
        $categorydata->contextid = 1;
        $category0               = new category_controller(0, $categorydata);
        $category0->save();

        // Add fields to this category.
        $fielddata                = new stdClass();
        $fielddata->nameshortname = 'aaaa';
        $fielddata->categoryid    = $category0->get('id');
        $fielddata->configdata    = "{\"required\":\"0\",\"uniquevalues\":\"0\",\"locked\":\"0\",\"visibility\":\"0\",
                                    \"defaultvalue\":\"\",\"displaysize\":0,\"maxlength\":0,\"ispassword\":\"0\",
                                    \"link\":\"\",\"linktarget\":\"\"}";

        $field0 = new \customfield_checkbox\field_controller();
        $field0->set('name', $fielddata->nameshortname);
        $field0->set('shortname', $fielddata->nameshortname);
        $field0->set('categoryid', $category0->get('id'));
        $field0->set('type', 'checkbox');
        $field0->set('configdata', $fielddata->configdata);
        $field0->set_category($category0);
        $field0->save();

        $fielddata->nameshortname = 'bbbb';
        $field1 = new \customfield_date\field_controller();
        $field1->set('name', $fielddata->nameshortname);
        $field1->set('shortname', $fielddata->nameshortname);
        $field1->set('categoryid', $category0->get('id'));
        $field1->set('type', 'date');
        $field1->set('configdata', $fielddata->configdata);
        $field1->set_category($category0);
        $field1->save();

        $fielddata->nameshortname = 'cccc';
        $field2 = new \customfield_select\field_controller();
        $field2->set('name', $fielddata->nameshortname);
        $field2->set('shortname', $fielddata->nameshortname);
        $field2->set('categoryid', $category0->get('id'));
        $field2->set('type', 'select');
        $field2->set('configdata', $fielddata->configdata);
        $field2->set_category($category0);
        $field2->save();

        $fielddata->nameshortname = 'dddd';
        $field3 = new \customfield_text\field_controller();
        $field3->set('name', $fielddata->nameshortname);
        $field3->set('shortname', $fielddata->nameshortname);
        $field3->set('categoryid', $category0->get('id'));
        $field3->set('type', 'text');
        $field3->set('configdata', $fielddata->configdata);
        $field3->set_category($category0);
        $field3->save();

        $fielddata->nameshortname = 'eeee';
        $field4 = new \customfield_textarea\field_controller();
        $field4->set('name', $fielddata->nameshortname);
        $field4->set('shortname', $fielddata->nameshortname);
        $field4->set('categoryid', $category0->get('id'));
        $field4->set('type', 'textarea');
        $field4->set('configdata', $fielddata->configdata);
        $field4->set_category($category0);
        $field4->save();

        $this->assertInstanceOf('customfield_checkbox\field_controller', api::field_factory($field0->get('id')));
        $this->assertInstanceOf('customfield_date\field_controller', api::field_factory($field1->get('id')));
        $this->assertInstanceOf('customfield_select\field_controller', api::field_factory($field2->get('id')));
        $this->assertInstanceOf('customfield_text\field_controller', api::field_factory($field3->get('id')));
        $this->assertInstanceOf('customfield_textarea\field_controller', api::field_factory($field4->get('id')));
    }

    /**
     * Tests for \core_customfield\api::move_category() behaviour.
     *
     * This replicates what is happening when categories are moved
     * in the interface using drag-drop.
     */
    public function test_move_category() {
        // Create the categories.
        $categorydata            = new stdClass();
        $categorydata->name      = 'aaaa';
        $categorydata->component = 'core_course';
        $categorydata->area      = 'course';
        $categorydata->itemid    = 0;
        $categorydata->contextid = 1;
        $category0               = new category_controller(0, $categorydata);
        $category0->save();
        $id0 = $category0->get('id');

        $categorydata->name = 'bbbb';
        $category1          = new category_controller(0, $categorydata);
        $category1->save();
        $id1 = $category1->get('id');

        $categorydata->name = 'cccc';
        $category2          = new category_controller(0, $categorydata);
        $category2->save();
        $id2 = $category2->get('id');

        $categorydata->name = 'dddd';
        $category3          = new category_controller(0, $categorydata);
        $category3->save();
        $id3 = $category3->get('id');

        $categorydata->name = 'eeee';
        $category4          = new category_controller(0, $categorydata);
        $category4->save();
        $id4 = $category4->get('id');

        $categorydata->name = 'ffff';
        $category5          = new category_controller(0, $categorydata);
        $category5->save();
        $id5 = $category5->get('id');

        // Check order after re-fetch.
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);

        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);
        $this->assertSame((int) $category3->get('sortorder'), 3);
        $this->assertSame((int) $category4->get('sortorder'), 4);
        $this->assertSame((int) $category5->get('sortorder'), 5);

        // Move up 1 position.
        api::move_category(new category_controller($id3), $id2);
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);
        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 3);
        $this->assertSame((int) $category3->get('sortorder'), 2);
        $this->assertSame((int) $category4->get('sortorder'), 4);
        $this->assertSame((int) $category5->get('sortorder'), 5);

        // Move down 1 position.
        api::move_category(new category_controller($id2), $id3);
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);
        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);
        $this->assertSame((int) $category3->get('sortorder'), 3);
        $this->assertSame((int) $category4->get('sortorder'), 4);
        $this->assertSame((int) $category5->get('sortorder'), 5);

        // Move up 2 positions.
        api::move_category(new category_controller($id4), $id2);
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);
        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 3);
        $this->assertSame((int) $category3->get('sortorder'), 4);
        $this->assertSame((int) $category4->get('sortorder'), 2);
        $this->assertSame((int) $category5->get('sortorder'), 5);

        // Move down 2 positions.
        api::move_category(new category_controller($id4), $id5);
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);
        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);
        $this->assertSame((int) $category3->get('sortorder'), 3);
        $this->assertSame((int) $category4->get('sortorder'), 4);
        $this->assertSame((int) $category5->get('sortorder'), 5);

        // Move up 3 positions.
        api::move_category(new category_controller($id4), $id1);
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);
        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 2);
        $this->assertSame((int) $category2->get('sortorder'), 3);
        $this->assertSame((int) $category3->get('sortorder'), 4);
        $this->assertSame((int) $category4->get('sortorder'), 1);
        $this->assertSame((int) $category5->get('sortorder'), 5);

        // Move down 3 positions.
        api::move_category(new category_controller($id4), $id5);
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);
        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);
        $this->assertSame((int) $category3->get('sortorder'), 3);
        $this->assertSame((int) $category4->get('sortorder'), 4);
        $this->assertSame((int) $category5->get('sortorder'), 5);

        //Move to the end of the list.
        api::move_category(new category_controller($id2), 0);
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);
        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 5);
        $this->assertSame((int) $category3->get('sortorder'), 2);
        $this->assertSame((int) $category4->get('sortorder'), 3);
        $this->assertSame((int) $category5->get('sortorder'), 4);
    }

    /**
     * Tests for \core_customfield\api::move_category() behaviour.
     *
     * This replicates what is happening when categories sort order
     * is set incorrectly.
     */
    public function test_reorder_categories() {
        // Create the categories.
        $categorydata            = new stdClass();
        $categorydata->name      = 'aaaa';
        $categorydata->component = 'core_course';
        $categorydata->area      = 'course';
        $categorydata->itemid    = 0;
        $categorydata->contextid = 1;
        $category0               = new category_controller(0, $categorydata);
        $category0->save();
        $id0 = $category0->get('id');

        $categorydata->name = 'bbbb';
        $category1          = new category_controller(0, $categorydata);
        $category1->save();
        $id1 = $category1->get('id');

        $categorydata->name = 'cccc';
        $category2          = new category_controller(0, $categorydata);
        $category2->save();
        $id2 = $category2->get('id');

        $categorydata->name = 'dddd';
        $category3          = new category_controller(0, $categorydata);
        $category3->save();
        $id3 = $category3->get('id');

        $categorydata->name = 'eeee';
        $category4          = new category_controller(0, $categorydata);
        $category4->save();
        $id4 = $category4->get('id');

        $categorydata->name = 'ffff';
        $category5          = new category_controller(0, $categorydata);
        $category5->save();
        $id5 = $category5->get('id');

        // Check order after re-fetch.
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);

        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);
        $this->assertSame((int) $category3->get('sortorder'), 3);
        $this->assertSame((int) $category4->get('sortorder'), 4);
        $this->assertSame((int) $category5->get('sortorder'), 5);

        // Wrong sortorder values forced.
        $category0->set('sortorder', 101);
        $category0->save();
        $category1->set('sortorder', 42);
        $category1->save();
        $category2->set('sortorder', 3);
        $category2->save();
        $category3->set('sortorder', 14);
        $category3->save();
        $category4->set('sortorder', 15);
        $category4->save();
        $category5->set('sortorder', 92);
        $category5->save();

        // Check order after re-fetch.
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);

        $this->assertSame((int) $category0->get('sortorder'), 101);
        $this->assertSame((int) $category1->get('sortorder'), 42);
        $this->assertSame((int) $category2->get('sortorder'), 3);
        $this->assertSame((int) $category3->get('sortorder'), 14);
        $this->assertSame((int) $category4->get('sortorder'), 15);
        $this->assertSame((int) $category5->get('sortorder'), 92);

        // Force reorder, reload and check status.
        api::move_category($category0, 0);

        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);
        $category4 = new category_controller($id4);
        $category5 = new category_controller($id5);

        $this->assertSame((int) $category2->get('sortorder'), 0);
        $this->assertSame((int) $category3->get('sortorder'), 1);
        $this->assertSame((int) $category4->get('sortorder'), 2);
        $this->assertSame((int) $category1->get('sortorder'), 3);
        $this->assertSame((int) $category5->get('sortorder'), 4);
        $this->assertSame((int) $category0->get('sortorder'), 5);
    }

    /**
     * Tests for \core_customfield\api::list_categories() behaviour.
     */
    public function test_list_categories() {
        // Create the categories.
        $options = [
            'component' => 'core_course',
            'area'      => 'course',
            'itemid'    => 0,
            'contextid' => 1
        ];

        $categorydata            = new stdClass();
        $categorydata->name      = 'aaaa';
        $categorydata->component = $options['component'];
        $categorydata->area      = $options['area'];
        $categorydata->itemid    = $options['itemid'];
        $categorydata->contextid = $options['contextid'];
        $category0               = new category_controller(0, $categorydata);
        $category0->save();

        $categorydata->name = 'bbbb';
        $category1          = new category_controller(0, $categorydata);
        $category1->save();

        $categorydata->name = 'cccc';
        $category2          = new category_controller(0, $categorydata);
        $category2->save();

        $categorydata->name = 'dddd';
        $category3          = new category_controller(0, $categorydata);
        $category3->save();

        $categorydata->name = 'eeee';
        $category4          = new category_controller(0, $categorydata);
        $category4->save();

        $categorydata->name = 'ffff';
        $category5          = new category_controller(0, $categorydata);
        $category5->save();

        // Let's test counts.
        $this->assertCount(6, api::list_categories($options['component'], $options['area'], $options['itemid']));
        $category5->delete();
        $this->assertCount(5, api::list_categories($options['component'], $options['area'], $options['itemid']));
        $category4->delete();
        $this->assertCount(4, api::list_categories($options['component'], $options['area'], $options['itemid']));
        $category3->delete();
        $this->assertCount(3, api::list_categories($options['component'], $options['area'], $options['itemid']));
        $category2->delete();
        $this->assertCount(2, api::list_categories($options['component'], $options['area'], $options['itemid']));
        $category1->delete();
        $this->assertCount(1, api::list_categories($options['component'], $options['area'], $options['itemid']));
        $category0->delete();
        $this->assertCount(0, api::list_categories($options['component'], $options['area'], $options['itemid']));
    }

    public function test_load_data() {

    }

    public function test_get_fields_with_data() {

    }

    public function test_field_types() {

    }

    public function test_save_field() {

    }

    public function test_save_category() {

    }
}
