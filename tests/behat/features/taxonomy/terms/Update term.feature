Feature: Update term

    Scenario: I can update term
        Given there is a taxonomy "category"
        And which has term "My term"
        When I update term "My term" with new name "New term name"
        Then term "My term" should be updated

    Scenario: Term should be translated when I update it in foreign locale
        Given there are available locales "en_US,pl_PL"
        And there is a taxonomy "category"
        And which has term "My term"
        When I update term "My term" with new name "New term name", in "pl_PL" locale
        Then term "My term" should be translated in locale "pl_PL"

    Scenario: Term should be translated when I update already translated name in foreign locale
        Given there are available locales "en_US,pl_PL"
        And there is a taxonomy "category"
        And which has term "My term"
        And which has term "My term" translated to "pl_PL" with name "New PL term name"
        When I update term "My term" with new name "New term name", in "pl_PL" locale
        Then term "My term" should not be translated
