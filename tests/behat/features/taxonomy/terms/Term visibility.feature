Feature: Term visibility

    Scenario: I can turn term visibility on
        Given there is a taxonomy "category"
        And which has not-visible term "My term"
        When I turn term "My term" visibility on, in "en_US" locale
        Then term "My term" should be visible in locale "en_US"

    Scenario: I can turn term visibility off
        Given there is a taxonomy "category"
        And which has term "My term"
        When I turn term "My term" visibility off, in "en_US" locale
        Then term "My term" should be invisible in locale "en_US"

    Scenario: I cannot turn term visibility off when term is invisible
        Given there is a taxonomy "category"
        And which has not-visible term "My term"
        When I turn term "My term" visibility off, in "en_US" locale
        Then term "My term" visibility should not be changed
