<?php

namespace Drupal\rss_importer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RssSettingsForm.
 */
class RssSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'rss_importer.rss_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rss_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('rss_importer.rss_settings');
    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('RSS url'),
      '#description' => $this->t('Set rss feed url'),
      '#maxlength' => 255,
      '#size' => 65,
      '#default_value' => $config->get('url'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('rss_importer.rss_settings')
      ->set('url', $form_state->getValue('url'))
      ->save();
  }

}
