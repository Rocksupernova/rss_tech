<?php

namespace Drupal\rss_importer\Entity;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\node\Plugin\views\row\Rss;
use Drupal\Core\Url;

/**
 * Defines the Rss Item entity.
 *
 * @ingroup rss_importer
 *
 * @ContentEntityType(
 *   id = "rss_item",
 *   label = @Translation("Rss Item"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\rss_importer\RssItemListBuilder",
 *     "views_data" = "Drupal\rss_importer\Entity\RssItemViewsData",
 *     "translation" = "Drupal\rss_importer\RssItemTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\rss_importer\Form\RssItemForm",
 *       "add" = "Drupal\rss_importer\Form\RssItemForm",
 *       "edit" = "Drupal\rss_importer\Form\RssItemForm",
 *       "delete" = "Drupal\rss_importer\Form\RssItemDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\rss_importer\RssItemHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\rss_importer\RssItemAccessControlHandler",
 *   },
 *   base_table = "rss_item",
 *   data_table = "rss_item_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer rss item entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/rss_item/{rss_item}",
 *     "add-form" = "/admin/structure/rss_item/add",
 *     "edit-form" = "/admin/structure/rss_item/{rss_item}/edit",
 *     "delete-form" = "/admin/structure/rss_item/{rss_item}/delete",
 *     "collection" = "/admin/structure/rss_item",
 *   },
 *   field_ui_base_route = "rss_item.settings"
 * )
 */
class RssItem extends ContentEntityBase implements RssItemInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->get('description')->value;
  }

  /**
   * @return string
   */
  public function getLink() {
    return $this->get('link')->value;
  }

  /**
   * @return string
   */
  public function getImageUrl() {
    return $this->get('image_url')->value;
  }

  /**
   * @return string
   */
  public function getCategoryId() {
    return $this->get('category')->value;
  }

  /**
   * @return string
   */
  public function getCategoryLabel() {
    $cid = $this->get('category')->value;

    /** @var RssCategory $category */
    $category = RssCategory::load($cid);
    return $category ? $category->label() : '';
  }

  /**
   * Get the publication date and give it a nice format
   * Or return the DateObject
   * @param string $format
   * @param bool $asDateObject
   * @return mixed|string
   */
  public function getPublicationDate($format = 'd-m-Y', $asDateObject = false) {
    /** @var DrupalDateTime $date */
    $date = $this->get('pub_date')->date;
    return $asDateObject ? $date : $date->format($format);
  }

  public function getDetailUrl() {
    $url = Url::fromRoute('rss_importer.rss_item_detail', ['rss_item'=> $this->id()]);
    return $url->setAbsolute()->toString();
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Rss Item.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDescription(t('The description of the Rss Item.'))
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'text_format',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);

    $fields['link'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Item link'))
      ->setDescription(t('The link to the original content of the Rss Item.'))
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['image_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Image url'))
      ->setDescription(t('The image url of the Rss Item.'))
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['category'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Category'))
      ->setDescription(t('The category of the Rss Item.'))
      ->setSettings([
        'allowed_values' => RssCategory::buildCategoryOptions()
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'list_string',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['pub_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Publication date'))
      ->setDescription(t('The Date the Rss Item, was published.'))
      ->setSettings([
        'datetime_type' => 'date'
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['guid'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Guid'))
      ->setDescription(t('The Guid of the Rss Item.'))
      ->setReadOnly(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Rss Item is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
