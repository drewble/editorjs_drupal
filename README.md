The module integrate EditorJs to Drupal 8 or higher
===========

### Supported plugins:

- [x] [Header](https://github.com/editor-js/header)
- [x] [List](https://github.com/editor-js/list)
- [x] [Checklist](https://github.com/editor-js/checklist)
- [x] [Link](https://github.com/editor-js/link)
- [x] [Table](https://github.com/editor-js/table)
- [x] [Code](https://github.com/editor-js/code)
- [x] [InlineCode](https://github.com/editor-js/inline-code)
- [x] [Delimiter](https://github.com/editor-js/delimiter)
- [x] [image](https://github.com/batkor/editorjs-dimage)
- [x] [Code PrismJs](https://github.com/batkor/editorjs-code-lang)

### Description

**Editor.js** is a Block-Styled editor.
Blocks are structural units, of which the Entry is composed.
For example, `Paragraph`, `Heading`, `Image`, `Video`, `List` are `Blocks`.
Each Block is represented by Plugin.
[More information](https://editorjs.io/)

This module provides field type, field formatter, field widget
and plugin manager for easy adding new/custom EditorJs tools.

### Installation

1. Using Composer: (recommended)

   If you want Composer to automatically download the Libraries
   to the /libraries folder when installing the module, you must
   update your root project composer.json in the following sections:

   "extra" section: add the following:
   ```json
      "installer-types": [
          "npm-asset"
      ],
      "installer-paths": {
          "libraries/{$name}": [
              "type:drupal-library",
              "type:npm-asset"
          ]
      }
   ```
   "repositories" section: add the following:
   ```json
     "repositories": {
         {
             "type": "composer",
             "url": "https://asset-packagist.org"
         },
         {
            "type": "package",
            "package": {
                "name": "batkor/editorjs-dimage",
                "type": "drupal-library",
                "version": "1.0.1",
                "dist": {
                    "url": "https://github.com/batkor/editorjs-dimage/archive/1.0.1.zip",
                    "type": "zip"
                }
            }
         }
     }
   ```
   More information (https://www.drupal.org/docs/develop/using-composer/using-composer-to-install-drupal-and-manage-dependencies)

   If your need PrismJs tool, add following package:
   ```json
    {
      "type": "package",
      "package": {
      "name": "batkor/editorjs-code-lang",
      "type": "drupal-library",
      "version": "1.0.1",
      "dist": {
          "url": "https://github.com/batkor/editorjs-code-lang/archive/1.0.1.zip",
          "type": "zip"
        }
      }
    }
    ```
2. Manually:

   1. Download, extract and copy this module to your directory
      modules `/modules` or `/modules/contrib`.
   2. Download, extract and copy libraries to
      libraries directory `/libraries`. See table

    ##### Dependencies libraries.

    | Library       | PATH     | Link                    |
    | ----------- | -------- | -------------------------------|
    | EditorJs (Core) | `DRUPAL_ROOT/libraries/editorjs--editorjs/dist/editor.js` | (https://github.com/codex-team/editor.js/releases) |
    | Header (Tool) | `DRUPAL_ROOT/libraries/editorjs--header/dist/bundle.js` | (https://github.com/editor-js/header) |
    | List (Tool) | `DRUPAL_ROOT/libraries/editorjs--list/dist/bundle.js` | (https://github.com/editor-js/list) |
    | Checklist (Tool) | `DRUPAL_ROOT/libraries/editorjs--checklist/dist/bundle.js` | (https://github.com/editor-js/checklist) |
    | Link (Tool) | `DRUPAL_ROOT/libraries/editorjs--link/dist/bundle.js` | (https://github.com/editor-js/link) |
    | Table (Tool) | `DRUPAL_ROOT/libraries/editorjs--table/dist/bundle.js` | (https://github.com/editor-js/table) |
    | Code (Tool) | `DRUPAL_ROOT/libraries/editorjs--code/dist/bundle.js` | (https://github.com/editor-js/code) |
    | InlineCode (Tool) | `DRUPAL_ROOT/libraries/editorjs--inline-code/dist/bundle.js` | (https://github.com/editor-js/inline-code) |
    | Delimiter (Tool) | `DRUPAL_ROOT/libraries/editorjs--delimiter/dist/bundle.js` | (https://github.com/editor-js/delimiter) |
    | Image (Tool) | `DRUPAL_ROOT/libraries/editorjs--delimiter/dist/bundle.js` | (https://github.com/batkor/editorjs-dimage/releases) |
    | Code PrismJs (Tool) (optional) | `DRUPAL_ROOT/libraries/editorjs-code-lang/dist/bundle.js` | (https://github.com/batkor/editorjs-dimage/releases) |
3. Using Drush: Command `drush editorjs:d`
### Uninstallation

See this page (https://www.drupal.org/docs/extending-drupal/uninstalling-modules)
