<?php

namespace Drupal\etss2_theme_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Theme settings configuration form with file upload.
 */
class ThemeSettingsForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return ['etss2_theme_settings.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'etss2_theme_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('etss2_theme_settings.settings');

    // Font file upload fields.
    $font_fields = [
      'font_large_text_bold' => 'Font Large Text - Bold',
      'font_large_text_light' => 'Font Large Text - Light',
      'font_body_bold' => 'Font Body - Bold',
      'font_body_light' => 'Font Body - Light',
      'font_heading_bold' => 'Font Heading - Bold',
      'font_heading_light' => 'Font Heading - Light',
    ];

    $form['font_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Font Settings'),
    ];
    foreach ($font_fields as $field_name => $label) {
      $form['font_settings'][$field_name] = [
        '#type' => 'managed_file',
        '#title' => $this->t($label),
        '#upload_location' => 'public://fonts/',
        '#default_value' => $config->get($field_name) ?? [],
        '#upload_validators' => [
          'file_validate_extensions' => ['ttf otf woff woff2'],
        ],
      ];
    }

    $form['color_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Color Settings'),
    ];
    $form['color_settings']['primary_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Primary Color'),
      '#default_value' => $theme_settings->primary_color ?? '#FFFFFF',
      '#description' => $this->t('The primary(background) color for your site. Example: White (#FFFFFF).'),
      '#required' => TRUE,
    ];

    $form['color_settings']['secondary_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Secondary Color'),
      '#default_value' => $theme_settings->secondary_color ?? '#000000',
      '#description' => $this->t('A secondary color to complement the primary color. Hero background'),
      '#required' => TRUE,
    ];

    // Add new color fields.
    $form['color_settings']['tertiary_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Tertiary Color'),
      '#default_value' => $theme_settings->tertiary_color ?? '#151515',
      '#description' => $this->t('Adds variety and visual interest. Used in Header.'),
    ];

    $form['color_settings']['button_background_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Button Background Color'),
      '#default_value' => $theme_settings->button_background_color ?? '#007bff',
    ];

    $form['color_settings']['error_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Error Color'),
      '#default_value' => $theme_settings->error_color ?? '#ff0000',
      '#description' => $this->t('Used for error messages. Example: Red (#ff0000).'),
    ];

    $form['color_settings']['warning_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Warning Color'),
      '#default_value' => $theme_settings->error_color ?? '#ffaa00',
      '#description' => $this->t('Used for error messages. Example: Orange (#ffaa00).'),
    ];

    $form['color_settings']['success_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Success Color'),
      '#default_value' => $theme_settings->error_color ?? '#00ff00',
      '#description' => $this->t('Used for error messages. Example: Green (#00ff00).'),
    ];


    $form['layout_spacing'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Layout and Spacing Settings'),
      '#description' => $this->t('Configure the layout and spacing for the site.'),
    ];
    $form['layout_spacing']['padding'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Global Padding'),
      '#default_value' => $theme_settings->padding ?? '20px',
      '#description' => $this->t('Set the global padding for sections, e.g., 20px. This will be applied consistently across different sections of the site.'),
      '#required' => TRUE,
      '#pattern' => '^\d+(px|em|rem|%)$',
      '#element_validate' => [[get_class(object: $this), 'validateSpacing']],
    ];
    $form['layout_spacing']['margin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Global Margin'),
      '#default_value' => $theme_settings->margin ?? '20px',
      '#description' => $this->t('Set the global margin for sections, e.g., 20px. This helps control the spacing around different elements.'),
      '#required' => TRUE,
      '#pattern' => '^\d+(px|em|rem|%)$',
      '#element_validate' => [[get_class(object: $this), 'validateSpacing']],
    ];
    $form['layout_spacing']['grid_columns'] = [
      '#type' => 'select',
      '#title' => $this->t('Grid Layout Columns'),
      '#options' => [
        '2' => $this->t('2 Columns'),
        '3' => $this->t('3 Columns'),
        '4' => $this->t('4 Columns'),
      ],
      '#default_value' => $theme_settings->grid_columns ?? '3',
      '#description' => $this->t('Select the number of columns for the grid layout. This will determine how content is arranged in different sections of the site.'),
      '#required' => TRUE,
    ];


    // Custom CSS/JavaScript Editor with validation.
    $form['custom_code'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Custom CSS/JavaScript'),
      '#description' => $this->t('Add custom CSS and JavaScript for advanced customization. Ensure that the syntax is correct.'),
    ];
    $form['custom_code']['custom_css'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Custom CSS'),
      '#default_value' => $theme_settings->custom_css ?? '',
      '#description' => $this->t('Add any custom CSS that should be applied globally. This can be used for minor adjustments not covered by the theme settings. Example: "body {background-color: #ffffff;}"'),
      '#attributes' => [
        'class' => ['custom-css-editor'],
        'placeholder' => $this->t('/* Write your CSS here */'),
      ],
    ];
    $form['custom_code']['custom_js'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Custom JavaScript'),
      '#default_value' => $theme_settings->custom_js ?? '',
      '#description' => $this->t('Add any custom JavaScript that should be applied globally. Be cautious with your scripts to avoid conflicts.'),
      '#attributes' => [
        'class' => ['custom-js-editor'],
        'placeholder' => $this->t('// Write your JavaScript here'),
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  public static function validateSpacing(&$element, FormStateInterface $form_state, &$form)
  {
    $value = $form_state->getValue($element['#name']);
    if (!preg_match('/^\d+(px|em|rem|%)$/', subject: $value)) {
      $form_state->setError($element, t('The value must be a valid CSS spacing format (e.g., 20px, 1em, 50%).'));
    }
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    // Fetch the configuration for theme settings and S3.
    $config = $this->config('etss2_theme_settings.settings'); // For theme settings
    $s3_config = \Drupal::config('s3fs.settings'); // For S3 configuration

    // Get the bucket name from the S3 configuration.
    $bucket_name = $s3_config->get('bucket');
    $public_folder = $s3_config->get('public_folder');

    // Define the font fields you want to process.
    $font_fields = [
      'font_large_text_bold',
      'font_large_text_light',
      'font_body_bold',
      'font_body_light',
      'font_heading_bold',
      'font_heading_light',
    ];

    // Loop through the font fields and process them.
    foreach ($font_fields as $field_name) {
      $file_ids = $form_state->getValue($field_name);

      if (!empty($file_ids)) {
        // Load the first file ID in the array (assuming a single file is uploaded)
        $file = File::load(reset($file_ids));
        if ($file) {
          // Set the file as permanent
          $file->setPermanent();
          $file->save();

          // Get the file URI and construct the S3 URL
          $file_uri = $file->getFileUri();
          $file_url = 'https://' . $bucket_name . '.s3.amazonaws.com/' . $public_folder . '/' . ltrim($file_uri, 'public://');

          // Save the file ID and file URL in the configuration
          $config->set($field_name, $file_ids)
            ->set($field_name . '_url', $file_url)
            ->save();
        }
      } else {
        // If no file is uploaded, clear the configuration for that field
        $config->clear($field_name);
      }
    }



    // Save color settings.
    $config
      ->set('primary_color', $form_state->getValue('primary_color'))
      ->set('secondary_color', $form_state->getValue('secondary_color'))
      ->set('tertiary_color', $form_state->getValue('tertiary_color'))
      ->set('button_background_color', $form_state->getValue('button_background_color'))
      ->set('error_color', $form_state->getValue('error_color'))
      ->set('warning_color', $form_state->getValue('warning_color'))
      ->set('success_color', $form_state->getValue('success_color'))
      ->set('padding', $form_state->getValue('padding'))
      ->set('margin', $form_state->getValue('margin'))
      ->set('grid_columns', $form_state->getValue('grid_columns'))
      ->set('custom_css', $form_state->getValue('custom_css'))
      ->set('custom_js', $form_state->getValue('custom_js'))

      ->save();

    parent::submitForm($form, $form_state);
  }


}
