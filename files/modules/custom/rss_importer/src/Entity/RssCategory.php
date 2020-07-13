<?php

namespace Drupal\rss_importer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Rss category entity.
 *
 * @ConfigEntityType(
 *   id = "rss_category",
 *   label = @Translation("Rss category"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rss_importer\RssCategoryListBuilder",
 *     "form" = {
 *       "add" = "Drupal\rss_importer\Form\RssCategoryForm",
 *       "edit" = "Drupal\rss_importer\Form\RssCategoryForm",
 *       "delete" = "Drupal\rss_importer\Form\RssCategoryDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\rss_importer\RssCategoryHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "rss_category",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/rss_category/{rss_category}",
 *     "add-form" = "/admin/structure/rss_category/add",
 *     "edit-form" = "/admin/structure/rss_category/{rss_category}/edit",
 *     "delete-form" = "/admin/structure/rss_category/{rss_category}/delete",
 *     "collection" = "/admin/structure/rss_category"
 *   }
 * )
 */
class RssCategory extends ConfigEntityBase implements RssCategoryInterface {

  /**
   * The Rss category ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Rss category label.
   *
   * @var string
   */
  protected $label;

  /**
   * @return array
   */
  public static function buildCategoryOptions() {
    $options = [];
    # Get all the category ids
    $cIds = \Drupal::entityQuery('rss_category')
      ->execute();
    $categories = $cIds ? RssCategory::loadMultiple($cIds) : [];
    foreach ($categories as $category) {
      $options[$category->id()] = $category->label();
    }
    return $options;
  }
}
