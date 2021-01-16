<?php

namespace Drupal\Tests\editorjs\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;

/**
 * This class provides methods specifically for testing 'editorjs' field.
 */
abstract class EditorJsFieldTestBase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->drupalCreateContentType(['type' => 'article']);
    $this->webUser = $this->drupalCreateUser([
      'create article content',
      'edit own article content',
    ]);
    $this->drupalLogin($this->webUser);

    // Add the 'editorjs' field to the article content type.
    FieldStorageConfig::create([
      'field_name' => 'field_editorjs',
      'entity_type' => 'node',
      'type' => 'editorjs',
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_editorjs',
      'label' => 'EditorJs',
      'entity_type' => 'node',
      'bundle' => 'article',
    ])->save();

  }

  /**
   * The base method for testing "editorjs" field.
   *
   * @param array $value
   *   The value for field widget.
   * @param string $result_contain
   *   The HTML value for check result field formatter.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function toolTest(array $value, $result_contain) {
    $this->drupalGet('node/add/article');
    $field_editorjs = $this->assertSession()->hiddenFieldExists('field_editorjs[0][value]');
    $field_editorjs_value = Json::encode([$value]);
    $field_editorjs->setValue($field_editorjs_value);
    $title = $this->randomString();
    $edit = [
      'title[0][value]' => $title,
    ];

    $this->submitForm($edit, 'Save');
    $this->assertSession()->responseContains($result_contain);
  }

}
