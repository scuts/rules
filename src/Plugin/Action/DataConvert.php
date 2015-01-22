<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Action\DataConvert.
 */

namespace Drupal\rules\Plugin\Action;

use Drupal\rules\Engine\RulesActionBase;
use Drupal\Component\Utility\String;
use Drupal\rules\Plugin\Exception\RulesPluginException;

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

    if (!is_numeric($value)) {
      throw new RulesPluginException($this->t('The given context value is not numeric.'));
    }

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
        case 'round':
          $value = round($value);
          break;
        default:
          throw new RulesPluginException(String::format('Unknown rounding behavior: @rounding_behavior', [
            '@rounding_behavior' => $rounding_behavior,
          ]));
      }
    }

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
      default:
        throw new RulesPluginException(String::format('Unknown target type: @type', [
          '@type' => $target_type,
        ]));
    }

    $this->setProvidedValue('conversion_result', $result);
  }
}
