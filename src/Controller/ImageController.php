<?php

namespace Drupal\editorjs\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Defines controller for image plugin.
 *
 * @see https://github.com/editor-js/image
 */
final class ImageController implements ContainerInjectionInterface {

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
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * ImageController constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $accountProxy
   *   The account proxy.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository
   *   The entity repository.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    FileSystemInterface $fileSystem,
    AccountProxyInterface $accountProxy,
    EntityRepositoryInterface $entityRepository,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    $this->fileSystem = $fileSystem;
    $this->accountProxy = $accountProxy;
    $this->entityRepository = $entityRepository;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('current_user'),
      $container->get('entity.repository'),
      $container->get('entity_type.manager')
    );
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
        'uuid' => $file->uuid(),
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
  public function uploadUrl(Request $request): JsonResponse {
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
        'uuid' => $file->uuid(),
      ],
    ];

    return new JsonResponse($result);
  }

  /**
   * Response for generate image style url.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   */
  public function styleUrl(Request $request): JsonResponse {
    $data = Json::decode($request->getContent());

    /** @var \Drupal\file\Entity\File $file */
    $file = $this->entityRepository
      ->loadEntityByUuid('file', $data['uuid']);
    if (!$file) {
      throw new BadRequestHttpException('File not found.');
    }
    /** @var \Drupal\image\Entity\ImageStyle $image_style */
    $image_style = $this
      ->entityTypeManager
      ->getStorage('image_style')
      ->load($data['image_style_id']);
    if (!$image_style) {
      throw new BadRequestHttpException('Image style not found.');
    }

    $url = $image_style->buildUrl($file->getFileUri());

    return new JsonResponse(['url' => $url]);
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
