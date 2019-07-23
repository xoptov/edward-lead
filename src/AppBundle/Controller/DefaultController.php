<?php

namespace AppBundle\Controller;

use AppBundle\Service\Uploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DefaultController extends Controller
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
     * @Route("/", name="app_index", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('@App/Default/index.html.twig');
    }

    /**
     * @Route("/upload/logotype/{filter}", name="app_upload_logotype", methods={"POST"})
     *
     * @param CacheManager $imagineCacheManager
     * @param Request      $request
     * @param string       $filter
     *
     * @return Response
     */
    public function uploadImageAction(
        CacheManager $imagineCacheManager,
        Request $request,
        string $filter
    ): Response {

        if (!$request->files->has('uploader')) {
            return new JsonResponse(['errors' => ['Файл не отправлен']], Response::HTTP_BAD_REQUEST);
        }

        $uploadedFile = $request->files->get('uploader');
        $constraint = new Image([
            'mimeTypes' => 'image/png',
            'mimeTypesMessage' => 'Поддерживаются только PNG изображения',
            'maxSize' => $this->uploadMaxSize,
            'maxSizeMessage' => 'Максимальный размер загружаемого изображения должен быть {size}'
        ]);

        $violations = $this->validator->validate($uploadedFile, $constraint);

        if ($violations->count()) {
            return $this->responseErrors($violations);
        }

        try {
            $result = $this->uploader->store($uploadedFile, 'logotype');
        } catch (FileException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $url = $imagineCacheManager->getBrowserPath($result['path'], $filter);

        return new JsonResponse([
            'path' => $result['path'],
            'url' => $url
        ]);
    }

    /**
     * @Route("/upload/audio", name="app_upload_audio", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function uploadAudioRecordAction(Request $request): Response
    {
        if (!$request->files->has('uploader')) {
            return new JsonResponse(['errors' => ['Файл не отправлен']]);
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
        } catch (FileException $e) {
            return new JsonResponse(['errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
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
