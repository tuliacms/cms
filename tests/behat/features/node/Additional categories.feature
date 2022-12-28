Feature: Main category

    Scenario: I can assign node to additional category
        Given there is a node "My node"
        When I assign this node to additional category "Category 1" of taxonomy "category"
        Then this node should be assigned to "additional" category "Category 1" of taxonomy "category"

    Scenario: Node can be autoassigned to parent categories of additional category assigned by me
        Given the category "Category 1.1.1" of taxonomy "category" has parents of "Category 1.1,Category 1"
        And there is a node "My node"
        When I assign this node to additional category "Category 1.1.1" of taxonomy "category"
        Then this node should be assigned to "additional" category "Category 1.1.1" of taxonomy "category"
        And this node should be assigned to "calculated" category "Category 1.1" of taxonomy "category"
        And this node should be assigned to "calculated" category "Category 1" of taxonomy "category"

    Scenario: I can assign Node to two different additional categories of the same taxonomy
        Given there is a node "My node"
        And which is assigned to "additional" category "Category 1" of taxonomy "category"
        When I assign this node to additional category "Category 2" of taxonomy "category"
        Then this node should be assigned to "additional" category "Category 1" of taxonomy "category"
        And this node should be assigned to "additional" category "Category 2" of taxonomy "category"

    Scenario: I can assign Node to two different additional categories of the different taxonomies
        Given there is a node "My node"
        And which is assigned to "additional" category "Category 1" of taxonomy "category"
        When I assign this node to additional category "Tag 1" of taxonomy "tag"
        Then this node should be assigned to "additional" category "Category 1" of taxonomy "category"
        And this node should be assigned to "additional" category "Tag 1" of taxonomy "tag"

    Scenario: I cannot assign Node twice to the same term
        Given there is a node "My node"
        And which is assigned to "additional" category "Category 1" of taxonomy "category"
        When I assign this node to additional category "Category 1" of taxonomy "category"
        Then assignation to categories for this node should not be changed

    Scenario: I can unassign Node from additional category
        Given there is a node "My node"
        And which is assigned to "additional" category "Category 1" of taxonomy "category"
        When I unassign this node from additional category "Category 1" of taxonomy "category"
        Then this node should not be assigned to "additional" category "Category 1" of taxonomy "category"

    Scenario: Node can be auto-unassigned from parent categories of additional category unassigned by me
        Given the category "Term 1.1.1" of taxonomy "category" has parents of "Term 1.1,Term 1"
        And there is a node "My node"
        And which is assigned to "additional" category "Term 1.1.1" of taxonomy "category"
        When I unassign this node from additional category "Term 1.1.1" of taxonomy "category"
        Then this node should not be assigned to "additional" category "Term 1.1.1" of taxonomy "category"
        And this node should not be assigned to "additional" category "Term 1.1" of taxonomy "category"
        And this node should not be assigned to "additional" category "Term 1" of taxonomy "category"

    Scenario: I can replace existing additional categories assignations to new one
        Given there is a node "My node"
        And which is assigned to additional category "Category 1" of taxonomy "category"
        When I replace additional categories assignations in this node to term "Tag 1" of taxonomy "tag"
        Then this node should not be assigned to "additional" category "Category 1" of taxonomy "category"
        Then this node should be assigned to "additional" category "Tag 1" of taxonomy "tag"

    Scenario: I cannot assign node to additional category when is already assigned to the same main category
        Given there is a node "My node"
        And which is assigned to main category "Category 1" of taxonomy "category"
        When I assign this node to additional category "Category 1" of taxonomy "category"
        Then assignation to categories for this node should not be changed

    Scenario: Replacing additional categories with empty collection cannot unassign main category
        Given there is a node "My node"
        And which is assigned to main category "Category 1" of taxonomy "category"
        When I replace additional categories assignations in this node with empty collection
        Then this node should be assigned to "main" category "Category 1" of taxonomy "category"
