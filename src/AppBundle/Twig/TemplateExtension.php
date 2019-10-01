<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\Lead;
use AppBundle\Entity\MonetaryTransaction;
use AppBundle\Entity\User;
use AppBundle\Util\Formatter;
use AppBundle\Service\LeadManager;
use AppBundle\Service\AccountManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class TemplateExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @var LeadManager
     */
    private $leadManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     * @param LeadManager            $leadManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        LeadManager $leadManager
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->leadManager = $leadManager;
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter("hidden_phone", [$this, "hiddenPhone"]),
            new \Twig_SimpleFilter("date_format", [$this, "dateFormat"]),
            new \Twig_SimpleFilter("money_format", [$this, "moneyFormat"]),
            new \Twig_SimpleFilter("money_source", [$this, "moneySource"]),
            new \Twig_SimpleFilter('human_phone', [$this, 'humanPhone'])
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('balance_hold', [$this, 'getBalanceHold']),
            new \Twig_SimpleFunction('vue_var', [$this, 'vueVariable']),
            new \Twig_SimpleFunction('can_show_phone', [$this, 'canShowPhone']),
            new \Twig_SimpleFunction('must_show_call_button', [$this, 'mustShowCallButton'])
        ];
    }

    /**
     * @param string $phone
     *
     * @return string
     */
    public function hiddenPhone(string $phone): string
    {
        return Formatter::hidePhoneNumber($phone);
    }

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    public function dateFormat(\DateTime $date): string
    {
        return Formatter::localizeDate($date);
    }

    /**
     * @param null|string $phone
     *
     * @return string
     */
    public function humanPhone(?string $phone): string
    {
        return Formatter::humanizePhone($phone);
    }

    /**
     * @param int $money
     *
     * @return string
     */
    public function moneyFormat(int $money): string
    {
        return Formatter::humanizeMoney($money);
    }

    /**
     * @param Invoice $invoice
     *
     * @return string
     */
    public function moneySource(Invoice $invoice): string
    {
        $transactions = $this->entityManager->getRepository(MonetaryTransaction::class)->findBy(['operation' => $invoice]);
        if ($transactions != null && count($transactions) > 0) {
            $accountOut = $transactions[0]->getAccount();
            if ($accountOut != null) {
                $descName = (string)$accountOut->getDescription();
                switch ($descName) {
                    case 'tincoff-bank':
                        return 'Банковской картой';
                    case 'tincoff-bank-uric':
                        return 'Банковский перевод';
                }
            }
        }
        return 'Ожидается оплата...';
    }

    /**
     * @param string $var
     *
     * @return string
     */
    public function vueVariable(string $var): string
    {
        return '{{'.$var.'}}';
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getBalanceHold(User $user): int
    {
        return $this->accountManager->getHoldAmount($user->getAccount());
    }

    /**
     * @param Lead $lead
     * @param User $user
     *
     * @return bool
     */
    public function canShowPhone(Lead $lead, User $user): bool
    {
        return $this->leadManager->isCanShowPhone($lead, $user);
    }

    /**
     * @param Lead $lead
     * @param User $user
     *
     * @return bool
     *
     * @throws NonUniqueResultException
     */
    public function mustShowCallButton(Lead $lead, User $user): bool
    {
        if ($lead->getStatus() !== Lead::STATUS_IN_WORK || $lead->isOwner($user)) {
            return false;
        }

        $room = $lead->getRoom();

        if ($room) {
            if (!$room->isPlatformWarranty()) {
                return false;
            }
        }

        if ($this->leadManager->hasAnsweredPhoneCall($lead, $user)) {
            return false;
        }

        return true;
    }
}