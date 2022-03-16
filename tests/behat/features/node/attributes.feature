Feature: Manage attributes
    In order to update node details
    An admin should be able to
    Manage attributes

    Scenario: Add attribute to node
        Given there is a node
        And node dont have attribute "my_attribute"
        When admin adds attribute "my_attribute" with value "Some value"
        Then node have attribute "my_attribute" with value "Some value"

    Scenario: Update node attribute
        Given there is a node
        And node has attribute "my_attribute" with value "Some value"
        When admin adds attribute "my_attribute" with value "Some other value"
        Then node have attribute "my_attribute" with value "Some other value"

    Scenario: Remove attribute from node
        Given there is a node
        And node has attribute "my_attribute" with value "Some value"
        When admin removes attribute "my_attribute"
        Then node dont have attribute "my_attribute"
