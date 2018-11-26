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
 * Tests for class \core_customfield\category_controller.
 *
 * @package    core_customfield
 * @category   phpunit
 * @copyright  Toni Barbera <toni@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_customfield\category_controller;
use \core_customfield\field_controller;

/**
 * Functional test for class \core_customfield\category_controller.
 */
class core_customfield_category_testcase extends advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }


    /**
     * Tests for \core_customfield\category_controller::save() behaviour.
     */
    public function test_create_category() {

        // Create the category.
        $categorydata            = new stdClass();
        $categorydata->name      = 'aaaa';
        $categorydata->component = 'core_course';
        $categorydata->area      = 'course';
        $categorydata->itemid    = 0;
        $categorydata->contextid = 1;

        $category0 = new category_controller(0, $categorydata);
        $category0->save();

        // Confirm that base data was inserted correctly.
        $this->assertSame($category0->get('name'), $categorydata->name);
        $this->assertSame($category0->get('description'), null);
        $this->assertSame($category0->get('descriptionformat'), '0');
        $this->assertSame($category0->get('component'), $categorydata->component);
        $this->assertSame($category0->get('area'), $categorydata->area);
        $this->assertSame($category0->get('itemid'), $categorydata->itemid);
        $this->assertSame($category0->get('contextid'), $categorydata->contextid);
        $this->assertSame($category0->get('sortorder'), -1);
    }

    /**
     * Tests for \core_customfield\category_controller::save() behaviour.
     */
    public function test_create_categories_order() {

        // Create the category.
        $categorydata            = new stdClass();
        $categorydata->name      = 'aaaa';
        $categorydata->component = 'core_course';
        $categorydata->area      = 'course';
        $categorydata->itemid    = 0;
        $categorydata->contextid = 1;

        $category0 = new category_controller(0, $categorydata);
        $category0->save();

        // Creating 2nd category and check if sortorder is correct.
        $categorydata->name = 'bbbb';
        $category1 = new category_controller(0, $categorydata);
        $category1->save();

        // Check order after re-fetch.
        $id0 = $category0->get('id');
        $id1 = $category1->get('id');
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);

        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);

        // Creating 3rd category and check if sortorder is correct.
        $categorydata->name = 'cccc';
        $category2 = new category_controller(0, $categorydata);
        $category2->save();

        // Check order after re-fetch.
        $id2 = $category2->get('id');
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);

        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);

        // Creating 4th category and check if sortorder is correct.
        $categorydata->name = 'dddd';
        $category3 = new category_controller(0, $categorydata);
        $category3->save();

        // Check order after re-fetch.
        $id3 = $category3->get('id');
        $category0 = new category_controller($id0);
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);
        $category3 = new category_controller($id3);

        $this->assertSame((int) $category0->get('sortorder'), 0);
        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);
        $this->assertSame((int) $category3->get('sortorder'), 3);
    }

    /**
     * Tests for \core_customfield\category_controller::set() behaviour.
     */
    public function test_create_category_and_rename() {
        // Create the category.
        $categorydata            = new stdClass();
        $categorydata->name      = 'aaaa';
        $categorydata->component = 'core_course';
        $categorydata->area      = 'course';
        $categorydata->itemid    = 0;
        $categorydata->contextid = 1;

        $category0 = new category_controller(0, $categorydata);
        $category0->save();

        // Initially confirm that base data was inserted correctly.
        $this->assertSame($category0->get('name'), $categorydata->name);
        $this->assertSame($category0->get('description'), null);
        $this->assertSame($category0->get('descriptionformat'), '0');
        $this->assertSame($category0->get('component'), $categorydata->component);
        $this->assertSame($category0->get('area'), $categorydata->area);
        $this->assertSame($category0->get('itemid'), $categorydata->itemid);
        $this->assertSame($category0->get('contextid'), $categorydata->contextid);
        $this->assertSame($category0->get('sortorder'), -1);

        // Checking new name are correct updated.
        $newname = 'bbbb';
        $category0->set('name', $newname);
        $this->assertSame($category0->get('name'), $newname);

        // Checking new name are correct updated after save.
        $category0->save();
        $id = $category0->get('id');

        $category0 = new category_controller($id);
        $this->assertSame($category0->get('name'), $newname);
    }

    /**
     * Tests for \core_customfield\category_controller::delete() behaviour.
     */
    public function test_create_category_and_delete() {
        // Create the category.
        $categorydata            = new stdClass();
        $categorydata->name      = 'aaaa';
        $categorydata->component = 'core_course';
        $categorydata->area      = 'course';
        $categorydata->itemid    = 0;
        $categorydata->contextid = 1;

        $category0 = new category_controller(0, $categorydata);
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

        // Confirm that exist in the database.
        $this->assertTrue(category_controller::record_exists($id0));

        //Delete and confirm that is deleted.
        $category0->delete();
        $this->assertFalse(category_controller::record_exists($id0));

        // Confirm correct order after delete.
        // Check order after re-fetch.
        $category1 = new category_controller($id1);
        $category2 = new category_controller($id2);

        $this->assertSame((int) $category1->get('sortorder'), 1);
        $this->assertSame((int) $category2->get('sortorder'), 2);
    }

    /**
     * Tests for \core_customfield\category_controller::delete() behaviour.
     */
    public function test_categories_delete() {
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
        $fielddata->type          = 'text';
        $fielddata->categoryid    = $category0->get('id');
        $fielddata->configdata    = "{\"required\":\"0\",\"uniquevalues\":\"0\",\"locked\":\"0\",\"visibility\":\"0\",
                                    \"defaultvalue\":\"\",\"displaysize\":0,\"maxlength\":0,\"ispassword\":\"0\",
                                    \"link\":\"\",\"linktarget\":\"\"}";

        $field0 = new \customfield_text\field_controller();
        $field0->set('name', $fielddata->nameshortname);
        $field0->set('shortname', $fielddata->nameshortname);
        $field0->set('categoryid', $category0->get('id'));
        $field0->set('type', $fielddata->type);
        $field0->set('configdata', $fielddata->configdata);
        $field0->set_category($category0);
        $field0->save();

        $fielddata->nameshortname = 'bbbb';
        $field1                   = new \customfield_text\field_controller();
        $field1->set('name', $fielddata->nameshortname);
        $field1->set('shortname', $fielddata->nameshortname);
        $field1->set('categoryid', $category0->get('id'));
        $field1->set('type', $fielddata->type);
        $field1->set('configdata', $fielddata->configdata);
        $field1->set_category($category0);
        $field1->save();

        $fielddata->nameshortname = 'cccc';
        $field2                   = new \customfield_text\field_controller();
        $field2->set('name', $fielddata->nameshortname);
        $field2->set('shortname', $fielddata->nameshortname);
        $field2->set('categoryid', $category0->get('id'));
        $field2->set('type', $fielddata->type);
        $field2->set('configdata', $fielddata->configdata);
        $field2->set_category($category0);
        $field2->save();

        $fielddata->nameshortname = 'dddd';
        $field3                   = new \customfield_text\field_controller();
        $field3->set('name', $fielddata->nameshortname);
        $field3->set('shortname', $fielddata->nameshortname);
        $field3->set('categoryid', $category0->get('id'));
        $field3->set('type', $fielddata->type);
        $field3->set('configdata', $fielddata->configdata);
        $field3->set_category($category0);
        $field3->save();

        $fielddata->nameshortname = 'eeee';
        $field4                   = new \customfield_text\field_controller();
        $field4->set('name', $fielddata->nameshortname);
        $field4->set('shortname', $fielddata->nameshortname);
        $field4->set('categoryid', $category0->get('id'));
        $field4->set('type', $fielddata->type);
        $field4->set('configdata', $fielddata->configdata);
        $field4->set_category($category0);
        $field4->save();

        $fielddata->nameshortname = 'ffff';
        $field5                   = new \customfield_text\field_controller();
        $field5->set('name', $fielddata->nameshortname);
        $field5->set('shortname', $fielddata->nameshortname);
        $field5->set('categoryid', $category0->get('id'));
        $field5->set('type', $fielddata->type);
        $field5->set('configdata', $fielddata->configdata);
        $field5->set_category($category0);
        $field5->save();

        // Check that category have fields and store ids for future checks
        $this->assertCount(6, $category0->fields());

        $category0fieldsids = array();
        foreach ($category0->fields() as $field) {
            $category0fieldsids[] = $field->get('id');
        }

        // Delete category.
        $this->assertTrue($category0->delete());

        // Check that the category fields has been deleted.
        foreach ($category0fieldsids as $fieldid) {
            $this->assertFalse(field_controller::record_exists($fieldid));
        }
    }
}
