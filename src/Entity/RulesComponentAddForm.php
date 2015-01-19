<?php

/**
 * @file
 * Contains \Drupal\rules\Entity\RulesComponentAddForm.
 */

namespace Drupal\rules\Entity;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for adding rules components.
 */
class RulesComponentAddForm extends RulesComponentFormBase {

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Add new rules component');
    return $actions;
  }
}
