<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\RulesExpressionInterface.
 */

namespace Drupal\rules\Engine;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Executable\ExecutableInterface;

/**
 * Defines the interface for Rules expressions.
 *
 * @see \Drupal\rules\Plugin\RulesExpressionPluginManager
 */
interface RulesExpressionInterface extends ExecutableInterface, ContextAwarePluginInterface, ConfigurablePluginInterface {

  /**
   * Execute the expression with a given Rules state.
   *
   * @param \Drupal\rules\Engine\RulesState $state
   *   The state with all the execution variables in it.
   *
   * @return null|bool
   *   The expression may return a boolean value after execution, this is used
   *   by conditions that return their evaluation result.
   *
   * @throws \Drupal\rules\Exception\RulesEvaluationException
   *   In case the Rules expression triggers errors during execution.
   */
  public function executeWithState(RulesState $state);

}
