<?php

namespace StubBundle\Controller;

use StubBundle\Form\Type\SipAccountType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/stub/sip")
 */
class SipController extends Controller
{
    /**
     * @Route("/account", name="stub_sip_create", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request): JsonResponse
    {
        $form = $this->createForm(SipAccountType::class, null, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse(
                ['errors' => ['Получено пустое тело запроса']],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(
                ['errors' => $errors],
                Response::HTTP_BAD_REQUEST
            );
        }

        $data = $form->getData();

        if (in_array($data['login'], [1, 2, 3])) {
            return new JsonResponse([
                'errors' => ['Учётная запись с таким login уже существует']
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'Учётная запись создана']);
    }

    /**
     * @Route("/account", name="stub_sip_status", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function statusAction(Request $request): JsonResponse
    {
        $form = $this->createForm(SipAccountType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse(
                ['errors' => ['Не указаны данные в запросе']],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $data = $form->getData();

        if (!in_array($data['login'], [1, 2, 3])) {
            return new JsonResponse(
                ['errors' => ['Учётная запись не найдена']],
                Response::HTTP_NOT_FOUND
            );
        }

        if ($data['login'] != $data['password']) {
            return new JsonResponse(
                ['errors' => ['Указан неверный пароль']],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $status = null;

        switch ($data['login']) {
            case 1:
                $status = 'waiting';
                break;
            case 2:
                $status = 'connected';
                break;
            case 3:
                $status = 'error';
                break;
        }

        return new JsonResponse(['status' => $status]);
    }

    /**
     * @Route("/account", name="stub_sip_delete", methods={"DELETE"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request): JsonResponse
    {
        $form = $this->createForm(SipAccountType::class, null, [
            'method' => Request::METHOD_DELETE,
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new JsonResponse(
                ['errors' => ['Не указаны данные в запросе']],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $data = $form->getData();

        if (!in_array($data['login'], [1, 2, 3])) {
            return new JsonResponse(
                ['errors' => ['Учётная запись не найдена']],
                Response::HTTP_NOT_FOUND
            );
        }

        if ($data['login'] != $data['password']) {
            return new JsonResponse(
                ['errors' => ['Указан неверный пароль']],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return new JsonResponse(['message' => 'Учётная запись удалена']);
    }
}
