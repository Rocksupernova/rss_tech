<?php

namespace Drupal\rss_importer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Rss Item entities.
 *
 * @ingroup rss_importer
 */
class RssItemListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Rss Item ID');
    $header['name'] = $this->t('Name');
    $header['category'] = $this->t('Category');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\rss_importer\Entity\RssItem $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.rss_item.edit_form',
      ['rss_item' => $entity->id()]
    );
    $row['category'] = $entity ? $entity->getCategoryLabel() : '';
    return $row + parent::buildRow($entity);
  }

}
