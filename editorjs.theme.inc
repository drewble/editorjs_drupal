<?php

/**
 * @file
 * EditorJs theme preprocess functions.
 */

use Drupal\Core\Render\Element;

/**
 * Implements hook_preprocess_HOOK().
 */
function template_preprocess_ce_blocks(&$variables) {
  $variables['blocks'] = [];
  $libraries = ['editorjs/base'];
  foreach (Element::children($variables['elements']) as $key) {
    $variables['blocks'][] = $variables['elements'][$key];
    $libraries[] = 'editorjs/' . $variables['elements'][$key]['#data']['type'];
  }
  $variables['#attached']['library'] = $libraries;
}

/**
 * Implements hook_preprocess_HOOK().
 */
function template_preprocess_ce_block(&$variables) {
  $variables['type'] = $variables['data']['type'];
  $variables['data'] = $variables['data']['data'];
}

/**
 * Implements hook_theme_suggestions_HOOK().
 */
function editorjs_theme_suggestions_ce_block(array $variables) {
  $suggestions = [];
  if (!empty($variables['data']['type'])) {
    $suggestions[] = 'ce_block__' . $variables['data']['type'];
  }
  return $suggestions;
}

/**
 * Implements hook_preprocess_HOOK().
 *
 * Prepare variables for "ce_block__image" template.
 */
function template_preprocess_ce_block__image(&$variables) {
  $theme = empty($variables['data']['image_style']) ? 'image' : 'image_style';

  if (empty($variables['data']['file']['uuid'])) {
    return;
  }

  /** @var \Drupal\file\Entity\File $file */
  $file = \Drupal::service('entity.repository')
    ->loadEntityByUuid('file', $variables['data']['file']['uuid']);
  if (empty($file)) {
    return;
  }

  $variables['data']['image'] = [
    '#theme' => $theme,
    '#style_name' => $variables['data']['image_style'] ?? NULL,
    '#uri' => $file->getFileUri(),
    '#attributes' => ['class' => ['image-tool__image-picture']],
  ];
}

