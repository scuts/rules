<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Action\VariableAddTest.
 */

namespace Drupal\Tests\rules\Integration\Action;

use Drupal\Tests\rules\Integration\RulesIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\Action\VariableAdd
 * @group rules_action
 */
class VariableAddTest extends RulesIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Test the action execution.
   *
   * @covers ::execute
   */
  public function testExecute() {
    $variable = $this->randomMachineName();

    /** @var \Drupal\rules\Plugin\Action\VariableAdd $action */
    $action = $this->actionManager->createInstance('rules_variable_add');
    $action->setContextValue('value', $variable);
    $action->execute();

    $result = $action->getProvided('variable_added');
    $this->assertEquals($variable, $result->getContextValue());
  }
}
