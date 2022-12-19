Feature: Add term

    Scenario: I can add new term to existing taxonomy
        Given there is a taxonomy "category"
        When I add new term "My term" to this taxonomy
        Then new term "My term" should be added
        And new term should be root item

    Scenario: I can add term as child of another term
        Given there is a taxonomy "category"
        And there is a term "Parent term"
        When I add new term "My term" to this taxonomy, as child of "Parent term"
        Then new term "My term" should be added
        And new term should be child of "Parent term"
