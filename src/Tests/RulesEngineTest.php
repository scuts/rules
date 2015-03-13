<?php

/**
 * @file
 * Contains \Drupal\rules\Tests\RulesEngineTest.
 */

namespace Drupal\rules\Tests;

use Drupal\rules\Engine\RulesLog;
use Drupal\rules\Engine\RulesState;

/**
 * Test using the Rules API to create and evaluate rules.
 *
 * @group rules
 */
class RulesEngineTest extends RulesDrupalTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Clear the log from any stale entries that are bleeding over from previous
    // tests.
    $logger = RulesLog::logger();
    $logger->clear();
  }

  /**
   * Tests creating a rule and iterating over the rule elements.
   */
  public function testRuleCreation() {
    // Create an 'and' condition container and add conditions to it.
    $and = $this->expressionManager->createAnd()
      ->addCondition('rules_test_false')
      ->addExpressionObject($this->expressionManager->createCondition('rules_test_true')->negate())
      ->negate();

    // Test that the 'and' condition container evaluates to TRUE.
    $this->assertTrue($and->execute());

    // Create an 'or' condition container and add conditions to it, including
    // the previously created 'and' condition container.
    $or = $this->expressionManager->createOr()
      ->addExpressionObject($this->expressionManager->createCondition('rules_test_true')->negate())
      ->addCondition('rules_test_false')
      ->addCondition($and);

    // Test that the 'or' condition container evaluates to TRUE.
    $this->assertTrue($or->execute());

    // Create a rule and add conditions to it, including the previously created
    // 'or' condition container.
    $rule = $this->expressionManager->createRule();
    $rule->addCondition('rules_test_true')
      ->addCondition('rules_test_true')
      ->addExpressionObject($or);

    // Test that the rule's condition container evaluates to TRUE.
    $this->assertTrue($rule->getConditions()->execute());

    // Add an action to it and execute the rule.
    $rule->addAction('rules_test_log');
    $rule->execute();

    // Test that the action logged something.
    $log = RulesLog::logger()->get();
    $this->assertEqual($log[0][0], 'action called');
  }

  /**
   * Tests passing a string context to a condition.
   */
  public function testContextPassing() {
    $rule = $this->expressionManager->createRule([
      'context_definitions' => [
        'test' => [
          'type' => 'string',
          'label' => 'Test string',
        ],
      ],
    ]);

    $rule->addCondition('rules_test_string_condition', [
      'context_mapping' => ['text:select' => 'test'],
    ]);

    $rule->addAction('rules_test_log');
    $rule->setContextValue('test', 'test value');
    $rule->execute();

    // Test that the action logged something.
    $log = RulesLog::logger()->get();
    $this->assertEqual($log[0][0], 'action called');
  }

  /**
   * Tests that a condition can provide a value and another one can consume it.
   */
  public function testProvidedVariables() {
    $rule = $this->expressionManager->createRule();

    // The first condition provides a "provided_text" variable.
    $rule->addCondition('rules_test_provider');
    // The second condition consumes the variable.
    $rule->addCondition('rules_test_string_condition', [
      'context_mapping' => ['text:select' => 'provided_text'],
    ]);

    $rule->addAction('rules_test_log');
    $rule->execute();

    // Test that the action logged something.
    $log = RulesLog::logger()->get();
    $this->assertEqual($log[0][0], 'action called');
  }

  /**
   * Tests that provided variables can be renamed with configuration.
   */
  public function testRenamingOfProvidedVariables() {
    $rule = $this->expressionManager->createRule();

    // The condition provides a "provided_text" variable.
    $rule->addCondition('rules_test_provider', [
      // Expose the variable as 'newname'.
      'provides_mapping' => ['provided_text' => 'newname'],
    ]);

    $state = new RulesState();
    $rule->executeWithState($state);

    // Check that the newly named variable exists and has the provided value.
    $variable = $state->getVariable('newname');
    $this->assertEqual($variable->getContextValue(), 'test value');
  }

  /**
   * Tests that multiple actions can consume and provide context variables.
   */
  public function testActionProvidedContext() {
    $rule = $this->expressionManager->createRule();

    // The condition provides a "provided_text" variable.
    $rule->addCondition('rules_test_provider');

    // The action provides a "concatenated" variable.
    $rule->addAction('rules_test_string', [
      'context_mapping' => ['text:select' => 'provided_text'],
    ]);

    // Add the same action again which will provide a "concatenated2" variable
    // now.
    $rule->addAction('rules_test_string', [
      'context_mapping' => ['text:select' => 'concatenated'],
      'provides_mapping' => ['concatenated' => 'concatenated2'],
    ]);

    $state = new RulesState();
    $rule->executeWithState($state);

    // Check that the created variables exists and have the provided values.
    $concatenated = $state->getVariable('concatenated');
    $this->assertEqual($concatenated->getContextValue(), 'test valuetest value');
    $concatenated2 = $state->getVariable('concatenated2');
    $this->assertEqual($concatenated2->getContextValue(), 'test valuetest valuetest valuetest value');
  }

}
