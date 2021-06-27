<?php

namespace Drupal\editorjs\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Bytes;
use Drupal\Component\Utility\Environment;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\Exception\FileException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Utility\Token;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Defines controller for image plugin.
 *
 * @see https://github.com/batkor/editorjs-dimage
 */
final class ImageController implements ContainerInjectionInterface {

  use StringTranslationTrait;

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
   * The stream wrapper manager.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerChannelFactory;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The token system.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

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
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $streamWrapperManager
   *   The stream wrapper manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   The logger factory.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Utility\Token $token
   *   The token system.
   */
  public function __construct(
    FileSystemInterface $fileSystem,
    AccountProxyInterface $accountProxy,
    EntityRepositoryInterface $entityRepository,
    EntityTypeManagerInterface $entityTypeManager,
    StreamWrapperManagerInterface $streamWrapperManager,
    ConfigFactoryInterface $configFactory,
    LoggerChannelFactoryInterface $loggerChannelFactory,
    MessengerInterface $messenger,
    Token $token
  ) {
    $this->fileSystem = $fileSystem;
    $this->accountProxy = $accountProxy;
    $this->entityRepository = $entityRepository;
    $this->entityTypeManager = $entityTypeManager;
    $this->streamWrapperManager = $streamWrapperManager;
    $this->configFactory = $configFactory;
    $this->loggerChannelFactory = $loggerChannelFactory;
    $this->messenger = $messenger;
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('current_user'),
      $container->get('entity.repository'),
      $container->get('entity_type.manager'),
      $container->get('stream_wrapper_manager'),
      $container->get('config.factory'),
      $container->get('logger.factory'),
      $container->get('messenger'),
      $container->get('token')
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
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function upload(Request $request): JsonResponse {
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadFile */
    $uploadFile = $request->files->get('image');
    if (!$uploadFile) {
      throw new BadRequestHttpException();
    }

    // There is always a file size limit due to the PHP server limit.
    $validators = [
      'file_validate_extensions' => [$this->allowExtension($request)],
      'file_validate_size' => [Bytes::toInt(Environment::getUploadMaxSize())],
    ];
    $directory = trim($request->headers->get('x-directory', ''), '/');
    if (!empty($directory)) {
      $directory .= '/';
    }
    $data = file_get_contents($uploadFile->getRealPath());
    $file = $this->saveData($data, 'public://' . $directory . $uploadFile->getClientOriginalName(), $validators);
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

    $validators = [
      'file_validate_extensions' => [$this->allowExtension($request)],
      'file_validate_size' => [Bytes::toInt(Environment::getUploadMaxSize())],
    ];
    $directory = trim($request->headers->get('x-directory', ''), '/');
    if (!empty($directory)) {
      $directory .= '/';
    }
    $parsed_url = UrlHelper::parse($url);
    $destination = "public://$directory";
    $destination .= basename($parsed_url['path']);
    $file = $this->saveData($data, $destination, $validators);
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
    if (empty($data['image_style_id'])) {
      return new JsonResponse(['url' => $file->createFileUrl()]);
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
   * @param array $validators
   *   The callback list for validate.
   * @param int $replace
   *   The replace behavior when the destination file already exists.
   *
   * @return \Drupal\file\FileInterface|false
   *   The file entity elsa false.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function saveData($data, $destination, array $validators = [], $replace = FileSystemInterface::EXISTS_REPLACE) {
    if (empty($destination)) {
      $destination = $this->configFactory->get('system.file')->get('default_scheme') . '://';
    }
    $destination = $this->token->replace($destination, [], ['clear' => TRUE]);
    if (!$this->streamWrapperManager->isValidUri($destination)) {
      $this->loggerChannelFactory->get('file')->notice('The data could not be saved because the destination %destination is invalid. This may be caused by improper use of file_save_data() or a missing stream wrapper.', ['%destination' => $destination]);
      $this->messenger->addError($this->t('The data could not be saved because the destination is invalid. More information is available in the system log.'));
      return FALSE;
    }

    try {
      $dir = $this->fileSystem->dirname($destination);
      $this->fileSystem->prepareDirectory($dir, FileSystemInterface::CREATE_DIRECTORY);
      $uri = $this->fileSystem->saveData($data, $destination, $replace);
      /** @var \Drupal\file\Entity\File $file */
      $file = File::create([
        'uri' => $uri,
        'uid' => $this->accountProxy->id(),
        'status' => 0,
      ]);
      // If we are replacing an existing file re-use its database record.
      // @todo Do not create a new entity in order to update it. See
      //   https://www.drupal.org/node/2241865.
      if ($replace == FileSystemInterface::EXISTS_REPLACE) {
        $existing_files = $this->entityTypeManager->getStorage('file')->loadByProperties(['uri' => $uri]);
        if (count($existing_files)) {
          $existing = reset($existing_files);
          $file->fid = $existing->id();
          $file->set('uuid', $existing->uuid());
          $file->setOriginalId($existing->id());
          $file->setFilename($existing->getFilename());
        }
      }
      // If we are renaming around an existing file (rather than a directory),
      // use its basename for the filename.
      elseif ($replace == FileSystemInterface::EXISTS_RENAME && is_file($destination)) {
        $file->setFilename($this->fileSystem->basename($destination));
      }

      $errors = file_validate($file, $validators);
      if (!empty($errors)) {
        foreach ($errors as $error) {
          $this->messenger->addError($error);
        }
        return FALSE;
      }
      $file->save();
      return $file;
    }
    catch (FileException $e) {
      return FALSE;
    }
  }

  /**
   * Returns allow extension from request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return string
   *   The allow extensions list.
   */
  protected function allowExtension(Request $request) {
    return $request->headers->get('allow-extensions', 'png gif jpg jpeg');
  }

}
