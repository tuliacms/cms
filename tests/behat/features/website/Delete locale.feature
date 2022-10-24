Feature: Delete locale

  Scenario: I cannot delete default locale
    Given there is a website "Default website", with default locale "en_US"
    When I delete locale "en_US"
    Then locale should not be deleted, because "Cannot delete default locale"

  Scenario: I cannot delete locale I'm on right now
    Given there is a website "Default website", with default locale "en_US"
    And which have locale "pl_PL"
    And I am on the website "Default website", and locale "pl_PL" right now
    When I delete locale "pl_PL"
    Then locale should not be deleted, because "Cannot delete locale that You are on"

  Scenario: I cannot delete non-existent locale
    Given there is a website "Default website", with default locale "en_US"
    When I delete locale "de_DE"
    Then locale should not be deleted, because "Locale does not exists"

  Scenario: I can delete locale
    Given there is a website "Default website", with default locale "en_US"
    And which have locale "pl_PL"
    When I delete locale "pl_PL"
    Then locale should be deleted

  Scenario: I can delete current locale, but from foreign website
    Given there is a website "Default website", with default locale "en_US"
    And which have locale "pl_PL"
    And I am on the website "Foreign website", and locale "pl_PL" right now
    When I delete locale "pl_PL"
    Then locale should be deleted
