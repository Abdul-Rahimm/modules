<?php

namespace Drupal\swagger_ui_page\Controller;

use Drupal\Core\Controller\ControllerBase;

class SwaggerUIController extends ControllerBase {
  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => '<div id="swagger-ui"></div>',
      '#attached' => [
        'library' => [
          'swagger_ui_page/swagger_ui',
        ],
      ],
    ];
  }
}
