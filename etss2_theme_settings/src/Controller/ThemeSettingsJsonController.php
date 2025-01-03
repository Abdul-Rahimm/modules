<?php

namespace Drupal\etss2_theme_settings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a JSON API for ETSS2 Theme Settings.
 */
class ThemeSettingsJsonController extends ControllerBase {

  /**
   * Returns theme settings as JSON.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response with the theme settings.
   */
  public function getThemeSettings() {
    // Load the configuration for ETSS2 Theme Settings.
    $config = $this->config('etss2_theme_settings.settings');

    // Prepare response data.
    $data = [
      // Font settings.
      'font_large_text_bold' => [
        'url' => $config->get('font_large_text_bold_url'),
      ],
      'font_large_text_light' => [
        'url' => $config->get('font_large_text_light_url'),
      ],
      'font_body_bold' => [
        'url' => $config->get('font_body_bold_url'),
      ],
      'font_body_light' => [
        'url' => $config->get('font_body_light_url'),
      ],
      'font_heading_bold' => [
        'url' => $config->get('font_heading_bold_url'),
      ],
      'font_heading_light' => [
        'url' => $config->get('font_heading_light_url'),
      ],

      'primary_color' => $config->get('primary_color'),
      'secondary_color' => $config->get('secondary_color'),
      'tertiary_color' => $config->get('tertiary_color'),
      'error_color' => $config->get('error_color'),
      'warning_color' => $config->get('warning_color'),
      'success_color' => $config->get('success_color'),
      'button_background_color' => $config->get('button_background_color'),
      
      'padding' => $config->get('padding'),
      'margin' => $config->get('margin'),
      'grid_columns' => $config->get('grid_columns'),
      'custom_css' => $config->get('custom_css'),
      'custom_js' => $config->get('custom_js'),

    ];

    // Return the response as JSON.
    return new JsonResponse($data);
  }

}
