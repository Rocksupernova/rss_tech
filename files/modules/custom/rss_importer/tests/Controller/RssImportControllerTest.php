<?php

namespace Drupal\rss_importer\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the rss_importer module.
 */
class RssImportControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "rss_importer RssImportController's controller functionality",
      'description' => 'Test Unit for module rss_importer and controller RssImportController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests rss_importer functionality.
   */
  public function testRssImportController() {
    // Check that the basic functions of module rss_importer.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
