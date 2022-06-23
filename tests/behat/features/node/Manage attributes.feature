Feature: Manage attributes

    Scenario: Add attribute to node
        Given there is a node "My root node"
        And this node don't have attribute "my_attribute"
        When admin adds attribute "my_attribute" with value "Some value" to this node
        Then node should have attribute "my_attribute" with value "Some value"

    Scenario: Update node attribute
        Given there is a node "My root node"
        And this node has attribute "my_attribute" with value "Some value"
        When admin adds attribute "my_attribute" with value "Some other value" to this node
        Then node should have attribute "my_attribute" with value "Some other value"

    Scenario: Remove attribute from node
        Given there is a node "My root node"
        And this node has attribute "my_attribute" with value "Some value"
        When admin removes attribute "my_attribute" from this node
        Then this node should not have attribute "my_attribute"
