Feature: Manage images
  @createSchema
  @image
  Scenario: Create and upload a new image
    Given I am authenticated with "admin"
    When I add "Content-Type" header equal to "multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/images" with parameters:
      | key  | value       |
      | file | @haho.jpg   |
    Then the response status code should be 201
    And the response should be in JSON
    And the JSON matches expected template
    """
    {
      "@context": "/api/contexts/Image",
      "@id": "/api/images/1",
      "@type": "Image",
      "id": 1,
      "file": null,
      "url": "@string@"
    }
    """