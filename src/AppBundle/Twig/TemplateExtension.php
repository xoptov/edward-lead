<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Util\Formatter;
use AppBundle\Entity\PhoneCall;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class TemplateExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('has_answered_phone_call', [$this, 'hasAnsweredPhoneCall'])
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
}