<?php

namespace Drupal\etss2_business_info\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the BusinessInfo entity.
 *
 * @ContentEntityType(
 *   id = "business_info",
 *   label = @Translation("Business Info"),
 *   base_table = "business_info",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "business_name"
 *   },
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "add" = "Drupal\Core\Entity\ContentEntityForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *   },
 *   links = {
 *     "canonical" = "/business_info/{business_info}",
 *     "add-form" = "/business_info/add",
 *     "edit-form" = "/business_info/{business_info}/edit",
 *     "delete-form" = "/business_info/{business_info}/delete",
 *   }
 * )
 */
class BusinessInfo extends ContentEntityBase {

  /**
   * Defines the base fields for the entity.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Business Name.
    $fields['business_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Business Name'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ]);

    // ABN.
    $fields['abn'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ABN'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 14,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ]);

    // ACN.
    $fields['acn'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ACN'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 11,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ]);

    // Business Address.
    $fields['business_address'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Business Address'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ]);

    // Business Phone.
    $fields['business_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Business Phone'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 12,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 4,
      ]);

    // Business Email.
    $fields['business_email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Business Email'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'email_default',
        'weight' => 5,
      ]);

    // Operational Hours.
    $fields['operational_hours'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Operational Hours'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 20,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 6,
      ]);

    // Help Portal URL.
    $fields['help_portal_url'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Help Portal URL'))
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'uri_default',
        'weight' => 7,
      ]);

    // Customer Portal URL.
    $fields['customer_portal_url'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Customer Portal URL'))
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'uri_default',
        'weight' => 8,
      ]);

    // Request Callback URL.
    $fields['request_callback_url'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Request Callback URL'))
      ->setRequired(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'uri_default',
        'weight' => 9,
      ]);

    return $fields;
  }
}
