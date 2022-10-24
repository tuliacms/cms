Feature: Delete website

  Scenario: I cannot delete last website in system
    Given there is a website "Default website", with default locale "en_US"
    When I delete this website
    Then website should not be deleted, because "At least one website must be active"

  Scenario: I cannot delete website if this is the last active website
    Given there is a website "Default website", with default locale "en_US"
    And there is at least one other inactive website in system
    When I delete this website
    Then website should not be deleted, because "At least one website must be active"

  Scenario: I cannot delete website I'm on right now
    Given there is a website "Default website", with default locale "en_US"
    And there is at least one other active website in system
    And I am on the website "Default website", and locale "en_US" right now
    When I delete this website
    Then website should not be deleted, because "Cannot delete current website"

  Scenario: I can delete website
    Given there is a website "Default website", with default locale "en_US"
    And there is at least one other active website in system
    When I delete this website
    Then website should be deleted
