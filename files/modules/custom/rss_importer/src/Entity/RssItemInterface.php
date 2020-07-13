<?php

namespace Drupal\rss_importer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Rss Item entities.
 *
 * @ingroup rss_importer
 */
interface RssItemInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Rss Item name.
   *
   * @return string
   *   Name of the Rss Item.
   */
  public function getName();

  /**
   * Sets the Rss Item name.
   *
   * @param string $name
   *   The Rss Item name.
   *
   * @return \Drupal\rss_importer\Entity\RssItemInterface
   *   The called Rss Item entity.
   */
  public function setName($name);

  /**
   * Gets the Rss Item creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Rss Item.
   */
  public function getCreatedTime();

  /**
   * Sets the Rss Item creation timestamp.
   *
   * @param int $timestamp
   *   The Rss Item creation timestamp.
   *
   * @return \Drupal\rss_importer\Entity\RssItemInterface
   *   The called Rss Item entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Rss Item description.
   * @return string
   */
  public function getDescription();

  /**
   * Gets the Rss Item url to original content.
   * @return string
   */
  public function getLink();

  /**
   * Gets the Rss Item image url.
   * @return string
   */
  public function getImageUrl();

  /**
   * Gets the Rss Item category id.
   * @return string
   */
  public function getCategoryId();

  /**
   * Gets the Rss Item category label.
   * @return string
   */
  public function getCategoryLabel();

  /**
   * Return the original publication date of the Rss Item.
   * @param $format
   * @param $asDateObject
   * @return mixed
   */
  public function getPublicationDate($format, $asDateObject);
}
