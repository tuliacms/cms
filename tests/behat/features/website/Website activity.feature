Feature: Website activity

  Scenario: I cannot disable website when is already turned off
    Given there is a website "Default website", with default locale "en_US"
    And which is inactive
    When I disable this website
    Then website should not be disabled
    And website should not be updated

  Scenario: I can disable website
    Given there is a website "Default website", with default locale "en_US"
    When I disable this website
    Then website should be disabled
    And website should be updated

  Scenario: I cannot enable website when is already turned on
    Given there is a website "Default website", with default locale "en_US"
    When I enable this website
    Then website should not be enabled
    And website should not be updated

  Scenario: I can enable website
    Given there is a website "Default website", with default locale "en_US"
    And which is inactive
    When I enable this website
    Then website should be enabled
    And website should be updated
