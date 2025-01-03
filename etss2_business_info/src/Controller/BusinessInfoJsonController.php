<?php

namespace Drupal\etss2_business_info\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class BusinessInfoJsonController.
 *
 * Provides a JSON response for business information.
 */
class BusinessInfoJsonController extends ControllerBase {

  /**
   * Returns the business info as JSON.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON representation of business info.
   */
  public function getBusinessInfo() {
    // Load the configuration.
    $config = $this->config('etss2_business_info.settings');

    // Prepare the response data.
    $data = [
      'business_name' => $config->get('business_name') ?? '',
      'abn' => $config->get('abn') ?? '',
      'acn' => $config->get('acn') ?? '',
      'business_address' => $config->get('business_address') ?? '',
      'business_phone' => $config->get('business_phone') ?? '',
      'business_email' => $config->get('business_email') ?? '',
      'operational_hours' => $config->get('operational_hours') ?? '',
      'help_portal_url' => $config->get('help_portal_url') ?? '',
      'customer_portal_url' => $config->get('customer_portal_url') ?? '',
      'request_callback_url' => $config->get('request_callback_url') ?? '',
    ];

    // Return the JSON response.
    return new JsonResponse($data);
  }

}

// ->set('business_name', $form_state->getValue('business_name'))
//       ->set('abn', $form_state->getValue('abn'))
//       ->set('acn', $form_state->getValue('acn'))
//       ->set('business_address', $form_state->getValue('business_address'))
//       ->set('business_phone', $form_state->getValue('business_phone'))
//       ->set('business_email', $form_state->getValue('business_email'))
//       ->set('operational_hours', $form_state->getValue('operational_hours'))
//       ->set('help_portal_url', $form_state->getValue('help_portal_url'))
//       ->set('customer_portal_url', $form_state->getValue('customer_portal_url'))
//       ->set('request_callback_url'
