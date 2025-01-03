<?php

namespace Drupal\mymodule\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Dotenv\Dotenv;

/**
 * Class SocialFooterForm.
 */
class mymoduleForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mymodule.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mymodule';
  }

  function mymodule_init() {
    $dotenv_path = __DIR__;
    
    if (file_exists($dotenv_path . '/.env')) {
        $dotenv = Dotenv::createImmutable($dotenv_path);
        $dotenv->load();

        // Ensure the variables are loaded.
        if (!getenv('AWS_ACCESS_KEY_ID') || !getenv('AWS_SECRET_ACCESS_KEY')) {
            \Drupal::logger('mymodule')->error('AWS credentials not found in .env file.');
        }
    } else {
        \Drupal::logger('mymodule')->error('.env file not found in the module directory.');
    }
}

 /**
 * Builds the form for managing social media links.
 *
 * This form allows users to:
 * - Dynamically add multiple social media links.
 * - Provide a name, URL, and upload an icon for each link.
 * - Remove existing social media links.
 * - Save the configured links to the module's configuration.
 *
 * @param array $form
 *   The form structure.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The current state of the form.
 *
 * @return array
 *   The rendered form array.
 */
public function buildForm(array $form, FormStateInterface $form_state) {
    // Load existing configuration for social links.
    $config = $this->config('mymodule.settings');

    // Retrieve social links from form state (if already modified) or configuration.
    $social_links = $form_state->get('social_links') ?? $config->get('social_links') ?? [];
    $form_state->set('social_links', $social_links);

    // Container for all social links.
    $form['social_links_container'] = [
        '#type' => 'container',  // Groups elements in a container.
        '#tree' => TRUE,        // Ensures form values are structured hierarchically.
    ];

    // Loop through each existing social link and build its corresponding form elements.
    foreach ($social_links as $key => $link) {
        $form['social_links_container'][$key] = [
            '#type' => 'details',  // Creates a collapsible fieldset for each link.
            '#title' => $link['name'] ? $this->t(' @name', ['@name' => $link['name']]) : $this->t('Add details'),
            '#open' => TRUE,      // Ensures the fieldset is open by default.
        ];

        // Input for the social media name.
        $form['social_links_container'][$key]['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#default_value' => $link['name'] ?? '',  // Prefills the value if available.
            '#required' => TRUE,  // Makes the field mandatory.
        ];

        // Input for the social media URL.
        $form['social_links_container'][$key]['url'] = [
            '#type' => 'url',
            '#title' => $this->t('URL'),
            '#default_value' => $link['url'] ?? '',  // Prefills the value if available.
            '#required' => TRUE,  // Ensures only valid URLs are accepted.
        ];

        // File upload input for the icon.
        $form['social_links_container'][$key]['icon'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Icon'),
            '#description' => $this->t('Upload an image file for the icon.'),
            '#upload_location' => 'temporary://',  // Stores files in a temporary location.
            '#default_value' => isset($link['icon_file_id']) ? [$link['icon_file_id']] : NULL,  // Prefills the file ID if available.
            '#required' => TRUE,  // Makes the field mandatory.
        ];

        // Remove button for each link.
        $form['social_links_container'][$key]['remove'] = [
            '#type' => 'submit',
            '#value' => $this->t('Remove'),
            '#submit' => [[$this, 'removeSocialLink']],  // Custom submit handler for removal.
            '#name' => 'remove_' . $key,  // Unique name for the button.
            '#ajax' => [  // AJAX settings for dynamic removal.
                'callback' => '::ajaxCallback',
                'wrapper' => 'social-links-container-wrapper',
            ],
            '#attributes' => ['data-key' => $key],  // Passes the link key as an attribute.
        ];
    }

    // Button to add a new social link dynamically.
    $form['social_links_container']['add_more'] = [
        '#type' => 'submit',
        '#value' => $this->t('Add More'),
        '#submit' => [[$this, 'addSocialLink']],  // Custom submit handler for adding links.
        '#ajax' => [  // AJAX settings for dynamic addition.
            'callback' => '::ajaxCallback',
            'wrapper' => 'social-links-container-wrapper',
        ],
    ];

    // AJAX wrapper for dynamically updated sections.
    $form['#prefix'] = '<div id="social-links-container-wrapper">';
    $form['#suffix'] = '</div>';

    // Submit button to save the form.
    $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Save Links'),
    ];

    // Return the parent form structure.
    return parent::buildForm($form, $form_state);
}


  /**
   * AJAX callback for dynamic changes.
   */
  public function ajaxCallback(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * Add a new social link.
   */
  public function addSocialLink(array &$form, FormStateInterface $form_state) {
    $social_links = $form_state->get('social_links') ?? [];
    $social_links[] = ['name' => '', 'url' => '', 'icon_file_id' => ''];
    $form_state->set('social_links', $social_links);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Remove a social link.
   */
  public function removeSocialLink(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $key = $trigger['#attributes']['data-key'] ?? NULL;

    if ($key !== NULL) {
      $social_links = $form_state->get('social_links');
      unset($social_links[$key]);
      $social_links = array_values($social_links); // Reindex the array.
      $form_state->set('social_links', $social_links);
      $form_state->setRebuild(TRUE);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $social_links = $form_state->getValue('social_links_container');
    unset($social_links['add_more']);

    foreach ($social_links as $key => &$link) {
      if (!empty($link['icon'])) {
        $file = File::load(reset($link['icon']));
        $file->setPermanent();
        $file->save();

        // $this->messenger()->addMessage($this->t('file->getFileUri() = @x ', ['@x' =>$file->getFileUri()]));
        
        // assume uploadToS3 handles the S3 upload and returns the URL.
        $s3_url = $this->uploadToS3($file->getFileUri());

        // $this->messenger()->addMessage($this->t('s3_url = @x ', ['@x' =>$s3_url]));

        $link['icon_url'] = $s3_url; // save the S3 URL.
        $link['icon_file_id'] = $file->id(); // save file ID for default value.
      }
    }

    $this->config('mymodule.settings')
      ->set('social_links', $social_links)
      ->save();

    $this->messenger()->addMessage($this->t('Social links saved successfully.'));
  }

  /**
   * Uploads a file to S3 and returns the file URL.
   */
  private function uploadToS3($file_uri) {
  // AWS configuration
  $bucket = 'etss2-social-icons-footer';
  $aws_region = getenv('AWS_REGION');
  $aws_key = getenv('AWS_ACCESS_KEY_ID');
  $aws_secret = getenv('AWS_SECRET_ACCESS_KEY');
  
  try {
    // Create an S3 client
    $s3 = new S3Client([
      'region' => $aws_region,
      'version' => 'latest',
      'credentials' => [
        'key' => $aws_key,
        'secret' => $aws_secret,
      ],
       'http' => [
        'verify' => false,
    ],
    ]);

    // Get file contents and metadata
    $file_contents = file_get_contents($file_uri);
    $file_name = basename($file_uri);

    // Upload to S3
    $result = $s3->putObject([
      'Bucket' => $bucket,
      'Key' => 'social-icons/' . $file_name, // Prefix folder for organization
      'Body' => $file_contents,
      'ContentType' => mime_content_type($file_uri),
    ]);

    // Return the public URL of the uploaded file
    return $result['ObjectURL'];
  } catch (AwsException $e) {
    // Log the error and return null
    \Drupal::logger('mymodule')->error('S3 Upload Error: ' . $e->getMessage());
    // $this->messenger()->addMessage($this->t('S3 Upload Error: = @x ', ['@x' =>$e->getMessage()]));

    
    return null;
  }
}
}

// private function uploadToS3($file_uri) {
//   // Logic for uploading to S3.
//   // Use AWS SDK for PHP to upload the file.
//   // Return the public URL of the uploaded file.
//   return 'https://s3-bucket-url/path-to-file'; // Replace with actual S3 logic.
// }