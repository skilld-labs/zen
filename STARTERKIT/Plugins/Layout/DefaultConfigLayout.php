<?php

namespace Drupal\Starterkit\Plugin\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;

class DefaultConfigLayout extends LayoutDefault implements PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'class' => '',
    ];
  }

  public function build(array $regions) {
    $build = parent::build($regions);
    if (!empty($this->configuration['class'])) {
      $build['#attributes']['class'][] = $this->configuration['class'];
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['class'] = [
      '#type' => 'textfield',
      '#title' => 'Extra Classes',
      '#default_value' => $this->configuration['class'],
    ];
    $form['media'] = [
      '#title' => $this->t('Background Media'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#selection_handler' => 'default',
      '#selection_settings' => [
        'target_bundles' => ['image', 'video'],
      ],
    ];
    if (!empty($this->configuration['media'])) {
      $form['media']['#default_value'] = \Drupal::entityTypeManager()->getStorage('media')->load($this->configuration['media']);
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['class'] = $form_state->getValue('class');
    $this->configuration['media'] = $form_state->getValue('media');
  }

}
