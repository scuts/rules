<?php

/**
 * @file
 * Contains \Drupal\rules\Context\RulesContextTrait.
 */

namespace Drupal\rules\Context;

use Drupal\Component\Plugin\ContextAwarePluginInterface;
use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Component\Utility\String;
use Drupal\Core\Plugin\Context\Context;
use Drupal\rules\Exception\RulesEvaluationException;
use Drupal\rules\Engine\RulesState;

/**
 * Offers common methods for context plugin implementers.
 */
trait RulesContextTrait {

  /**
   * The data objects that are provided by this plugin.
   *
   * @var \Drupal\Component\Plugin\Context\ContextInterface[]
   */
  protected $provided;

  /**
   * The data processor plugin manager used to process context variables.
   *
   * @var \Drupal\rules\Engine\RulesDataProcessorManager
   */
  protected $processorManager;

  /**
   * @see \Drupal\rules\Context\ProvidedContextPlugininterface
   */
  public function setProvidedValue($name, $value) {
    $this->getProvided($name)->setContextValue($value);
    return $this;
  }

  /**
   * @see \Drupal\rules\Context\ProvidedContextPlugininterface
   */
  public function getProvided($name) {
    // Check for a valid context value.
    if (!isset($this->provided[$name])) {
      $this->provided[$name] = new Context($this->getProvidedDefinition($name));
    }
    return $this->provided[$name];
  }

  /**
   * @see \Drupal\rules\Context\ProvidedContextPlugininterface
   */
  public function getProvidedDefinition($name) {
    $definition = $this->getPluginDefinition();
    if (empty($definition['provides'][$name])) {
      throw new ContextException(sprintf("The %s provided context is not valid.", $name));
    }
    return $definition['provides'][$name];
  }

  /**
   * @see \Drupal\rules\Context\ProvidedContextPlugininterface
   */
  public function getProvidedDefinitions() {
    $definition = $this->getPluginDefinition();
    return !empty($definition['provides']) ? $definition['provides'] : [];
  }

  /**
   * Maps variables from rules state into the plugin context.
   *
   * @param \Drupal\Component\Plugin\ContextAwarePluginInterface $plugin
   *   The plugin that is populated with context values.
   * @param \Drupal\rules\Engine\RulesState $state
   *   The Rules state containing available variables.
   *
   * @throws \Drupal\rules\Exception\RulesEvaluationException
   *   In case a required context is missing for the plugin.
   */
  protected function mapContext(ContextAwarePluginInterface $plugin, RulesState $state) {
    $context_definitions = $plugin->getContextDefinitions();
    foreach ($context_definitions as $name => $definition) {
      $context_value = NULL;
      // First check if we can forward a context directly set on this plugin.
      try {
        $context_value = $this->getContextValue($name);
      }
      // A context exception means that there is no context with the given name,
      // so we catch it and continue with the context mapping below.
      catch (ContextException $e) {
      }

      if ($context_value) {
        $plugin->setContextValue($name, $context_value);
      }
      // Check if a data selector is configured that maps to the state.
      elseif (isset($this->configuration['context_mapping'][$name . ':select'])) {
        $typed_data = $state->applyDataSelector($this->configuration['context_mapping'][$name . ':select']);
        $plugin->setContextValue($name, $typed_data);
      }
      elseif ($definition->isRequired()) {
        throw new RulesEvaluationException(String::format('Required context @name is missing for plugin @plugin.', [
          '@name' => $name,
          '@plugin' => $plugin->getPluginId(),
        ]));
      }
    }
  }

  /**
   * Maps provided context values from the plugin to the Rules state.
   *
   * @param ProvidedContextPluginInterface $plugin
   *   The plugin where the context values are extracted.
   * @param \Drupal\rules\Engine\RulesState $state
   *   The Rules state where the context variables are added.
   */
  protected function mapProvidedContext(ProvidedContextPluginInterface $plugin, RulesState $state) {
    $provides = $plugin->getProvidedDefinitions();
    foreach ($provides as $name => $provided_definition) {
      // Avoid name collisions in the rules state: provided variables can be
      // renamed.
      if (isset($this->configuration['provides_mapping'][$name])) {
        $state->addVariable($this->configuration['provides_mapping'][$name], $plugin->getProvided($name));
      }
      else {
        $state->addVariable($name, $plugin->getProvided($name));
      }
    }
  }

  /**
   * Process data context on the plugin, usually before it gets executed.
   *
   * @param \Drupal\Component\Plugin\ContextAwarePluginInterface $plugin
   *   The plugin to process the context data on.
   */
  protected function processData(ContextAwarePluginInterface $plugin) {
    if (isset($this->configuration['processor_mapping'])) {
      foreach ($this->configuration['processor_mapping'] as $name => $settings) {
        $data_processor = $this->processorManager->createInstance($settings['plugin'], $settings['configuration']);
        $new_value = $data_processor->process($plugin->getContextValue($name));
        $plugin->setContextValue($name, $new_value);
      }
    }
  }

}
