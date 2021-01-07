<?php

namespace Drupal\editorjs\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drush\Commands\DrushCommands;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Provides drush commands for "editorjs" module.
 */
class EditorjsCommands extends DrushCommands {

  /**
   * Download all dependency libraries.
   *
   * @usage editorjs:download editorjs:d
   *   Usage description
   *
   * @command editorjs:download
   * @aliases editorjs:d
   */
  public function downloadLibraries() {
    $libraries = [
      'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.19.1/dist/editor.min.js' => '/libraries/editorjs--editorjs/dist/editor.js',
      'https://cdn.jsdelivr.net/npm/@editorjs/checklist@1.3.0/dist/bundle.min.js' => '/libraries/editorjs--checklist/dist/bundle.js',
      'https://cdn.jsdelivr.net/npm/@editorjs/code@2.6.0/dist/bundle.min.js' => '/libraries/editorjs--code/dist/bundle.js',
      'https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1.2.0/dist/bundle.min.js' => '/libraries/editorjs--delimiter/dist/bundle.js',
      'https://raw.githubusercontent.com/batkor/editorjs-dimage/main/dist/bundle.js' => '/libraries/editorjs-dimage/dist/bundle.js',
      'https://cdn.jsdelivr.net/npm/@editorjs/inline-code@1.3.1/dist/bundle.min.js' => '/libraries/editorjs--inline-code/dist/bundle.js',
      'https://cdn.jsdelivr.net/npm/@editorjs/link@2.3.1/dist/bundle.min.js' => '/libraries/editorjs--link/dist/bundle.js',
      'https://cdn.jsdelivr.net/npm/@editorjs/list@1.6.1/dist/bundle.min.js' => '/libraries/editorjs--list/dist/bundle.js',
      'https://cdn.jsdelivr.net/npm/@editorjs/table@1.3.0/dist/bundle.min.js' => '/libraries/editorjs--table/dist/bundle.js',
      'https://raw.githubusercontent.com/batkor/editorjs-code-lang/main/dist/bundle.js' => '/libraries/editorjs-code-lang/dist/bundle.js',
    ];
    $app_dir = \Drupal::root();
    foreach ($libraries as $path => $destination) {
      file_put_contents($app_dir . $destination, file_get_contents($path));
      $this->logger()->success(dt('Downloaded: ' . $path));
    }
    $this->logger()->success(dt('All libraries downloaded.'));
  }

}
