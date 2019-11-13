<?php

namespace AppBundle\Controller\API\v1;

use AppBundle\Service\Uploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Route("/api/v1")
 */
class UploadController extends Controller
{
    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var int
     */
    private $uploadMaxSize;

    /**
     * @var array
     */
    private $audioAllowedTypes;

    /**
     * @var int
     */
    private $audioMaxSize;

    /**
     * @param Uploader           $uploader
     * @param ValidatorInterface $validator
     * @param int                $uploadMaxSize
     * @param array              $audioAllowedTypes
     * @param int                $audioMaxSize
     */
    public function __construct(
        Uploader $uploader,
        ValidatorInterface $validator,
        int $uploadMaxSize,
        array $audioAllowedTypes,
        int $audioMaxSize
    ) {
        $this->uploader = $uploader;
        $this->validator = $validator;
        $this->uploadMaxSize = $uploadMaxSize;
        $this->audioAllowedTypes = $audioAllowedTypes;
        $this->audioMaxSize = $audioMaxSize;
    }

    /**
     * @Route("/upload/logotype/{filter}", name="api_v1_upload_logotype", methods={"POST"})
     *
     * @param CacheManager $imagineCacheManager
     * @param Request      $request
     * @param string       $filter
     *
     * @return Response
     */
    public function postUploadImageAction(
        CacheManager $imagineCacheManager,
        Request $request,
        string $filter
    ): Response {

        if (!$request->files->has('uploader')) {
            return new JsonResponse(['error' => 'Файл не отправлен'], Response::HTTP_BAD_REQUEST);
        }

        $uploadedFile = $request->files->get('uploader');
        $constraint = new Image([
            'mimeTypes' => ['image/png', 'image/jpeg', 'image/pjpeg'],
            'mimeTypesMessage' => 'Поддерживаются только PNG и JPEG изображения',
            'maxSize' => $this->uploadMaxSize,
            'maxSizeMessage' => 'Максимальный размер загружаемого изображения должен быть {size}'
        ]);

        $violations = $this->validator->validate($uploadedFile, $constraint);

        if ($violations->count()) {
            return $this->responseErrors($violations);
        }

        try {
            $result = $this->uploader->store($uploadedFile, 'logotype');
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $url = $imagineCacheManager->getBrowserPath($result['path'], $filter);

        return new JsonResponse([
            'path' => $result['path'],
            'url' => $url
        ]);
    }

    /**
     * @Route("/upload/audio", name="api_v1_upload_audio", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postUploadAudioRecordAction(Request $request): Response
    {
        if (!$request->files->has('uploader')) {
            return new JsonResponse(['error' => 'Файл не отправлен']);
        }

        $uploadedFile = $request->files->get('uploader');

        $constraint = new File([
            'mimeTypes' => $this->audioAllowedTypes,
            'mimeTypesMessage' => 'Неподдерживается тип загружаемого файла',
            'maxSize' => $this->audioMaxSize,
            'maxSizeMessage' => 'Максимальный размер загружаемой аудио записи должен быть {size}'
        ]);

        $violations = $this->validator->validate($uploadedFile, $constraint);

        if ($violations->count()) {
            return $this->responseErrors($violations);
        }

        try {
            $result = $this->uploader->store($uploadedFile, 'audio');
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $url = $this->generateUrl('app_uploaded_path', [
            'directory' => 'audio',
            'filename' => $result['filename']]
        );

        return new JsonResponse([
            'path' => $result['path'],
            'url' => $url
        ]);
    }

    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return JsonResponse
     */
    private function responseErrors(ConstraintViolationListInterface $violations)
    {
        $errors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }
        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }
}