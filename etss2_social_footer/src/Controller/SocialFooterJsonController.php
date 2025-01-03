<?php

namespace Drupal\etss2_social_footer\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides a JSON response for Social Footer.
 */
class SocialFooterJsonController extends ControllerBase {

  /**
   * Returns the Social Footer links as JSON.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON representation of social media links.
   */
  public function getSocialFooter() {
    // Load the configuration.
    $config = $this->config('etss2_social_footer.settings');

    // Get the social links array.
    $social_links = $config->get('social_links') ?? [];

    // Prepare the response data.
    $data = [];
    foreach ($social_links as $link) {
      $data[] = [
        'name' => $link['name'] ?? '',
        'url' => $link['url'] ?? '',
        'icon_url' => $link['icon_url'] ?? '',
      ];
    }

    // Return the JSON response.
    return new JsonResponse($data);
  }
}
