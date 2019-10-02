<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Invoice;
use AppBundle\Util\Formatter;
use AppBundle\Service\LeadManager;
use AppBundle\Service\AccountManager;
use AppBundle\Entity\MonetaryTransaction;

class TemplateExtension extends \Twig_Extension
{
    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @var LeadManager
     */
    private $leadManager;

    /**
     * @param AccountManager         $accountManager
     * @param LeadManager            $leadManager
     */
    public function __construct(
        AccountManager $accountManager,
        LeadManager $leadManager
    ) {
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
            new \Twig_SimpleFilter('human_phone', [$this, 'humanPhone']),
            new \Twig_SimpleFilter('human_duration', [$this, 'humanDuration'])
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
            new \Twig_SimpleFunction('source_of_money', [$this, 'getSourceOfMoney'])
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
     * @param int $seconds
     *
     * @return string
     */
    public function humanDuration(int $seconds): string
    {
        return Formatter::humanDuration($seconds);
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
    public function getSourceOfMoney(Invoice $invoice): string
    {
        $outcomeTransaction = reset($invoice->getOutcomeTransactions());

        /** @var MonetaryTransaction $outcomeTransaction */
        if ($outcomeTransaction) {
            $sourceAccount = $outcomeTransaction->getAccount();
            if ($sourceAccount->getDescription() === 'tinkoff-bank') {
                return 'Банковской картой';
            } elseif ($sourceAccount->getDescription() === 'tincoff-bank-uric') {
                return 'Банковский перевод';
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
}