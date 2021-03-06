<?php

namespace Drupal\rss_importer;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Rss Item entity.
 *
 * @see \Drupal\rss_importer\Entity\RssItem.
 */
class RssItemAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\rss_importer\Entity\RssItemInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished rss item entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published rss item entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit rss item entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete rss item entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add rss item entities');
  }


}
