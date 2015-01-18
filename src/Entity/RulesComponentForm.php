<?php

/**
 * @file
 * Contains \Drupal\action\ActionAddForm.
 */

namespace Drupal\rules\Entity;

use Drupal\rules\Plugin\RulesComponentManager;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for action add forms.
 */
class RulesComponentForm extends EntityForm {

  protected $storage = NULL;

  protected $componentManager = NULL;

  /**
   * Constructs a new ActionAddForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The rules_component storage.
   * @param \Drupal\rules\Plugin\RulesComponentManager $component_manager
   *   The rules component manager.
   */
  public function __construct(EntityStorageInterface $storage, RulesComponentManager $component_manager) {
    $this->storage = $storage;
    $this->componentManager = $component_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('rules_component'),
      $container->get('plugin.manager.rules_component')
    );
  }

  public function form($form, FormStateInterface $form_state) {
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => '',
      '#required' => TRUE,
    );

    // @todo enter a real tag field here.
    $form['tag'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Tag'),
      '#default_value' => '',
      '#description' => t('Enter a tag here'),
      '#required' => TRUE,
    );


    $form['id'] = array(
      '#type' => 'machine_name',
      '#description' => t('A unique machine-readable name. Can only contain lowercase letters, numbers, and underscores.'),
      '#disabled' => !$this->entity->isNew(),
      '#default_value' => $this->entity->id(),
      '#machine_name' => array(
        'exists' => array($this, 'exists'),
        'replace_pattern' =>'([^a-z0-9_]+)|(^custom$)',
        'error' => $this->t('The machine-readable name must be unique, and can only contain lowercase letters, numbers, and underscores. Additionally, it can not be the reserved word "custom".'),
      ),
    );

    $form['description'] = array(
      '#type' => 'textarea',
      '#default_value' => '',
      '#description' => t('Enter a description for this rule component.'),
      '#title' => t('Description'),
    );


    return parent::form($form, $form_state);
  }
}
