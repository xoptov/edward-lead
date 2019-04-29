<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Account;
use AppBundle\Entity\Company;
use AppBundle\Event\WithdrawEvent;
use AppBundle\Service\AccountManager;
use AppBundle\Service\UserManager;
use AppBundle\Entity\HistoryAction;
use AppBundle\Form\Type\CompanyType;
use AppBundle\Form\Type\ProfileType;
use AppBundle\Service\WithdrawManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use AppBundle\Exception\FinancialException;
use AppBundle\Form\Type\PasswordUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @var WithdrawManager
     */
    private $withdrawManager;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserManager              $userManager
     * @param AccountManager           $accountManager
     * @param WithdrawManager          $withdrawManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        UserManager $userManager,
        AccountManager $accountManager,
        WithdrawManager $withdrawManager
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
        $this->accountManager = $accountManager;
        $this->withdrawManager = $withdrawManager;
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

                $user->switchToCompany()->makeTypeSelected();

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

        $user->switchToWebmaster()->makeTypeSelected();

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
        $profileForm = $this->createForm(ProfileType::class, $this->getUser());

        if ($request->isMethod(Request::METHOD_POST)) {
            $profileForm->handleRequest($request);

            if ($profileForm->isValid()) {
                $this->entityManager->flush();

                $this->addFlash('success', 'Данные профиля успешно обновлены');
            }
        }

        $passwordForm = $this->createForm(PasswordUpdateType::class, null, [
            'action' => $this->generateUrl('app_profile_update_password')
        ]);

        return $this->render('@App/User/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }

    /**
     * @Route("/history/login", name="app_history_login", methods={"GET"})
     *
     * @return Response
     */
    public function historyAction(): Response
    {
        $historyLogins = $this->entityManager->getRepository('AppBundle:HistoryAction')
            ->getByUserAndActionInDescOrder($this->getUser(), HistoryAction::ACTION_LOGIN);

        return $this->render('@App/User/history_logins.html.twig', [
            'historyLogins' => $historyLogins
        ]);
    }

    /**
     * @Route("/profile/password/update", name="app_profile_update_password", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     *
     * @throws OptimisticLockException
     */
    public function updatePasswordAction(Request $request): Response
    {
        $passwordForm = $this->createForm(PasswordUpdateType::class);
        $passwordForm->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();

        if ($passwordForm->isValid()) {
            $data = $passwordForm->getData();
            $user->setPlainPassword($data['password']);
            $this->userManager->updateUser($user);

            $this->addFlash('success', 'Новый пароль успешно установлен');
        }

        $profileForm = $this->createForm(ProfileType::class, $user, [
            'action' => $this->generateUrl('app_profile')
        ]);

        return $this->render('@App/User/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }

    /**
     * @Route("/billing", name="app_billing", methods={"GET"})
     *
     * @return Response
     */
    public function billingAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $availableBalance = $this->accountManager->getAvailableBalance($user->getAccount(), Account::DIVISOR);
        $holdBalance = $this->accountManager->getHoldAmount($user->getAccount(), Account::DIVISOR);

        return $this->render('@App/User/billing.html.twig', [
            'availableBalance' => $availableBalance,
            'holdBalance' => $holdBalance
        ]);
    }

    /**
     * @Route("/withdraw", name="app_withdraw", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function withdrawAction(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $freeBalance = $this->accountManager->getAvailableBalance($user->getAccount());

        $builder = $this->createFormBuilder();
        $builder
            ->add('amount', MoneyType::class, [
                'divisor' => Account::DIVISOR,
                'currency' => 'RUB',
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => $freeBalance,
                        'message' => 'Недостаточно средств для вывода'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class);

        $form = $builder->getForm();

        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $withdraw = $this->withdrawManager->create($user, $data['amount'], false);
                } catch (FinancialException $e) {
                    $this->addFlash('error', $e->getMessage());

                    return $this->render('@App/User/withdraw.html.twig', [
                        'form' => $form->createView()
                    ]);
                }

                $this->eventDispatcher->dispatch(
                    WithdrawEvent::CREATED,
                    new WithdrawEvent($withdraw)
                );

                $this->entityManager->flush();

                $this->addFlash('success', 'Зявка на вывод принята');

                return $this->redirectToRoute('app_billing');
            }
        }

        return $this->render('@App/User/withdraw.html.twig', [
            'form' => $form->createView()
        ]);
    }
}