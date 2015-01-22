<?php

/**
 * @file
 * Contains \Drupal\rules\Plugin\Exception\RulesPluginException.
 */

namespace Drupal\rules\Plugin\Exception;

use Drupal\Component\Plugin\Exception\ExceptionInterface;

/**
 * An exception class to be thrown for rules context plugin exceptions.
 */
class RulesPluginException extends \Exception implements ExceptionInterface { }
