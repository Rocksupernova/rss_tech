<?php

namespace Drupal\rss_importer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\rss_importer\Entity\RssItem;
use Drupal\rss_importer\Entity\RssItemInterface;

/**
 * Class RssOverviewController.
 */
class RssOverviewController extends ControllerBase {

  /**
   * Build the detail page of a Rss Item
   * @param RssItem $rss_item
   */
  public function rssItemDetail(RssItem $rss_item) {
    return [
      '#theme' => 'rss_item',
      '#rss_item' => $rss_item,
      '#cache' => [
        'max-age' => 0,
      ]
    ];
  }

  /**
   * @param RssItemInterface $rss_item
   * @return string|null
   */
  public function rssItemTitle(RssItemInterface $rss_item) {
    return $rss_item->label();
  }

  /**
   * Cron run will execute after 15 min
   */
  public static function rssCronRunImport() {
    $previousRun = \Drupal::state()->get('rss_importer.cron_run', 0);
    $time = time();
    $nextRun = strtotime('-15 minutes');

    # Run cron every 15 minutes
    # Check if time is later then 08:00 o'clock in the morning - ((int)date('H')) > 8 - date needs to be parsed to int
    if ($previousRun < $nextRun && ((int)date('H')) > 8) {
      \Drupal::logger('rss_importer.cron_run')->info('Cron: RSS Import start');
      $rssImportController = new RssImportController();
      $rssImportController->importRssFeed(true);

      # Log when cron is completed
      \Drupal::logger('rss_importer.cron_run')->info('Cron: RSS Import done');
      # Update cron run
      \Drupal::state()->set('rss_importer.cron_run', $time);
    }
  }
}
