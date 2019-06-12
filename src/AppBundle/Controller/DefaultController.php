<?php

namespace AppBundle\Controller;

use AppBundle\Service\Uploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DefaultController extends Controller
{
    /**
     * @var int
     */
    private $uploadMaxSize;

    /**
     * @param int $uploadMaxSize
     */
    public function __construct(int $uploadMaxSize)
    {
        $this->uploadMaxSize = $uploadMaxSize;
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
     * @param ValidatorInterface $validator
     * @param Uploader           $uploader
     * @param CacheManager       $imagineCacheManager
     * @param Request            $request
     * @param string             $filter
     *
     * @return Response
     */
    public function uploadImageAction(
        ValidatorInterface $validator,
        Uploader $uploader,
        CacheManager $imagineCacheManager,
        Request $request,
        string $filter
    ): Response {
        if (!$request->files->has('logotype')) {
            return new JsonResponse(['messages' => ['Файл не отправлен']], Response::HTTP_BAD_REQUEST);
        }

        $uploadedFile = $request->files->get('logotype');
        $constraint = new Image([
            'mimeTypes' => 'image/png',
            'mimeTypesMessage' => 'Поддерживаются только PNG изображения',
            'maxSize' => $this->uploadMaxSize,
            'maxSizeMessage' => 'Максимальный размер загружаемого изображения должен быть {size}'
        ]);

        $violations = $validator->validate($uploadedFile, $constraint);

        if ($violations->count()) {
            $errors = [];
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $errors[] = [
                    'message' => $violation->getMessage()
                ];
            }
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        try {
            $path = $uploader->store($uploadedFile, 'logotype');
        } catch (FileException $e) {
            $errors = [
                ['message' => $e->getMessage()]
            ];
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        $cachePath = $imagineCacheManager->getBrowserPath($path, $filter);

        return new JsonResponse([
            'logotype' => [
                'path' => $path,
                'uri' => $cachePath
            ]
        ]);
    }
}
