<?php

/**
 * @file
 * Contains \Drupal\rules\Entity\RulesComponentDeleteForm.
 */

namespace Drupal\rules\Entity;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds a form to delete an action.
 */
class RulesComponentDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the rules component %label?', array('%label' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.rules_component.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();

    $this->logger('user')->notice('Deleted rules component %label)', array('%label' => $this->entity->label()));
    drupal_set_message($this->t('Rules component %label was deleted', array('%label' => $this->entity->label())));

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
