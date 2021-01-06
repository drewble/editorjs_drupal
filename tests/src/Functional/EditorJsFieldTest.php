<?php

namespace Drupal\Tests\editorjs\Functional;

use Drupal\file\Entity\File;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Tests for the 'editorjs' field.
 *
 * @group editorjs
 */
class EditorJsFieldTest extends EditorJsFieldTestBase {

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
    'editorjs',
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
            'checklist' => [
              'status' => TRUE,
              'settings' => ['inlineToolbar' => TRUE],
            ],
            'list' => [
              'status' => TRUE,
              'settings' => ['inlineToolbar' => TRUE],
            ],
            'inline_code' => [
              'status' => TRUE,
            ],
            'header' => [
              'status' => TRUE,
              'settings' => [
                'placeholder' => 'Enter a header',
                'levels' => [2, 3, 4, 5],
                'defaultLevel' => 2,
              ],
            ],
            'code' => [
              'status' => TRUE,
              'settings' => ['placeholder' => 'Enter a code'],
            ],
            'delimiter' => [
              'status' => TRUE,
            ],
            'image' => [
              'status' => TRUE,
              'settings' => [
                'headers' => [],
                'endpoints' => [],
              ],
            ],
            'linkTool' => [
              'status' => TRUE,
              'settings' => ['endpoint' => ''],
            ],
            'table' => [
              'status' => TRUE,
              'settings' => ['inlineToolbar' => TRUE, 'rows' => 2, 'cols' => 2],
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
   * Tests "editorjs" field "paragraph" tool.
   */
  public function testParagraphTool() {
    $this->toolTest([
      'type' => 'paragraph',
      'data' => [
        'text' => $this->randomString(),
      ],
    ], '<p class="ce-paragraph">');
  }

  /**
   * Tests "editorjs" field "checklist" tool.
   */
  public function testCheckListTool() {
    $this->toolTest([
      'type' => 'checklist',
      'data' => [
        'items' => [
          ['text' => $this->randomString(), 'checked' => TRUE],
        ],
      ],
    ], '<div class="ce-block__content cdx-checklist">');
  }

  /**
   * Tests "editorjs" field "list" tool.
   */
  public function testListTool() {
    $this->toolTest([
      'type' => 'list',
      'data' => [
        'style' => 'unordered',
        'items' => [$this->randomString()],
      ],
    ], '<ul class="cdx-list');
  }

  /**
   * Tests "editorjs" field "header" tool.
   */
  public function testHeaderTool() {
    $this->toolTest([
      'type' => 'header',
      'data' => [
        'text' => $this->randomString(),
        'level' => 2,
      ],
    ], '<h2 class="ce-block__content">');
  }

  /**
   * Tests "editorjs" field "code" tool.
   */
  public function testCodeTool() {
    $value = 'public function testCodeTool() {}';
    $this->toolTest([
      'type' => 'code',
      'data' => [
        'code' => $value,
      ],
    ], '<code class="ce-code">' . $value . '</code>');
  }

  /**
   * Tests "editorjs" field "delimiter" tool.
   */
  public function testDelimiterTool() {
    $this->toolTest([
      'type' => 'delimiter',
      'data' => [],
    ], '<div class="ce-block__content ce-delimiter"></div>');
  }

  /**
   * Tests "editorjs" field "linkTool" tool.
   */
  public function testLinkToolTool() {
    $this->toolTest([
      'type' => 'linkTool',
      'data' => [
        'link' => 'https://www.drupal.org/',
        'meta' => [
          'title' => 'Drupal',
          'description' => 'Drupal is an open source platform for building amazing digital experiences.',
        ],
      ],
    ], '<div class="link-tool ce-block__content">');
  }

  /**
   * Tests "editorjs" field "table" tool.
   */
  public function testTableTool() {
    $this->toolTest([
      'type' => 'table',
      'data' => [
        'content' => [
          [$this->randomString(), $this->randomString()],
          [$this->randomString(), $this->randomString()],
        ],
      ],
    ], '<div class="ce-block__content ce-table__wrap">');
  }

  /**
   * Tests "editorjs" field "image" tool.
   */
  public function testImageTool() {
    $image = current($this->drupalGetTestFiles('image'));
    /** @var \Drupal\file\Entity\File $file */
    $file = File::create((array) $image);
    $file->save();
    $this->toolTest([
      'type' => 'image',
      'data' => [
        'file' => [
          'url' => $file->createFileUrl(),
          'uuid' => $file->uuid(),
        ],
      ],
    ], '<div class="image-tool__image">');
  }

}
