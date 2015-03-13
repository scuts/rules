<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Integration\Condition\UserHasEntityFieldAccessTest.
 */

namespace Drupal\Tests\rules\Integration\Condition;

use Drupal\Core\Language\Language;
use Drupal\Tests\rules\Integration\RulesEntityIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\Condition\UserHasEntityFieldAccess
 * @group rules_conditions
 */
class UserHasEntityFieldAccessTest extends RulesEntityIntegrationTestBase {

  /**
   * The condition to be tested.
   *
   * @var \Drupal\rules\Core\RulesConditionInterface
   */
  protected $condition;

  /**
   * The mocked entity access handler.
   *
   * @var \PHPUnit_Framework_MockObject_MockObject|\Drupal\Core\Entity\EntityAccessControlHandlerInterface
   */
  protected $entityAccess;

  /**
   * The mocked entity manager.
   *
   * @var \PHPUnit_Framework_MockObject_MockObject|\Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->enableModule('user');
    $this->condition = $this->conditionManager->createInstance('rules_entity_field_access');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary() {
    $this->assertEquals('User has access to field on entity', $this->condition->summary());
  }

  /**
   * Tests evaluating the condition.
   *
   * @covers ::evaluate
   */
  public function testConditionEvaluation() {
    $account = $this->getMock('Drupal\user\UserInterface');
    $entity = $this->getMock('Drupal\Core\Entity\ContentEntityInterface');
    $items = $this->getMock('Drupal\Core\Field\FieldItemListInterface');

    $entity->expects($this->any())
      ->method('getEntityTypeId')
      ->will($this->returnValue('user'));

    $entity->expects($this->exactly(3))
      ->method('hasField')
      ->with('potato-field')
      ->will($this->returnValue(TRUE));

    $definition = $this->getMock('Drupal\Core\Field\FieldDefinitionInterface');
    $entity->expects($this->exactly(2))
      ->method('getFieldDefinition')
      ->with('potato-field')
      ->will($this->returnValue($definition));

    $entity->expects($this->exactly(2))
      ->method('get')
      ->with('potato-field')
      ->will($this->returnValue($items));

    $this->condition->setContextValue('entity', $entity)
      ->setContextValue('field', 'potato-field')
      ->setContextValue('user', $account);

    $this->entityAccess->expects($this->exactly(3))
      ->method('access')
      ->will($this->returnValueMap([
        [$entity, 'view', Language::LANGCODE_DEFAULT, $account, FALSE, TRUE],
        [$entity, 'edit', Language::LANGCODE_DEFAULT, $account, FALSE, TRUE],
        [$entity, 'delete', Language::LANGCODE_DEFAULT, $account, FALSE, FALSE],
      ]));

    $this->entityAccess->expects($this->exactly(2))
      ->method('fieldAccess')
      ->will($this->returnValueMap([
        ['view', $definition, $account, $items, FALSE, TRUE],
        ['edit', $definition, $account, $items, FALSE, FALSE],
      ]));

    // Test with 'view', 'edit' and 'delete'. Both 'view' and 'edit' will have
    // general entity access, but the 'potato-field' should deny access for the
    // 'edit' operation. Hence, 'edit' and 'delete' should return FALSE.
    $this->condition->setContextValue('operation', 'view');
    $this->assertTrue($this->condition->evaluate());
    $this->condition->setContextValue('operation', 'edit');
    $this->assertFalse($this->condition->evaluate());
    $this->condition->setContextValue('operation', 'delete');
    $this->assertFalse($this->condition->evaluate());
  }
}
