<?php

namespace AppBundle\Twig;


use AppBundle\Entity\Lead;
use AppBundle\Entity\PhoneCall;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

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
            new \Twig_SimpleFunction('has_reserved_lead', [$this, 'hasReservedLead']),
            new \Twig_SimpleFunction('vue_var', [$this, 'vueVariable'])
        ];
    }

    /**
     * @param $phone
     *
     * @return string
     */
    public function hiddenPhone($phone): string
    {
        if (strlen($phone) === 3) {
            return sprintf('%s * *', substr($phone,0 , 1));
        }

        return sprintf(
            '+%s(%s)-%s-**-**',
            substr($phone, 0, 1),
            substr($phone, 1, 3),
            substr($phone, 4, 3)
        );
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     */
    public function dateFormat(\DateTime $dateTime): string
    {
        $day = date_format($dateTime, 'j');
        $month = date_format($dateTime, 'n');
        $year = date_format($dateTime, 'Y');

        switch ($month) {
            case 1:
                $month = "января";
                break;
            case 2:
                $month = "февраля";
                break;
            case 3:
                $month = "марта";
                break;
            case 4:
                $month = "апреля";
                break;
            case 5:
                $month = "мая";
                break;
            case 6:
                $month = "июня";
                break;
            case 7:
                $month = "июля";
                break;
            case 8:
                $month = "августа";
                break;
            case 9:
                $month = "сентябя";
                break;
            case 10:
                $month = "октября";
                break;
            case 11:
                $month = "ноября";
                break;
            case 12:
                $month = "декабря";
                break;
        }
        return sprintf("%s %s %s г.", $day, $month, $year);
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
     * @param null|string $phone
     *
     * @return string
     */
    public function humanPhone(?string $phone): string
    {
        if (!$phone) {
            return '';
        }

        $formattedPhone = sprintf('+%s(%s)%s-%s-%s',
            substr($phone, 0, 1),
            substr($phone, 1, 3),
            substr($phone, 5, 3),
            substr($phone, 8, 2),
            substr($phone, 10, 2)
        );

        return $formattedPhone;
    }

    /**
     * @param int $money
     *
     * @return string
     */
    public function moneyFormat(int $money): string
    {
        $result = intdiv($money, 100) . ' руб.';

        if ($end = $money % 100) {
            $result .= ' ' . $money % 100 . ' коп.';
        }

        return $result;
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
}