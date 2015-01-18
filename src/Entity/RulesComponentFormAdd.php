<?php

/**
 * @file
 * Contains \Drupal\action\ActionAddForm.
 */

namespace Drupal\rules\Entity;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for rules component add forms.
 */
class RulesComponentFormAdd extends RulesComponentFormBase {

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Add new rules component search page');
    return $actions;
  }
}
