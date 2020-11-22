<?php

namespace Drupal\editorjs\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Defines controller for image plugin.
 *
 * @see https://github.com/editor-js/image
 */
class ImageController implements ContainerInjectionInterface {

  /**
   * The file system manager.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The account proxy.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $accountProxy;

  /**
   * ImageController constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system manager.
   */
  public function __construct(FileSystemInterface $fileSystem, AccountProxyInterface $accountProxy) {
    $this->fileSystem = $fileSystem;
    $this->accountProxy = $accountProxy;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('file_system'), $container->get('current_user'));
  }

  /**
   * Response for image upload.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function upload(Request $request): JsonResponse {
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadFile */
    $uploadFile = $request->files->get('image');
    if (!$uploadFile) {
      throw new BadRequestHttpException();
    }
    $openFile = $uploadFile->openFile();
    $openFile = $openFile->fread($openFile->getSize());
    if ($openFile === FALSE) {
      return new JsonResponse(['success' => FALSE]);
    }
    $file = $this->saveData($openFile, 'public://' . $uploadFile->getClientOriginalName());
    if (!$file) {
      return new JsonResponse(['success' => FALSE]);
    }
    $result = [
      'success' => TRUE,
      'file' => [
        'url' => $file->createFileUrl(FALSE),
      ],
    ];
    return new JsonResponse($result);
  }

  /**
   * Response for image upload by url.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function fetch(Request $request): JsonResponse {
    $data = Json::decode($request->getContent());
    if (empty($data['url'])) {
      throw new BadRequestHttpException();
    }
    $url = $data['url'];
    $data = file_get_contents($url);
    if ($data == FALSE) {
      return new JsonResponse(['success' => FALSE]);
    }
    $file = $this->saveData($data, 'public://' . basename($url));
    if (!$file) {
      return new JsonResponse(['success' => FALSE]);
    }

    $result = [
      'success' => TRUE,
      'file' => [
        'url' => $file->createFileUrl(FALSE),
      ],
    ];

    return new JsonResponse($result);
  }

  /**
   * Returns file entity after save.
   *
   * @param string $data
   *   A string containing the contents of the file.
   * @param string $destination
   *   A string containing the destination URI.
   * @param int $replace
   *   The replace behavior when the destination file already exists.
   *
   * @return \Drupal\file\FileInterface|false
   *   The file entity elsa false.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @todo Not use 'file_save_data' function or leave as is?
   */
  protected function saveData($data, $destination, $replace = FileSystemInterface::EXISTS_REPLACE) {
    $file = file_save_data($data, $destination, $replace);
    if (!$file) {
      return FALSE;
    }
    // Set permanent status after save parent entity.
    $file->setTemporary();
    $file->save();

    return $file;
  }

}
