Feature: Purposes

  Background:
    Given there is a singular purpose "Homepage"
    And there is a node "My root node"

  Scenario: I cannot add singular flag to multiple nodes
    Given there is a node with purpose "Homepage"
    When I impose purpose "Homepage" to this node
    Then purpose should not be imposed to this node, becuase "This singular purpose is imposed to another node"

  Scenario: I can add non-singular purpose for multiple nodes
    Given there is a non-singular purpose named "Privacy policy"
    And there is a node with purpose "Privacy policy"
    When I impose purpose "Privacy policy" to this node
    Then purpose "Privacy policy" should be imposed to this node

  Scenario: I can add purpose to node
    When I impose purpose "Homepage" to this node
    Then purpose "Homepage" should be imposed to this node

  Scenario: I cannot add multiple times same flag to one node
    Given this node has purpose named "Homepage"
    When I impose purpose "Homepage" to this node
    Then purpose should not be imposed to this node

  Scenario: I can impose multiple purposes at time
    Given there is a singular purpose "Purpose 1"
    And there is a singular purpose "Purpose 2"
    And there is a singular purpose "Privacy policy"
    And this node has purpose named "Homepage"
    And this node has purpose named "Privacy policy"
    When I persist purposes "Homepage,Purpose 1,Purpose 2" to this node
    Then this node should has updated purposes of "Homepage,Purpose 1,Purpose 2"
