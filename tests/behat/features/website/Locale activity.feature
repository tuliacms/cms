Feature: Locale activity

  Scenario: I cannot turn activity off when is already turned off
    Given there is a website "Default website", with default locale "en_US"
    And which have inactive locale "pl_PL"
    When I turn this website's locale "pl_PL" activity off
    Then website's locale "pl_PL" activity should not be turned off
    And website should not be updated

  Scenario: I can turn activity off
    Given there is a website "Default website", with default locale "en_US"
    And which have locale "pl_PL"
    When I turn this website's locale "pl_PL" activity off
    Then website's locale "pl_PL" activity should be turned off
    And website should be updated

  Scenario: I cannot turn activity on when is already turned on
    Given there is a website "Default website", with default locale "en_US"
    And which have locale "pl_PL"
    When I turn this website's locale "pl_PL" activity on
    Then website's locale "pl_PL" activity should not be turned on
    And website should not be updated

  Scenario: I can turn activity on
    Given there is a website "Default website", with default locale "en_US"
    And which have inactive locale "pl_PL"
    When I turn this website's locale "pl_PL" activity on
    Then website's locale "pl_PL" activity should be turned on
    And website should be updated
