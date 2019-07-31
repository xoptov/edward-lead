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
            new \Twig_SimpleFunction('has_reserved_lead', [$this, 'hasReservedLead']),
            new \Twig_SimpleFunction('has_answered_phone_call', [$this, 'hasAnsweredPhoneCall']),
            new \Twig_SimpleFunction('balance_hold', [$this, 'getBalanceHold']),
            new \Twig_SimpleFunction('vue_var', [$this, 'vueVariable']),
            new \Twig_SimpleFunction('can_show_phone', [$this, 'isCanShowPhone']),
            new \Twig_SimpleFunction('must_show_call_button', [$this, 'isMustShowCallButton'])
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
     * @param User $user
     *
     * @return bool
     *
     * @throws NonUniqueResultException
     */
    public function hasReservedLead(User $user)
    {
        $lead = $this->entityManager->getRepository('AppBundle:Lead')
            ->getByUserAndReserved($user);

        if (!$lead) {
            return false;
        }

        return $this->hasAnsweredPhoneCall($lead, $user);
    }

    /**
     * @param Lead $lead
     * @param User $caller
     *
     * @return bool
     *
     * @throws NonUniqueResultException
     */
    public function hasAnsweredPhoneCall(Lead $lead, User $caller): bool
    {
        $phoneCall = $this->entityManager
            ->getRepository(PhoneCall::class)
            ->getAnsweredPhoneCallByLeadAndCaller($lead, $caller);

        return $phoneCall instanceof PhoneCall;
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
    public function isCanShowPhone(Lead $lead, User $user): bool
    {
        $room = $lead->getRoom();

        if ($room) {
            if (!$room->isPlatformWarranty()) {
                return true;
            }
        }

        if ($this->hasAnsweredPhoneCall($lead, $user)) {
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
    public function isMustShowCallButton(Lead $lead, User $user): bool
    {
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
}