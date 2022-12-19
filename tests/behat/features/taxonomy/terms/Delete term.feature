Feature: Delete term

    Scenario: I can delete term
        Given there is a taxonomy "category"
        And there is a term "My term"
        When I delete term "My term"
        Then term "My term" should be deleted

    Scenario: I cannot delete non-existent term
        Given there is a taxonomy "category"
        When I delete term "Non-existent term"
        Then term should not be deleted
