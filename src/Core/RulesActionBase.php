<?php

/**
 * @file
 * Contains \Drupal\rules\Core\RulesActionBase.
 */

namespace Drupal\rules\Core;

use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Plugin\ContextAwarePluginBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\rules\Context\RulesContextTrait;

/**
 * Base class for rules actions.
 */
abstract class RulesActionBase extends ContextAwarePluginBase implements RulesActionInterface {

  use RulesContextTrait;

  /**
   * The plugin configuration.
   *
   * @var array
   */
  protected $configuration;

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return [
      'id' => $this->getPluginId(),
    ] + $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $objects) {
    // @todo: Remove this once it is removed from the interface.
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // Per default no context parameters will be auto saved.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    // Just deny access per default for now.
    if ($return_as_object) {
      return new AccessResultForbidden();
    }
    return FALSE;
  }

}
