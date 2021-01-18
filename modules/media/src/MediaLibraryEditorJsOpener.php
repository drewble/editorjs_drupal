<?php

namespace Drupal\editorjs_media;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\editorjs_media\Ajax\EditorJsDialogSave;
use Drupal\media_library\MediaLibraryOpenerInterface;
use Drupal\media_library\MediaLibraryState;

/**
 * The media library opener for EditorsJs.
 */
class MediaLibraryEditorJsOpener implements MediaLibraryOpenerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The MediaLibraryEditorOpener constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function checkAccess(MediaLibraryState $state, AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'allow media image tool');
  }

  /**
   * {@inheritdoc}
   */
  public function getSelectionResponse(MediaLibraryState $state, array $selected_ids) {
    /** @var \Drupal\media\Entity\Media $selected_media */
    $selected_media = $this
      ->entityTypeManager
      ->getStorage('media')
      ->load(reset($selected_ids));

    $response = new AjaxResponse();
    $values = [
      'uuid' => $selected_media->uuid(),
      'url' => FALSE,
    ];

    $fid = $selected_media->getSource()->getSourceFieldValue($selected_media);
    if ($fid) {
      /** @var \Drupal\file\Entity\File $file */
      $file = $this
        ->entityTypeManager
        ->getStorage('file')
        ->load($fid);
      if ($file) {
        $values['url'] = $file->createFileUrl();
        $values['file_uuid'] = $file->uuid();
      }
    }

    $response->addCommand(new EditorJsDialogSave($values));

    return $response;
  }

}
