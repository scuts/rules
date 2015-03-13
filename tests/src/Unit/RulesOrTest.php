<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Unit\RulesOrTest.
 */

namespace Drupal\Tests\rules\Unit;

use Drupal\rules\Plugin\RulesExpression\RulesOr;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesExpression\RulesOr
 * @group rules
 */
class RulesOrTest extends RulesUnitTestBase {

  /**
   * The 'or' condition container being tested.
   *
   * @var \Drupal\rules\Engine\RulesConditionContainerInterface
   */
  protected $or;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->or = new RulesOr([], '', [], $this->expressionManager);
  }

  /**
   * Tests one condition.
   */
  public function testOneCondition() {
    // The method on the test condition must be called once.
    $this->trueCondition->expects($this->once())
      ->method('executeWithState');

    $this->or->addExpressionObject($this->trueCondition);
    $this->assertTrue($this->or->execute(), 'Single condition returns TRUE.');
  }

  /**
   * Tests an empty OR.
   */
  public function testEmptyOr() {
    $property = new \ReflectionProperty($this->or, 'conditions');
    $property->setAccessible(TRUE);

    $this->assertEmpty($property->getValue($this->or));
    $this->assertTrue($this->or->execute(), 'Empty OR returns TRUE.');
  }

  /**
   * Tests two true condition.
   */
  public function testTwoConditions() {
    // The method on the test condition must be called once.
    $this->trueCondition->expects($this->once())
      ->method('executeWithState');

    $this->or
      ->addExpressionObject($this->trueCondition)
      ->addExpressionObject($this->trueCondition);

    $this->assertTrue($this->or->execute(), 'Two conditions returns TRUE.');
  }

  /**
   * Tests two false conditions.
   */
  public function testTwoFalseConditions() {
    // The method on the test condition must be called once.
    $this->falseCondition->expects($this->exactly(2))
      ->method('executeWithState');

    $this->or
      ->addExpressionObject($this->falseCondition)
      ->addExpressionObject($this->falseCondition);

    $this->assertFalse($this->or->execute(), 'Two false conditions return FALSE.');
  }
}
