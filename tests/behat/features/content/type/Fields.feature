Feature: Fields

  Scenario: I can't add field to non-existent fields group
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    When I adds new field named "Field name", with code "field_code", of type "text", to group "nonexistent_group"
    Then field should not be added

  Scenario: I can't add field when its parent not exists
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name 1" with code "group_code_1" for section "main"
    When I adds new field named "Field name", with code "field_code", of type "text", to group "nonexistent_group", for parent "parent_non_existent_field"
    Then field should not be added

  Scenario: I can't add field with code that already exists
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    And there is a field named "Field name", with code "field_code", of type "text", to group "group_code"
    When I adds new field named "Field name", with code "field_code", of type "text", to group "group_code"
    Then field should not be added

  Scenario: I can add new field
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    When I adds new field named "Field name", with code "field_code", of type "text", to group "group_code"
    Then field should be added

  Scenario: I can add new field as child of another
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    And there is a field named "Parent field", with code "parent_field", of type "text", to group "group_code"
    When I adds new field named "Child field", with code "child_field", of type "text", to group "group_code", for parent "parent_field"
    Then field should be added

  Scenario: I can't update non-existent field
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    When I updates field "nonexistent_field" with name "New field name"
    Then field should not be updated

  Scenario: I can update field
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    And there is a field named "Field name", with code "field_code", of type "text", to group "group_code"
    When I updates field "field_code" with name "New field name"
    Then field should be updated

  Scenario: I can't remove non-existent field
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    When I removes field "nonexistent_field"
    Then field should not be removed

  Scenario: I can remove field
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    And there is a field named "Field name", with code "field_code", of type "text", to group "group_code"
    When I removes field "field_code"
    Then field should be removed

  Scenario Outline: I can sort fields
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    And there is a field named "Field 1", with code "field_code_1", of type "text", to group "group_code"
    And there is a field named "Field 2", with code "field_code_2", of type "text", to group "group_code"
    And there is a field named "Field 3", with code "field_code_3", of type "text", to group "group_code"
    When I sort fields to new order <positions>
    Then fields should be in order <expected>
    Examples:
      | positions                              | expected                               |
      | field_code_1,field_code_2,field_code_3 | field_code_1,field_code_2,field_code_3 |
      | field_code_2,field_code_3,field_code_1 | field_code_2,field_code_3,field_code_1 |
