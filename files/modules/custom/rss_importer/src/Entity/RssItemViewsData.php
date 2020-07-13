<?php

namespace Drupal\rss_importer\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Rss Item entities.
 */
class RssItemViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }
}
