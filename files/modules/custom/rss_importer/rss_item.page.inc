<?php

/**
 * @file
 * Contains rss_item.page.inc.
 *
 * Page callback for Rss Item entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Rss Item templates.
 *
 * Default template: rss-item.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_rss_item(array &$variables) {
  // Fetch RssItem Entity Object.
  $rss_item = $variables['elements']['#rss_item'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
