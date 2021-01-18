<?php

namespace Drupal\Tests\editorjs_media\Functional;

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\Tests\editorjs\Functional\EditorJsFieldTestBase;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Tests the media tool.
 *
 * @group editorjs
 */
class MediaToolTest extends EditorJsFieldTestBase {

  use MediaTypeCreationTrait;
  use TestFileCreationTrait {
    getTestFiles as drupalGetTestFiles;
  }

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field',
    'node',
    'media',
    'editor',
    'editorjs_media',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user with permission to create articles.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = \Drupal::service('entity_display.repository');
    $display_repository->getFormDisplay('node', 'article')
      ->setComponent('field_editorjs', [
        'type' => 'editorjs',
        'settings' => [
          'tools' => [
            'media_image' => [
              'status' => TRUE,
              'settings' => [
                'placeholder' => 'Select media',
                'media_types' => ['image'],
                'view_mode' => 'full',
              ],
            ],
          ],
        ],
      ])
      ->save();

    $display_repository->getViewDisplay('node', 'article')
      ->setComponent('field_editorjs', [
        'type' => 'editorjs_default',
        'weight' => 1,
        'settings' => [
          'negate' => TRUE,
        ],
      ])
      ->save();

  }

  /**
   * Tests "editorjs" field "media_image" tool.
   */
  public function testMediaTool() {
    $this->createMediaType('image', ['id' => 'image', 'label' => 'Image']);
    $image = current($this->drupalGetTestFiles('image'));
    /** @var \Drupal\file\Entity\File $file */
    $file = File::create((array) $image);
    $file->save();
    $media = Media::create([
      'bundle' => 'image',
      'name' => $this->randomString(),
      'field_media_image' => [
        [
          'target_id' => $file->id(),
          'alt' => $this->randomString(),
        ],
      ],
    ]);
    $media->save();
    $this->toolTest([
      'type' => 'media_image',
      'data' => [
        'uuid' => $media->uuid(),
        'view_mode' => 'full',
      ],
    ], 'ce-block__content ce-media-image">');
  }

}
