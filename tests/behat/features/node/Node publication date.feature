Feature: Node publication date

    Scenario: New node has published date set to "now"
        Given there is a node "My root node"
        Then node should be published at "now"

    Scenario: Admin can change published date
        Given there is a node "My root node"
        When admin change published date to "2030-01-01 01:00:00"
        Then node should be published at "2030-01-01 01:00:00"

    Scenario: New node has timeless publication end date
        Given there is a node "My root node"
        Then node should be published forever

    Scenario: Admin can change published end date
        Given there is a node "My root node"
        When admin change published end date to "2030-01-01 01:00:00"
        Then node is published to "2030-01-01 01:00:00"
        When admin change node to published forever
        Then node should be published forever
