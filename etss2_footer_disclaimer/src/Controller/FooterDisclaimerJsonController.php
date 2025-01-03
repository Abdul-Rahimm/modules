<?php

namespace Drupal\etss2_footer_disclaimer\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class FooterDisclaimerJsonController.
 *
 * Provides a JSON response for Footer Disclaimer.
 */
class FooterDisclaimerJsonController extends ControllerBase {

  /**
   * Returns the Footer Disclaimer as JSON.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON representation of business info.
   */
  public function getFooterDisclaimer() {
    // Load the configuration.
    $config = $this->config('etss2_footer_disclaimer.settings');

    // Prepare the response data.
    $data = [
      'enable_disclaimer' => $config->get('enable_disclaimer') ?? '',
      'disclaimer_preview' => $config->get('disclaimer_preview') ?? '',
      'privacy_statement' => $config->get('privacy_statement') ?? '',
      'terms_of_use' => $config->get('terms_of_use') ?? '',
      'terms_of_sale' => $config->get('terms_of_sale') ?? '',
      'trademarks' => $config->get('trademarks') ?? '',
      'australian_consumer_law_url' => $config->get('australian_consumer_law_url') ?? '',
    ];

    // Return the JSON response.
    return new JsonResponse($data);
  }
  //    Enable check
  // Disclaimer Preview
  // Privacy Statement
  // Terms of Use
  // Terms of Sale
  // Trademarks
  // Australian Consumer Law

}
