<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\RulesActionContainerInterface.
 */

namespace Drupal\rules\Engine;

/**
 * Contains action expressions.
 */
interface RulesActionContainerInterface extends RulesExpressionActionInterface, RulesExpressionContainerInterface {

  /**
   * Creates an action expression and adds it to the container.
   *
   * @param string $action_id
   *   The action plugin id
   * @param array $configuration
   *   (optional) The configuration for the specified plugin.
   *
   * @return $this
   */
  public function addAction($action_id, $configuration = NULL);

}
