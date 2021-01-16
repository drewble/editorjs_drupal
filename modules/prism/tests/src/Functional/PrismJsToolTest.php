<?php

namespace Drupal\Tests\editorjs_prism\Functional;

use Drupal\Tests\editorjs\Functional\EditorJsFieldTestBase;

/**
 * Tests the prismJs tool.
 *
 * @group editorjs
 */
class PrismJsToolTest extends EditorJsFieldTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field',
    'node',
    'editorjs_prism',
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
            'prism_code' => [
              'status' => TRUE,
              'settings' => [
                'placeholder' => 'Enter a code',
                'languages' => "php | PHP\r\njs | JavaScript\r\ncss | CSS\r\ntwig | TWIG",
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
   * Tests "editorjs" field "prism_code" tool.
   */
  public function testPrismCodeTool() {
    $value = 'public function testCodeTool() {}';
    $this->toolTest([
      'type' => 'prism_code',
      'data' => [
        'code' => $value,
        'languageCode' => 'php',
      ],
    ], "<code class=\"language-php\" class=\"ce-code\">{$value}</code>");
  }

}
