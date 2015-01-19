<?php

/**
 * @file
 * Contains \Drupal\rules\Entity\RulesComponentEditForm.
 */

namespace Drupal\rules\Entity;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form to edit rules components.
 */
class RulesComponentEditForm extends RulesComponentFormBase {

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save rules component');
    return $actions;
  }
}
