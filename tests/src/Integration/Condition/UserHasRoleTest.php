<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Condition\UserHasRoleTest.
 */

namespace Drupal\Tests\rules\Integration\Condition;

use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\Condition\UserHasRole
 * @group rules_conditions
 */
class UserHasRoleTest extends RulesEntityIntegrationTestBase {

  /**
   * The condition that is being tested.
   *
   * @var \Drupal\rules\Core\RulesConditionInterface
   */
  protected $condition;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->enableModule('user');
    $this->condition = $this->conditionManager->createInstance('rules_user_has_role');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary() {
    $this->assertEquals('User has role(s)', $this->condition->summary());
  }

  /**
   * Tests evaluating the condition.
   *
   * @covers ::evaluate
   */
  public function testConditionEvaluation() {
    // Set-up a mock object with roles 'authenticated' and 'editor', but not
    // 'administrator'.
    $account = $this->getMock('Drupal\user\UserInterface');
    $account->expects($this->exactly(7))
      ->method('getRoles')
      ->will($this->returnValue(['authenticated', 'editor']));

    $this->condition->setContextValue('user', $account);

    $authenticated = $this->getMockRole('authenticated');
    $editor = $this->getMockRole('editor');
    $administrator = $this->getMockRole('administrator');

    $this->condition->setContextValue('user', $account);

    // First test the default AND condition with both roles the user has.
    $this->condition->setContextValue('roles', [$authenticated, $editor]);
    $this->assertTrue($this->condition->evaluate());

    // User doesn't have the administrator role, this should fail.
    $this->condition->setContextValue('roles', [$authenticated, $administrator]);
    $this->assertFalse($this->condition->evaluate());

    // Only one role, should succeed.
    $this->condition->setContextValue('roles', [$authenticated]);
    $this->assertTrue($this->condition->evaluate());

    // A role the user doesn't have.
    $this->condition->setContextValue('roles', [$administrator]);
    $this->assertFalse($this->condition->evaluate());

    // Only one role, the user has with OR condition, should succeed.
    $this->condition->setContextValue('roles', [$authenticated]);
    $this->condition->setContextValue('operation', 'OR');
    $this->assertTrue($this->condition->evaluate());

    // User doesn't have the administrator role, but has the authenticated,
    // should succeed.
    $this->condition->setContextValue('roles', [$authenticated, $administrator]);
    $this->condition->setContextValue('operation', 'OR');
    $this->assertTrue($this->condition->evaluate());

    // User doesn't have the administrator role. This should fail.
    $this->condition->setContextValue('roles', [$administrator]);
    $this->condition->setContextValue('operation', 'OR');
    $this->assertFalse($this->condition->evaluate());
  }

  /**
   * Creates a mocked user role.
   *
   * @param string $id
   *   The machine-readable name of the mocked role.
   *
   * @return \PHPUnit_Framework_MockObject_MockBuilder|\Drupal\user\RoleInterface
   *   The mocked role.
   */
  protected function getMockRole($id) {
    $role = $this->getMockBuilder('Drupal\user\Entity\Role')
      ->disableOriginalConstructor()
      ->setMethods(['id'])
      ->getMock();

    $role->expects($this->any())
      ->method('id')
      ->will($this->returnValue($id));

    return $role;
  }

}
