Feature: Content type Creation

  Scenario: I can't ContentType when type not exists
    When I creates ContentType named "Content type", with code "content_type", with type "node"
    Then new ContentType should not be created

  Scenario: I can't create ContentType when code contains characters other than alphanumeric and underscore
    When I creates ContentType named "Content type", with code "S*&N&tg*&", with type "node"
    Then new ContentType should not be created

  Scenario: I can't create ContentType when it's code is already used
    Given exists ContentType named "Content type", with code "content_type", with type "node"
    When I creates ContentType named "Duplicated Content type", with code "content_type", with type "node"
    Then new ContentType should not be created

  Scenario: I can create ContentType
    When I creates ContentType named "Content type", with code "content_type", with type "node"
    Then new ContentType should be created
