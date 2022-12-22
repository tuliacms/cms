Feature: Node publication date

    Background:
        Given now is "2022-12-01 01:00:00"

    Scenario: New node is published at "now"
        When I create node "My node"
        Then node should be published to forever at "2022-12-01 01:00:00"

    Scenario: I can publish existing node again overwriting existing date
        Given there is a node "My node"
        When I publish this node at "2023-02-13 01:00:00"
        Then node should be published to forever at "2023-02-13 01:00:00"

    Scenario: I can publis node only for hour in some point of future
        Given there is a node "My node"
        When I publish this node at "2023-03-03 01:00:00", to "2023-03-03 02:00:00"
        Then node should be published at "2023-03-03 01:00:00" to "2023-03-03 02:00:00"
