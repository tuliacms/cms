Feature: Parent-child term

    Scenario: I can move term as child of another term
        Given there is a taxonomy "category"
        And which has term "Parent term"
        And which has term "Child term"
        When I move term "Child term" as child of "Parent term"
        Then term "Child term" should be moved as child of "Parent term"

    Scenario: I can update whole hierarchy of terms
        Given there is a taxonomy "category"
        And which has term "Term 1"
        And which has term "Term 1.1"
        And which has term "Term 1.1.1"
        And which has term "Term 1.1.2"
        When I update terms hierarchy as the following "Term 1:Term 1.1;Term 1.1:Term 1.1.1;Term 1.1:Term 1.1.2"
        Then term "Term 1.1.2" should be moved as child of "Term 1.1"
        Then term "Term 1.1.1" should be moved as child of "Term 1.1"
        Then term "Term 1.1" should be moved as child of "Term 1"
        Then term "Term 1" should be a root
