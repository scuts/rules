<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Action\DataConvert.
 */

namespace Drupal\rules\Plugin\Action;

use Drupal\rules\Engine\RulesActionBase;

/**
 * @Action(
 *   id = "rules_data_convert",
 *   label = @Translation("Convert data"),
 *   category = @Translation("Data"),
 *   context = {
 *     "value" = @ContextDefinition("any",
 *       label = @Translation("Value")
 *     ),
 *     "target_type" = @ContextDefinition("any",
 *       label = @Translation("Target type")
 *     ),
 *     "rounding_behavior" = @ContextDefinition("string",
 *       label = @Translation("Rounding behavior"),
 *       required = false
 *     )
 *   },
 *   provides = {
 *     "conversion_result" = @ContextDefinition("any",
 *        label = @Translation("Conversion result")
 *      )
 *   }
 * )
 */
class DataConvert extends RulesActionBase {

  /**
   * Executes the plugin.
   */
  public function execute() {
    $value = $this->getContextValue('value');
    $target_type = $this->getContextValue('target_type');
    $rounding_behavior = $this->getContextValue('rounding_behavior');

    // First apply the rounding behavior if given.
    if (!empty($rounding_behavior)) {
      switch ($rounding_behavior) {
        case 'up':
          $value = ceil($value);
          break;
        case 'down':
          $value = floor($value);
          break;
        default:
        case 'round':
          $value = round($value);
          break;
      }
    }

    // Avoid undefined variable notice.
    $result = NULL;

    switch ($target_type) {
      case 'decimal':
        $result = floatval($value);
        break;
      case 'integer':
        $result = intval($value);
        break;
      case 'text':
        $result = strval($value);
        break;
    }

    $this->setProvidedValue('conversion_result', $result);
  }
}
