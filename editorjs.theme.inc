<?php

/**
 * @file
 * EditorJs theme preprocess functions.
 */

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
