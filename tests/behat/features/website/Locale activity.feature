Feature: Locale activity

  Scenario: I cannot disable locale when is already turned off
    Given there is a website "Default website", with default locale "en_US"
    And which have inactive locale "pl_PL"
    When I disable locale "pl_PL" of this website
    Then website's locale "pl_PL" should not be disabled
    And website should not be updated

  Scenario: I can disable locale
    Given there is a website "Default website", with default locale "en_US"
    And which have locale "pl_PL"
    When I disable locale "pl_PL" of this website
    Then website's locale "pl_PL" should be disabled
    And website should be updated

  Scenario: I cannot enable locale when is already turned on
    Given there is a website "Default website", with default locale "en_US"
    And which have locale "pl_PL"
    When I enable "pl_PL" of this website
    Then website's locale "pl_PL" should not be enabled
    And website should not be updated

  Scenario: I can enable locale
    Given there is a website "Default website", with default locale "en_US"
    And which have inactive locale "pl_PL"
    When I enable "pl_PL" of this website
    Then website's locale "pl_PL" should be enabled
    And website should be updated
