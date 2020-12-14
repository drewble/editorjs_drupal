<?php

namespace Drupal\editorjs\Event;

/**
 * Contains events for EditorJs.
 */
final class EditorJsEvents {

  /**
   * The event name for parsing metadata from link.
   */
  public const LINK_FETCH = 'editorjs.link_fetch';

  /**
   * The event name for processing values post form submit.
   */
  public const FORM_SUBMIT = 'editorjs.form_submit';

}
