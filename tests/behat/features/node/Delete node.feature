Feature: Delete node

  Background:
    Given there is a node "My root node"

  Scenario: I can't delete node if this node has any children
    Given there is node "My child node" which is a child node of "My root node"
    When I delete this node
    Then this node should not be deleted, because: "Node has child subnodes"

  Scenario: I can delete node
    When I delete this node
    Then this node should be deleted
