<?php

namespace Drupal\etss2_business_info\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Asset\LibraryDiscoveryInterface;

/**
 * Class for adding/editing business info.
 */
class BusinessInfoForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'business_info_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['etss2_business_info.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Attach the library to the form.
    $form['#attached']['library'][] = 'etss2_business_info/business_info_form';

    $config = $this->config('etss2_business_info.settings');

    // Business Name
    $form['business_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Business Name'),
      '#default_value' => $config->get('business_name'),
      '#required' => TRUE,
      '#maxlength' => 255,
      '#description' => $this->t('Enter the business name. Only alphanumeric characters, ".", and "&" are allowed.'),
      '#pattern' => '^[A-Za-z0-9.& ]+$', // Regular expression for validation.
    ];

    // ABN
    $form['abn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ABN'),
      '#default_value' => $config->get('abn'),
      '#required' => TRUE,
      '#maxlength' => 14,
      '#description' => $this->t('Enter the ABN in the format "11 222 333 444".'),
      '#pattern' => '^\d{2}\s\d{3}\s\d{3}\s\d{3}$',
    ];

    // ACN
    $form['acn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ACN'),
      '#default_value' => $config->get('acn'),
      '#required' => TRUE,
      '#maxlength' => 11,
      '#description' => $this->t('Enter the ACN in the format "111 222 333".'),
      '#pattern' => '^\d{3}\s\d{3}\s\d{3}$',
    ];

    // Business Address
    $form['business_address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Business Address'),
      '#default_value' => $config->get('business_address'),
      '#required' => TRUE,
      '#maxlength' => 255,
      '#description' => $this->t('Enter the business address. Only alphanumeric characters, "#", and "," are allowed.'),
      '#pattern' => '^[A-Za-z0-9,# ]+$',
    ];

    // Business Phone
    $form['business_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Business Phone'),
      '#default_value' => $config->get('business_phone'),
      '#required' => TRUE,
      '#maxlength' => 12,
      '#description' => $this->t('Enter the phone number in the format "1300 456 789".'),
      '#pattern' => '^\d{4}\s\d{3}\s\d{3}$',
    ];

    // Business Email
    $form['business_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Business Email'),
      '#default_value' => $config->get('business_email'),
      '#required' => TRUE,
      '#description' => $this->t('Enter a valid business email address in the format john.doe@example.com.'),
    ];

    // Operational Hours
    $form['operational_hours'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Operational Hours'),
      '#default_value' => $config->get('operational_hours'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#description' => $this->t('Enter operational hours in the format "9:00AM-6:00PM".'),
      '#pattern' => '^[0-9:AMP\-]+$',
    ];

    // Help Portal URL
    $form['help_portal_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Help Portal'),
      '#default_value' => $config->get('help_portal_url'),
      '#required' => FALSE,
      '#description' => $this->t('Enter the help portal URL.'),
    ];

    // Customer Portal URL
    $form['customer_portal_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Customer Portal'),
      '#default_value' => $config->get('customer_portal_url'),
      '#required' => FALSE,
      '#description' => $this->t('Enter the customer portal URL.'),
    ];

    // Request Callback URL
    $form['request_callback_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Request Callback'),
      '#default_value' => $config->get('request_callback_url'),
      '#required' => FALSE,
      '#description' => $this->t('Enter the request callback URL.'),
    ];

    return parent::buildForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Custom validation for ABN.
    if (!preg_match('/^\d{2} \d{3} \d{3} \d{3}$/', $form_state->getValue('abn'))) {
      $form_state->setErrorByName('abn', $this->t('ABN must be in the format "11 222 333 444".'));
    }

    // Custom validation for ACN.
    if (!preg_match('/^\d{3} \d{3} \d{3}$/', $form_state->getValue('acn'))) {
      $form_state->setErrorByName('acn', $this->t('ACN must be in the format "111 222 333".'));
    }

    // Custom validation for Business Phone.
    if (!preg_match('/^\d{4} \d{3} \d{3}$/', $form_state->getValue('business_phone'))) {
      $form_state->setErrorByName('business_phone', $this->t('Business Phone must be in the format "1300 456 789".'));
    }

    // Additional email validation.
    if (!filter_var($form_state->getValue('business_email'), FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('business_email', $this->t('The provided email address is not valid.'));
    }

    // Operational Hours validation.
    if (!preg_match('/^\d{1,2}:\d{2}(AM|PM)-\d{1,2}:\d{2}(AM|PM)$/', $form_state->getValue('operational_hours'))) {
      $form_state->setErrorByName('operational_hours', $this->t('Operational Hours must be in the format "9:00AM-6:00PM".'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('etss2_business_info.settings')
      ->set('business_name', $form_state->getValue('business_name'))
      ->set('abn', $form_state->getValue('abn'))
      ->set('acn', $form_state->getValue('acn'))
      ->set('business_address', $form_state->getValue('business_address'))
      ->set('business_phone', $form_state->getValue('business_phone'))
      ->set('business_email', $form_state->getValue('business_email'))
      ->set('operational_hours', $form_state->getValue('operational_hours'))
      ->set('help_portal_url', $form_state->getValue('help_portal_url'))
      ->set('customer_portal_url', $form_state->getValue('customer_portal_url'))
      ->set('request_callback_url', $form_state->getValue('request_callback_url'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
