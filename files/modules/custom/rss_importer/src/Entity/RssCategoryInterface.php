<?php

namespace Drupal\rss_importer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Rss category entities.
 */
interface RssCategoryInterface extends ConfigEntityInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Implement function to return all categories as a options list.
   * @return mixed
   */
  public static function buildCategoryOptions();
}
