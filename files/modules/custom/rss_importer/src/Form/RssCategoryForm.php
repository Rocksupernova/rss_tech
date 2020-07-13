<?php

namespace Drupal\rss_importer\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RssCategoryForm.
 */
class RssCategoryForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $rss_category = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $rss_category->label(),
      '#description' => $this->t("Label for the Rss category."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $rss_category->id(),
      '#machine_name' => [
        'exists' => '\Drupal\rss_importer\Entity\RssCategory::load',
      ],
      '#disabled' => !$rss_category->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $rss_category = $this->entity;
    $status = $rss_category->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Rss category.', [
          '%label' => $rss_category->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Rss category.', [
          '%label' => $rss_category->label(),
        ]));
    }
    $form_state->setRedirectUrl($rss_category->toUrl('collection'));
  }

}
