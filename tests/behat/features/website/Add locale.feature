Feature: Add locale

  Scenario: I cannot add default locale twice
    Given there is a website "Default website", with default locale "en_US"
    When I add locale "en_US" to this website
    Then new locale should not be added because "This locale already exists in this website"

  Scenario: I cannot add locale when other locale with this code is in website
    Given there is a website "Default website", with default locale "en_US"
    And which have locale "pl_PL"
    When I add locale "pl_PL" to this website
    Then new locale should not be added because "This locale already exists in this website"

  Scenario: I can add new locale to website
    Given there is a website "Default website", with default locale "en_US"
    When I add locale "pl_PL" to this website
    Then new locale should be added
