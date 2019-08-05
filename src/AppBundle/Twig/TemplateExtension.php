<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Util\Formatter;
use AppBundle\Entity\PhoneCall;
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
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
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
     *
     * @throws NonUniqueResultException
     */
    public function canShowPhone(Lead $lead, User $user): bool
    {
        if ($lead->isOwner($user)) {
            return true;
        }

        $room = $lead->getRoom();

        if ($room) {
            if (!$room->isPlatformWarranty()) {
                if ($lead->getBuyer() === $user && ($lead->isReserved() || $lead->isSold())) {
                    return true;
                }
            }
        }

        if ($lead->getBuyer() === $user && $this->hasAnsweredPhoneCall($lead, $user)) {
            return true;
        }

        return false;
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
        if ($lead->isOwner($user)) {
            return false;
        }

        $room = $lead->getRoom();

        if ($room) {
            if (!$room->isPlatformWarranty()) {
                return false;
            }
        }

        if ($this->hasAnsweredPhoneCall($lead, $user)) {
            return false;
        }

        return true;
    }

    /**
     * @param Lead $lead
     * @param User $caller
     *
     * @return bool
     *
     * @throws NonUniqueResultException
     */
    private function hasAnsweredPhoneCall(Lead $lead, User $caller): bool
    {
        $phoneCall = $this->entityManager
            ->getRepository(PhoneCall::class)
            ->getAnsweredPhoneCallByLeadAndCaller($lead, $caller);

        return $phoneCall instanceof PhoneCall;
    }
}