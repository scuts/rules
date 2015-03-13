<?php

/**
 * @file
 * Contains \Drupal\rules\Engine\RulesConditionContainer.
 */

namespace Drupal\rules\Engine;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\rules\Exception\InvalidExpressionException;
use Drupal\rules\Engine\RulesExpressionPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Container for conditions.
 */
abstract class RulesConditionContainer extends RulesConditionBase implements RulesConditionContainerInterface, ContainerFactoryPluginInterface {

  use RulesExpressionTrait;

  /**
   * List of conditions that are evaluated.
   *
   * @var \Drupal\rules\Core\RulesConditionInterface[]
   */
  protected $conditions = [];

  /**
   * Constructs a new class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\rules\Engine\RulesExpressionPluginManager $expression_manager
   *   The rules expression plugin manager.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, RulesExpressionPluginManager $expression_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->expressionManager = $expression_manager;

    $configuration += ['conditions' => []];
    foreach ($configuration['conditions'] as $condition_config) {
      $condition = $this->expressionManager->createInstance($condition_config['id'], $condition_config);
      $this->addExpressionObject($condition);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.rules_expression')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function addExpressionObject(RulesExpressionInterface $expression) {
    if (!$expression instanceof RulesExpressionConditionInterface) {
      throw new InvalidExpressionException();
    }
    $this->conditions[] = $expression;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addExpression($plugin_id, $configuration = NULL) {
    return $this->addExpressionObject(
      $this->expressionManager->createInstance($plugin_id, $configuration ?: [])
    );
  }

  /**
   * {@inheritdoc}
   */
  public function addCondition($condition_id, $configuration = NULL) {
    return $this->addExpressionObject(
      $this->expressionManager
        ->createCondition($condition_id)
        ->setConfiguration($configuration ?: [])
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {

  }

  /**
   * {@inheritdoc}
   */
  public function execute() {
    if (isset($this->executableManager)) {
      return $this->executableManager->execute($this);
    }
    $result = $this->evaluate();
    return $this->isNegated() ? !$result : $result;
  }

  /**
   * {@inheritdoc}
   */
  public function negate($negate = TRUE) {
    $this->configuration['negate'] = $negate;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // @todo: Move to and implement at inheriting classes.
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    $configuration = parent::getConfiguration();
    // We need to update the configuration in case conditions have been added or
    // changed.
    $configuration['conditions'] = [];
    foreach ($this->conditions as $condition) {
      $configuration['conditions'][] = $condition->getConfiguration();
    }
    return $configuration;
  }

}
