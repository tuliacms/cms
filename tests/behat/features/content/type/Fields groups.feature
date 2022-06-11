Feature: Fields groups

  Scenario: I can't add new fields group, when it's code already exists
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    When I adds new fields group named "Group name" with code "group_code" for section "main"
    Then fields group should not be added

  Scenario: I can add new fields group
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    When I adds new fields group named "Group name" with code "group_code" for section "main"
    Then fields group should be added

  Scenario Outline:  I can sort field groups
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name 1" with code "group_code_1" for section "main"
    And there is a fields group in this ContentType, named "Group name 2" with code "group_code_2" for section "main"
    And there is a fields group in this ContentType, named "Group name 3" with code "group_code_3" for section "main"
    When I sort fields groups to new order <positions>
    Then fields groups should be in order <expected>
    Examples:
      | positions                              | expected                               |
      | group_code_1,group_code_2,group_code_3 | group_code_1,group_code_2,group_code_3 |
      | group_code_2,group_code_3,group_code_1 | group_code_2,group_code_3,group_code_1 |

  Scenario: I can't remove non-existent fields group
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    When I remove fields group with code "nonexistent_group"
    Then fields group should not be removed

  Scenario: I can remove fields group
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    When I remove fields group with code "group_code"
    Then fields group should be removed

  Scenario: I can rename fields group
    Given I have ContentType named "Content type", with code "content_type", with type "node"
    And there is a fields group in this ContentType, named "Group name" with code "group_code" for section "main"
    When I rename fields group with code "group_code" to "My new name"
    Then fields group "group_code" should be renamed to "My new name"
