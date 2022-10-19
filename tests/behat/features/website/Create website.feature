Feature: Create website

  Scenario: I can create website with default locale
    When I create website named "My website", with default locale "pl_PL"
    Then new website should be created
