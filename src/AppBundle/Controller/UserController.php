<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Company;
use AppBundle\Form\Type\CompanyType;
use AppBundle\Form\Type\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/select-type", name="app_select_type", methods={"GET"})
     * @return Response
     */
    public function selectTypeAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isTypeSelected()) {
            return $this->render('@App/User/select_type.html.twig');
        }

        if ($user->hasCompany()) {
            return $this->redirectToRoute('app_exchange');
        }

        return $this->redirectToRoute('app_exchange_my_leads');
    }

    /**
     * @Route("/creating/company", name="app_creating_company", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function creatingCompanyAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isTypeSelected()) {
            return new Response('Тип пользователя уже указан', Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(CompanyType::class, null, ['creating' => true]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                /** @var Company $company */
                $company = $form->getData();
                $company->setUser($user);
                $this->entityManager->persist($company);

                $user
                    ->removeRole('ROLE_WEBMASTER')
                    ->addRole('ROLE_COMPANY')
                    ->makeTypeSelected();

                $this->entityManager->flush();

                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('@App/User/company.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/stay/webmaster", name="app_stay_webmaster", methods={"GET"})
     *
     * @return Response
     */
    public function stayWebmasterAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isTypeSelected()) {
            return new Response('Тип пользователя уже указан', Response::HTTP_BAD_REQUEST);
        }

        $user
            ->removeRole('ROLE_COMPANY')
            ->addRole('ROLE_WEBMASTER')
            ->makeTypeSelected();

        $this->entityManager->flush();

        return $this->redirectToRoute('app_profile');
    }

    /**
     * @Route("/profile", name="app_profile", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function profileAction(Request $request): Response
    {
        $form = $this->createForm(ProfileType::class, $this->getUser());

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->entityManager->flush();
            }
        }

        return $this->render('@App/User/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }
}