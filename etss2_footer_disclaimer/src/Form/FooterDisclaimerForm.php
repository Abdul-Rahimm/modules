<?php

namespace Drupal\etss2_footer_disclaimer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\etss2_business_info\Entity\BusinessInfo;

/**
 * Class FooterDisclaimerForm.
 */
class FooterDisclaimerForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['etss2_footer_disclaimer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'etss2_footer_disclaimer_form';
  }

  /**
 *  validation function to ensure the field contains a valid URL.
 */
public function validateUrl($element, &$form_state, $form) {
  $value = $form_state->getValue($element['#parents']);

    //dont do anything if its empty
  if($value == "")
    return;
  
  // Check if the value is a valid URL.
  if (!filter_var($value, FILTER_VALIDATE_URL)) {
    // Set a validation error.
    $form_state->setError($element, $this->t('The value entered for %field must be a valid URL.', [
      '%field' => $element['#title'],
    ]));
  }
}


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    //below config stores footer disclaimer object
    $config = $this->config('etss2_footer_disclaimer.settings');

    //below config stores business info object
    $config_business_info = $this->config('etss2_business_info.settings');
    
    //all default values
    $default_disclaimer_preview           = 'Copyright © ' . date('Y') . ' ' . $config_business_info->get('business_name') . ' ACN ' . $config_business_info->get('acn');
    $default_privacy_statement            = $config->get('privacy_statement');
    $default_terms_of_use                 = $config->get('terms_of_use');
    $default_terms_of_sale                = $config->get('terms_of_sale');
    $default_trademarks                   = $config->get('trademarks');
    $default_australian_consumer_law_url  = $config->get('australian_consumer_law_url');
    
    
     //if user changes disclaimer --> then updated disclaimer should be shown. not default
    if($config->get('disclaimer_preview') != 'Copyright © ' . date('Y') . ' ' . $config_business_info->get('business_name') . ' ACN ' . $config_business_info->get('acn')){
      $default_disclaimer_preview = $config->get('disclaimer_preview');
    }

      //if user disables the footer disclaimer. clear all default values
    if($config->get('enable_disclaimer') == 0){
      $default_disclaimer_preview          ="";
      $default_privacy_statement           ="";
      $default_terms_of_use                ="";
      $default_terms_of_sale               ="";
      $default_trademarks                  ="";
      $default_australian_consumer_law_url ="";
    }

    //if user disables--> disclaimer will be cleared. after enabling, we need to set it back.
    if($config->get('enable_disclaimer') == 1 && $config->get('disclaimer_preview') == ""){
      $default_disclaimer_preview           = 'Copyright © ' . date('Y') . ' ' . $config_business_info->get('business_name') . ' ACN ' . $config_business_info->get('acn');
    }


    // Enable/disable disclaimer checkbox.
    $form['enable_disclaimer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Footer Disclaimer'),
      '#default_value' => $config->get('enable_disclaimer'),
    ];


    $form['disclaimer_preview'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Disclaimer Preview'),
      '#default_value' => $default_disclaimer_preview,
      '#description' => $this->t('Enter the Disclaimer Preview. Field is editable'),
    ];
    
    //input validity is checked thru the function validateURL
    $form['privacy_statement'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Privacy Statement'),
      '#default_value' => $default_privacy_statement,
      '#description' => $this->t('Enter the Privacy Statement URL'),
      '#element_validate' => [
    [$this, 'validateUrl'],
      ],
    ];

    $form['terms_of_use'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Terms of Use'),
      '#default_value' => $default_terms_of_use  ,
      '#description' => $this->t('Enter the Terms of Use URL'),
      '#element_validate' => [
        [$this, 'validateUrl'],
      ],
    ];

    $form['terms_of_sale'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Terms of Sale'),
      '#default_value' => $default_terms_of_sale  ,
      '#description' => $this->t('Enter the Terms of Sale URL'),
      '#element_validate' => [
        [$this, 'validateUrl'],
      ],
    ];

    $form['trademarks'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Trademarks'),
      '#default_value' => $default_trademarks   ,
      '#description' => $this->t('Enter the Trademarks URL, if any.'),
      '#element_validate' => [
        [$this, 'validateUrl'],
      ],
    ];

    $form['australian_consumer_law_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Australian Consumer Law'),
      '#default_value' => $default_australian_consumer_law_url,
      '#description' => $this->t('Enter the Australian Consumer Law URL'),
      '#element_validate' => [
        [$this, 'validateUrl'],
      ],
];

    return parent::buildForm($form, $form_state);
}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config_business_info = $this->config('etss2_business_info.settings');

    $set_disclaimer_preview           = $form_state->getValue('disclaimer_preview');
    $set_privacy_statement            = $form_state->getValue('privacy_statement');
    $set_terms_of_use                 = $form_state->getValue('terms_of_use');
    $set_terms_of_sale                = $form_state->getValue('terms_of_sale');
    $set_trademarks                   = $form_state->getValue('trademarks');
    $set_australian_consumer_law_url  = $form_state->getValue('australian_consumer_law_url');

    if($set_disclaimer_preview == '' and $form_state->getValue('enable_disclaimer') == 1){
      $set_disclaimer_preview         = 'Copyright © ' . date('Y') . ' ' . $config_business_info->get('business_name') . ' ACN ' . $config_business_info->get('acn');
    }
    if($form_state->getValue('enable_disclaimer') == 0){
      $set_disclaimer_preview           = '';
      $set_privacy_statement            = '';
      $set_terms_of_use                 = '';
      $set_terms_of_sale                = '';
      $set_trademarks                   = '';
      $set_australian_consumer_law_url  = '';
    }

    $this->config('etss2_footer_disclaimer.settings')
      ->set('enable_disclaimer', $form_state->getValue('enable_disclaimer'))
      ->set('disclaimer_preview', $set_disclaimer_preview)
      ->set('privacy_statement', $set_privacy_statement )
      ->set('terms_of_use', $set_terms_of_use )
      ->set('terms_of_sale', $set_terms_of_sale)
      ->set('trademarks', $set_trademarks)
      ->set('australian_consumer_law_url', $set_australian_consumer_law_url)
      ->save();

    parent::submitForm($form, $form_state);
  }
}
