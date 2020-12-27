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
function template_preprocess_ce_block__linkTool(&$variables) {
  $variables['data']['anchor'] = parse_url($variables['data']['link'], PHP_URL_HOST);
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
