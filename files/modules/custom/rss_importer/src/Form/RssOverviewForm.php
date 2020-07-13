<?php

namespace Drupal\rss_importer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\rss_importer\Entity\RssItem;

/**
 * Class RssSearchForm.
 */
class RssOverviewForm extends FormBase {

  private $title;
  private $pubDate;
  private $items;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rss_overview_form';
  }

  /**
   * RssOverviewForm constructor.
   */
  public function __construct() {
    # Make sure items search is handled before form is loaded
    $this->getItems();
  }

  /**
   * Build the items overview form that will be used as the page
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->buildSearchForm($form, $form_state);
    $this->buildOverviewForm($form, $form_state);

    # Add pagination to page
    $form['pager'] = [
      '#type' => 'pager',
      '#quantity' => 4,
      '#tags' => ["«", "‹", "", "›", "»"],
    ];

    $form['#attached'] = [
      'library' => [
        'rss_importer/rss_items_overview',
        'rss_importer/global']
    ];
    return $form;
  }

  /**
   * Build the search form that will be used to filter items
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function buildSearchForm(array &$form, FormStateInterface $form_state) {
    $form['search_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['search-form-container']
      ],
      'title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#placeholder' => t('Title'),
        '#default_value' => $this->title,
        '#required' => TRUE,
        '#wrapper_attributes' => ['class' => ['title-wrapper']]
      ],
      'pub_date' => [
        '#type' => 'date',
        '#title' => $this->t('Publication date'),
        '#default_value' => $this->pubDate,
        '#wrapper_attributes' => ['class' => ['publication-date-wrapper']]
      ],
      'submit' => [
        '#type' => 'submit',
        '#attributes' => ['class' => ['submit-button']],
        '#value' => $this->t('Search'),
      ]
    ];
    if ($this->title || $this->pubDate) {
      $form['search_container']['clear_search'] = [
        '#type' => 'link',
        '#title' => t('Clear search'),
        '#url' => Url::fromRoute('<current>'),
        '#attributes' => ['class' => ['clear-search-link']],
      ];
    }

  }

  /**
   * Build the overview grid with items
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function buildOverviewForm(array &$form, FormStateInterface $form_state) {
    $form['items_overview_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['rss-items-overview-wrapper', 'container']],
      'items_container' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['row']],
      ]
    ];

    # Get the found items
    $items = $this->items;
    /** @var RssItem[] $items */
    foreach ($items as $item) {
      $teaser = \Drupal::entityTypeManager()->getViewBuilder('rss_item')->view($item, 'teaser');
      $form['items_overview_wrapper']['items_container']['item_' . $item->id()] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['col-md-4']],
        '#markup' => render($teaser)
      ];
    }
  }

  /**
   * Get the RSS items that will be displayed
   */
  public function getItems() {
    # Check if search parameters have been given
    $request = \Drupal::request()->query;
    $this->title = $request->get('title');
    $this->pubDate = $request->get('pub_date');

    # Prepare query for search
    $query = \Drupal::entityQuery('rss_item')
      ->condition('status', 1)
      ->sort('pub_date', 'DESC');
    # Check if title parameter has been given for search
    if ($this->title) {
      $query->condition('name', $this->title, 'CONTAINS');
    }
    # Check if publication date parameter has been given for search
    if ($this->pubDate) {
      $query->condition('pub_date', $this->pubDate);
    }
    # Load found items and save in private variables
    $ids = $query->pager(9)
      ->execute();
    $this->items = $ids ? RssItem::loadMultiple($ids) : [];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    # Validation is not needed
    parent::validateForm($form, $form_state);
  }

  /**
   * Handle search form submit
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    # Set search filters
    $form_state->setRedirect('<current>', [
      'title' => $form_state->getValue('title'),
      'pub_date' => $form_state->getValue('pub_date'),
    ]);
  }

}
