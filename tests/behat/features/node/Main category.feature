Feature: Main category

    Scenario: I can assign node to main category
        Given there is a node "My node"
        When I assign this node to main category "Category 1" of taxonomy "category"
        Then this node should be assigned to "main" category "Category 1" of taxonomy "category"

    Scenario: Node can be autoassigned to parent categories of category assigned by me
        Given the category "Category 1.1.1" of taxonomy "category" has parents of "Category 1.1,Category 1"
        And there is a node "My node"
        When I assign this node to main category "Category 1.1.1" of taxonomy "category"
        Then this node should be assigned to "main" category "Category 1.1.1" of taxonomy "category"
        And this node should be assigned to "calculated" category "Category 1.1" of taxonomy "category"
        And this node should be assigned to "calculated" category "Category 1" of taxonomy "category"

    Scenario: I can unassign node from main category
        Given there is a node "My node"
        And which is assigned to main category "Category 1" of taxonomy "category"
        When I unassign this node from main category
        Then this node should not be assigned to "main" category "Category 1" of taxonomy "category"

    Scenario: I cannot assign Node twice to the same main category
        Given there is a node "My node"
        And which is assigned to "main" category "Category 1" of taxonomy "category"
        When I assign this node to main category "Category 1" of taxonomy "category"
        Then assignation to categories for this node should not be changed

    Scenario: Node can be auto-unassigned from parent categories of category unassigned by me
        Given the category "Category 1.1.1" of taxonomy "category" has parents of "Category 1.1,Category 1"
        And there is a node "My node"
        And which is assigned to main category "Category 1.1.1" of taxonomy "category"
        When I unassign this node from main category
        Then this node should not be assigned to "main" category "Category 1.1.1" of taxonomy "category"
        And this node should not be assigned to "calculated" category "Category 1.1" of taxonomy "category"
        And this node should not be assigned to "calculated" category "Category 1" of taxonomy "category"
