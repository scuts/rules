<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\RulesConditionContainerInterface.
 */

namespace Drupal\rules\Engine;

/**
 * Contains condition expressions.
 */
interface RulesConditionContainerInterface extends RulesExpressionConditionInterface, RulesExpressionContainerInterface {

  /**
   * Creates a condition expression and adds it to the container.
   *
   * @param string $condition_id
   *   The condition plugin id.
   * @param array $configuration
   *   (optional) The configuration for the specified plugin.
   *
   * @return \Drupal\rules\Core\RulesConditionInterface
   *   The created condition.
   */
  public function addCondition($condition_id, $configuration = NULL);
}
