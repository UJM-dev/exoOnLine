#language:en
Feature: First test 
  Checking QCM Question

@mink:sahi
Scenario: Open page and check it
    Given I am on "/all/exercise/"
    And Given I am authenticated
    Given I am on "/admin/question/"
    And Given I createQuestion
   
    When I select "QCM" from "menu_type_question"
    Then the response should contain "Question creation"

    And Given I verifyTable

    And Given I createNewQ

    And Given I passeTheQ

    And Given I verifyScore
    