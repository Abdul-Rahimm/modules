<?php

namespace Drupal\etss2_social_footer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * Class SocialFooterForm.
 */
class SocialFooterForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['etss2_social_footer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'etss2_social_footer_form';
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
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('etss2_social_footer.settings');

    // Get existing social media links from the configuration.
    $social_links = $form_state->get('social_links') ?? $config->get('social_links') ?? [];
    $form_state->set('social_links', $social_links);

    // Wrap all social links in a container for dynamic manipulation.
    $form['social_links_container'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    foreach ($social_links as $key => $link) {
      $form['social_links_container'][$key] = [
        '#type' => 'details',
        '#title' => $link['name'] ? $this->t(' @name', ['@name' => $link['name']]) : $this->t('Add details'),
        '#open' => TRUE,
      ];

      $form['social_links_container'][$key]['name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Name'),
        '#default_value' => $link['name'] ?? '',
        '#required' => TRUE,
      ];

      $form['social_links_container'][$key]['url'] = [
        '#type' => 'url',
        '#title' => $this->t('URL'),
        '#default_value' => $link['url'] ?? '',
        '#required' => TRUE,
      ];

      $form['social_links_container'][$key]['icon'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Icon'),
        '#description' => $this->t('Upload an image file for the icon.'),
        '#upload_location' => 'temporary://', // Save to temporary storage first.
        '#default_value' => isset($link['icon_file_id']) ? [$link['icon_file_id']] : NULL,
        '#required' => TRUE,
      ];

      $form['social_links_container'][$key]['remove'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove'),
        '#submit' => [[$this, 'removeSocialLink']],
        '#name' => 'remove_' . $key,
        '#ajax' => [
          'callback' => '::ajaxCallback',
          'wrapper' => 'social-links-container-wrapper',
        ],
        '#attributes' => ['data-key' => $key],
      ];
    }

    $form['social_links_container']['add_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add More'),
      '#submit' => [[$this, 'addSocialLink']],
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'wrapper' => 'social-links-container-wrapper',
      ],
    ];

    $form['#prefix'] = '<div id="social-links-container-wrapper">';
    $form['#suffix'] = '</div>';

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Links'),
    ];

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

    $this->config('etss2_social_footer.settings')
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
    \Drupal::logger('etss2_social_footer')->error('S3 Upload Error: ' . $e->getMessage());
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