<?php

namespace Drupal\rss_importer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Datetime\Element\Datetime;
use Drupal\node\Plugin\views\row\Rss;
use Drupal\rss_importer\Entity\RssCategory;
use Drupal\rss_importer\Entity\RssItem;

/**
 * Class RssImportController.
 */
class RssImportController extends ControllerBase {

  private $config;
  private $logs = [];

  public function __construct() {
    # Get xml url configured in the settings
    $this->config = \Drupal::config('rss_importer.rss_settings');
  }

  /**
   * Handle Rss feed item import
   * @param bool $cron
   * @return bool
   */
  public function importRssFeed($cron = false) {
    $xmlUrl = $this->config->get('url'); #'https://www.nu.nl/rss/Tech';
    $xml = simplexml_load_file($xmlUrl);
    foreach ($xml->channel->item as $item) {
      $this->handleItem($item);
    }

    # Show import logs
    if (!$cron) {
      # Clear the necessary cache to complete import/update successfully
      self::clearCaches();
      # Dump the different messages when manuel import
      dd($this->logs);
    }

    # If run by cron job
    return true;
  }

  /**
   * @param $item
   */
  public function handleItem($item) {
    # Clean the GuId and remove url format and leave only numbers
    $guId = (string)$item->guid;
    $cleanGuId = preg_replace('/[^0-9]/', '', $guId);
    # Clean publication date to DrupalDateTime format
    $pubDate = (string)$item->pubDate;
    $dateTime = new DrupalDateTime($pubDate);
    # Prepare item category
    $itemCategory = (string)$item->category;
    # Prepare clean data
    $itemData = [
      'name' => $itemTitle = (string)$item->title,
      'link' => (string)$item->link,
      'description' => (string)$item->description,
      'guid' => $cleanGuId,
      'pub_date' => $dateTime->format('Y-m-d'),
      'category' => null,
      'image_url' => (string)$item->enclosure['url']
    ];

    /** @var RssCategory $rssCategory */
    $rssCategory = $this->addCategory($itemCategory);
    /**
     * In this case the category id is a string and can be then used to
     * select the category from the dropdown list
     */
    $itemData['category'] = $rssCategory ? $rssCategory->id() : '';

    # Check if item has been previously imported
    $id = \Drupal::entityQuery('rss_item')
      ->condition('guid', $cleanGuId)
      ->condition('status', 1)
      ->execute();
    /** @var RssItem $rssItem */
    $rssItem = $id ? RssItem::load(current($id)) : null;
    $newItem = empty($rssItem);

    # Create a new Rss Item
    if ($newItem) {
      $rssItem = RssItem::create();
    }

    # Set entity data
    foreach ($itemData as $field => $value) {
      $rssItem->set($field, $value);
    }

    # Handle create or update rss item
    $message = '';
    try {
      if ($rssItem->save()) {
        if ($newItem) {
          $message = t('Created item - ' . $cleanGuId . ' - ' . $itemTitle . ' successfully!!');
        } else {
          $message = t('Updated item - ' . $cleanGuId . ' - ' . $itemTitle . ' successfully!!');
        }
        \Drupal::logger('rss_importer')->info($message);
      }
    } catch (\Exception $e) {
      $message = t('Error: item - ' . $cleanGuId . ' - ' . $itemTitle . ' - ') . $e->getMessage();
      \Drupal::logger('rss_importer')->error($message);
    }
    $this->logs[] = $message;
    return;
  }

  /**
   * Add a new category or return existing category
   * @param $categoryId
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface|int|null
   */
  public function addCategory($categoryId) {
    $id = strtolower($categoryId);
    # Check if category exists
    $cId = \Drupal::entityQuery('rss_category')
      ->condition('id', $id)
      ->execute();
    $category = $cId ? RssCategory::load(current($cId)) : null;
    if (!empty($category)) {
      # Return category item if found
      return $category;
    }
    # If category does not exist add it;
    $category = RssCategory::create([
      'id' => $id,
      'label' => $categoryId,
    ]);

    try {
      $category->save();
      $message = t('Category - ' . $categoryId . ' added successfully!!');
    } catch (\Exception $e) {
      $message = t('Error: category - ' . $categoryId . ' - ') . $e->getMessage();
      \Drupal::logger('rss_importer')->error($message);
    }

    $this->logs[] = $message;
    return $category;
  }

  /**
   * Clear the necessary caches to make sure import and updates are completed successfully
   * Implemented as static function in case this function is needed somewhere else
   */
  public static function clearCaches() {
    $caches = [
      'cache.discovery', # Make sure that in the backend the changes are updated
      'cache.config',
      'cache.entity',
      'cache.render', # Make sure that in the front-end the changes done are also updated
    ];
    # Clear render caches
    foreach ($caches as $cache) {
      \Drupal::service($cache)->invalidateAll();
    }
    return;
  }
}
