<?php

/**
 * @file
 * Contains \Drupal\action\ActionAddForm.
 */

namespace Drupal\rules\Entity;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for rules components forms.
 */
class RulesComponentFormEdit extends RulesComponentFormBase {

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save rules component');
    return $actions;
  }
}
