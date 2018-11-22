@core @core_course @core_customfield
Feature: Teachers can edit course custom fields
  In order to have additional data on the course
  As a teacher
  I need to edit data for custom fields

  Background:
    Given the following "course custom field categories" exist:
      | name |
      | Category for test |
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I navigate to "Courses > Course custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Text field" "link"
    And I set the following fields to these values:
      | Name | Test field |
      | Short name | testfield |
    And I press "Save changes"
    And I log out

  Scenario: Have a text field on course edit form
    When I log in as "teacher1"
     And I am on "Course 1" course homepage
     And I navigate to "Edit settings" in current page administration
     And I expand all fieldsets
    Then I should see "Category for test"
     And I should see "Test field"

  Scenario: Search for course field
    When I log in as "teacher1"
     And I am on "Course 1" course homepage
     And I navigate to "Edit settings" in current page administration
     And I set the following fields to these values:
       | Test field | testcontent |
     And I press "Save and display"
     And I log out
     And I log in as "admin"
     And I go to the courses management page
    When I set the field "coursesearchbox" to "testcontent"
     And I press "Go"
    Then I should see "Course 1"

  Scenario: Create a course from the management interface
   When I log in as "admin"
    And I go to the courses management page
    And I should see the "Categories" management page
    And I click on category "Miscellaneous" in the management interface
    And I should see the "Course categories and courses" management page
    And I click on "Create new course" "link" in the "#course-listing" "css_element"
    When I set the following fields to these values:
      | Course full name | Course 2 |
      | Course short name | C2 |
      | Format | topics |
      | Test field | testcontent2 |
    And I press "Save and return"
    Then I should see the "Course categories and courses" management page
    And I click on "Sort by Course time created ascending" "link" in the ".course-listing-actions" "css_element"
    And I click on "Course 2" "link" in the "region-main" "region"
    And I click on "Edit" "link" in the ".course-detail" "css_element"
    And the following fields match these values:
      | Course full name | Course 2 |
      | Course short name | C2 |
      | Format | topics |
      | Test field | testcontent2 |
