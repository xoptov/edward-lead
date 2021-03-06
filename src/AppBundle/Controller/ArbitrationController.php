<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Image;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Message;
use AppBundle\Service\Uploader;
use AppBundle\Form\Type\ReplayType;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use FOS\MessageBundle\Sender\SenderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\MessageBundle\Composer\ComposerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Image as ConstrainImage;

class ArbitrationController extends Controller
{
    /**
     * @var ComposerInterface
     */
    private $fosComposer;

    /**
     * @var SenderInterface
     */
    private $fosSender;

    /**
     * @var int
     */
    private $messageLimitInMinute;

    /**
     * @param ComposerInterface $composer
     * @param SenderInterface   $sender
     * @param int               $messageLimitInMinute
     */
    public function __construct(
        ComposerInterface $composer,
        SenderInterface $sender,
        int $messageLimitInMinute
    ) {
        $this->fosComposer = $composer;
        $this->fosSender = $sender;
        $this->messageLimitInMinute = $messageLimitInMinute;
    }

    /**
     * @Route("/arbitration", name="app_arbitration", methods={"GET"})
     */
    public function default() : Response
    {
        $provider = $this->get('fos_message.provider');

        $inboxThreads = $provider->getInboxThreads();
        $sentThreads = $provider->getSentThreads();

        $threads = array_unique(array_merge($inboxThreads, $sentThreads), SORT_REGULAR);

        usort($threads, function (Thread $a, Thread $b) {
            if ($a->getCreatedAt() == $b->getCreatedAt()) {
                return 0;
            }
            return ($a->getCreatedAt() > $b->getCreatedAt()) ? -1 : 1;
        });

        $user = $this->getUser();

        $openedThreads = array_filter($threads, function ($thread) use ($user) {
            /** @var Thread $thread */
            if ($thread->getStatus() !== Thread::STATUS_CLOSED) {
                return true;
            }
            return false;
        });

        $archiveThreads = array_filter($threads, function ($thread) use ($user) {
            /** @var Thread $thread */
            if ($thread->getStatus() === Thread::STATUS_CLOSED) {
                return true;
            }
            return false;
        });

        $form = $this->createForm(ReplayType::class);

        return $this->render("@App/Arbitration/default.html.twig", [
            'openedThreads' => $openedThreads,
            'archiveThreads' => $archiveThreads,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/arbitration/reply", name="app_arbitration_reply", methods={"POST"})
     *
     * @param Request                $request
     * @param CacheManager           $cacheManager
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function reply(
        Request $request,
        CacheManager $cacheManager,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $form = $this->createForm(ReplayType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $this->getUser();

        // todo: Нравится ли мне как тут я сделал? Нет конечно! Но! Блять я уже устал в пятницу вечером и хочу домой.
        try {
            $messageInTimeFrame = $entityManager->getRepository(Message::class)
                ->getCountInTimeFrameBySender($user, 60);

            if ($messageInTimeFrame >= $this->messageLimitInMinute) {
                return new JsonResponse([
                    'errors' => ['Вы слишком часто пишите сообщения. Подождите 10 минут']
                ], Response::HTTP_BAD_REQUEST);
            }

        } catch (DBALException $e) {
            return new JsonResponse(['errors' => ['Произошла ошибка при приёме сообщения']], Response::HTTP_BAD_REQUEST);
        }

        /** @var Message $message */
        $data = $form->getData();


        $messageBuilder = $this->fosComposer->reply($data['thread']);
        $messageBuilder->setBody($data['body']);
        $messageBuilder->setSender($user);
        $message = $messageBuilder->getMessage();

        /** @var Thread $thread */
        $thread = $message->getThread();
        $thread->setStatus(Thread::STATUS_WAIT_SUPPORT);

        if (!empty($data['images'])) {
            $message->setImages($data['images']);
        }

        $this->fosSender->send($message);

        $logotypePath = null;

        if ($user->isCompany()) {

            $company = $user->getCompany();
            $logotype = $company->getLogotype();

            if ($logotype) {
                $logotypePath = $cacheManager->getBrowserPath($logotype->getPath(), 'logotype_34x34');
            }
        }

        $images = [];

        /** @var Image $image */
        foreach ($message->getImages() as $image) {
            $images[] = [
                'id' => $image->getId(),
                'filename' => $image->getFilename(),
                'path' => $image->getPath()
            ];
        }

        return new JsonResponse([
            'target_in' => false,
            'target_out' => true,
            'sender' => 'Ваше сообщение',
            'body' => $message->getBody(),
            'time' => date_format($message->getCreatedAt(), 'd.m.Y H:m'),
            'logotype' => $logotypePath,
            'images' => $images
        ]);
    }

    /**
     * @Route("/arbitration/file", name="app_arbitration_file", methods={"POST"})
     *
     * @param Request $request
     * @param Uploader $uploader
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    public function fileUpload(Request $request, Uploader $uploader, ValidatorInterface $validator) : JsonResponse
    {
        if (!$request->files->has('file')) {
            return new JsonResponse(['errors' => ['Файл не отправлен']], Response::HTTP_BAD_REQUEST);
        }

        $file = $request->files->get('file');
        $constraint = new ConstrainImage([
            'mimeTypes' => ['image/png', 'image/jpeg'],
            'mimeTypesMessage' => 'Поддерживаются только PNG и JPEG изображения',
            'maxSize' => $this->getParameter('upload_max_size'),
            'maxSizeMessage' => 'Максимальный размер загружаемого изображения должен быть {size}'
        ]);

        $violations = $validator->validate($file, $constraint);

        if ($violations->count()) {
            $errors = [];
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $file = $uploader->store($file, Uploader::DIRECTORY_IMAGE);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $image = new Image();
        $image->setPath($file['path']);
        $image->setFilename($file['filename']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();

        return new JsonResponse(['id' => $image->getId(), 'name' => $file['filename']]);
    }

    /**
     * @Route("/arbitration/delete", name="app_arbitration_image_delete", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function fileDelete(Request $request) : JsonResponse
    {
        $id = $request->get('id', 0);
        $fileName = $request->get('fileName', '');

        $image = $this->getDoctrine()->getRepository(Image::class)->find($id);

        if (!$image) {
            return new JsonResponse(['errors' => ['Такого файла не существует!']], Response::HTTP_BAD_REQUEST);
        }

        if ($image->getFilename() != $fileName) {
            return new JsonResponse(['errors' => ['Вы не можете удалить этот файл!']], Response::HTTP_BAD_REQUEST);
        }

        $filename = $this->getParameter('upload_store_path')
            . DIRECTORY_SEPARATOR
            . Uploader::DIRECTORY_IMAGE
            . DIRECTORY_SEPARATOR
            . $fileName;

        $filesystem = new Filesystem();
        try {
            $filesystem->remove($filename);
            $this->getDoctrine()->getManager()->remove($image);
            $this->getDoctrine()->getManager()->flush();
        } catch (IOException $exception) {
            return new JsonResponse(['errors' => [$exception->getMessage()]], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['id' => $image->getId()]);
    }
}