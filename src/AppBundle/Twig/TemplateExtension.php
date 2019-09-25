<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Lead;
use AppBundle\Entity\Trade;
use AppBundle\Entity\User;
use AppBundle\Util\Formatter;
use AppBundle\Service\LeadManager;
use AppBundle\Service\AccountManager;
use Doctrine\ORM\EntityManagerInterface;

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
     * @var int
     */
    private $maxAsksCallback;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AccountManager         $accountManager
     * @param LeadManager            $leadManager
     * @param int                    $maxAsksCallback
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountManager $accountManager,
        LeadManager $leadManager,
        int $maxAsksCallback
    ) {
        $this->entityManager = $entityManager;
        $this->accountManager = $accountManager;
        $this->leadManager = $leadManager;
        $this->maxAsksCallback = $maxAsksCallback;
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
            new \Twig_SimpleFunction('can_show_call_button', [$this, 'canShowCallButton'])
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
     */
    public function canShowCallButton(Lead $lead, User $user): bool
    {
        $trade = $lead->getTrade();

        if ($trade && $trade->getBuyer() === $user) {

            $lastPhoneCall = $trade->getLastPhoneCall();

            if ($trade->getStatus() === Trade::STATUS_NEW && !$lastPhoneCall) {
                return true;
            }

            if ($trade->getStatus() === Trade::STATUS_CALL_BACK
                && $lastPhoneCall
                && !$trade->hasAskCallbackPhoneCall($lastPhoneCall)
                && $trade->getAskCallbackCount() < $this->maxAsksCallback
            ){
                return true;
            }
        }

        return false;
    }
}