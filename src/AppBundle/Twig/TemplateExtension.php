<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Invoice;
use AppBundle\Util\Formatter;
use AppBundle\Service\LeadManager;
use AppBundle\Service\TradeManager;
use AppBundle\Service\AccountManager;
use AppBundle\Entity\MonetaryTransaction;
use Doctrine\DBAL\DBALException;

class TemplateExtension extends \Twig_Extension
{
    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * @var TradeManager
     */
    private $tradeManager;

    /**
     * @param AccountManager $accountManager
     * @param TradeManager   $tradeManager
     */
    public function __construct(
        AccountManager $accountManager,
        TradeManager $tradeManager
    ) {
        $this->accountManager = $accountManager;
        $this->tradeManager = $tradeManager;
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
            new \Twig_SimpleFunction('can_show_phone', [$this, 'canShowPhone']),
            new \Twig_SimpleFunction('source_of_money', [$this, 'getSourceOfMoney']),
            new \Twig_SimpleFunction('final_price', [$this, 'getFinalPrice']),
            new \Twig_SimpleFunction('pending_amount', [$this, 'getAmountInPendingTrades'])
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
     * @param int|null $seconds
     *
     * @return string|null
     */
    public function humanDuration(?int $seconds): ?string
    {
        if (empty($seconds)) {
            return null;
        }

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
        $outcomeTransactions = $invoice->getOutcomeTransactions();
        $outcomeTransaction = reset($outcomeTransactions);

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
     * @param User $user
     *
     * @return int
     */
    public function getBalanceHold(User $user): int
    {
        return $this->accountManager->getHoldAmount($user->getAccount());
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getAmountInPendingTrades(User $user): int
    {
        try {
            return $this->tradeManager->getAmountInPendingTrades($user);
        } catch (DBALException $e) {
            return 0;
        }
    }

    /**
     * @param Lead $lead
     * @param User $user
     *
     * @return bool
     */
    public function canShowPhone(Lead $lead, User $user): bool
    {
        return LeadManager::isCanShowPhone($lead, $user);
    }

    /**
     * @param Lead $lead
     *
     * @return int
     */
    public function getFinalPrice(Lead $lead): int
    {
        return $this->tradeManager->calculateCostWithMarginWithFee($lead);
    }
}