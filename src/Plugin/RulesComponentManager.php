<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\RulesExpressionPluginManager.
 */

namespace Drupal\rules\Plugin;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Plugin manager for all Rules components.
 *
 * @see \Drupal\rules\Engine\RulesExpressionInterface
 */
class RulesComponentManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, ModuleHandlerInterface $module_handler, $plugin_definition_annotation_name = 'Drupal\rules\Annotation\RulesComponent') {
    $this->alterInfo('rules_component');
    parent::__construct('Plugin/RulesComponent', $namespaces, $module_handler, 'Drupal\rules\Engine\RulesExpressionInterface', $plugin_definition_annotation_name);
  }

}
