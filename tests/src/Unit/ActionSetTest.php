<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Unit\ActionSetTest.
 */

namespace Drupal\Tests\rules\Unit;

use Drupal\rules\Plugin\RulesExpression\ActionSet;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesExpression\ActionSet
 * @group rules
 */
class ActionSetTest extends RulesUnitTestBase {

  /**
   * The action set being tested.
   *
   * @var \Drupal\rules\Plugin\RulesExpression\ActionSet
   */
  protected $actionSet;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->actionSet = new ActionSet([], '', [], $this->expressionManager);
  }

  /**
   * Tests that an action in the set fires.
   */
  public function testActionExecution() {
    // The method on the test action must be called once.
    $this->testAction->expects($this->once())
      ->method('executeWithState');

    $this->actionSet->addExpressionObject($this->testAction)->execute();
  }

  /**
   * Tests that two actions in the set fire both.
   */
  public function testTwoActionExecution() {
    // The method on the test action must be called twice.
    $this->testAction->expects($this->exactly(2))
      ->method('executeWithState');

    $this->actionSet->addExpressionObject($this->testAction)
      ->addExpressionObject($this->testAction)
      ->execute();
  }

  /**
   * Tests that nested action sets work.
   */
  public function testNestedActionExecution() {
    // The method on the test action must be called twice.
    $this->testAction->expects($this->exactly(2))
      ->method('executeWithState');

    $inner = $this->getMockActionSet()
      ->addExpressionObject($this->testAction);

    $this->actionSet->addExpressionObject($this->testAction)
      ->addExpressionObject($inner)
      ->execute();
  }

}
