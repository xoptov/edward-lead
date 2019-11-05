<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use AppBundle\Entity\Invoice;
use AppBundle\Util\Formatter;
use AppBundle\Service\LeadManager;
use AppBundle\Service\TimerManager;
use AppBundle\Entity\Room\Schedule;
use AppBundle\Service\TradeManager;
use AppBundle\Service\AccountManager;
use AppBundle\Entity\MonetaryTransaction;

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
     * @var TimerManager
     */
    private $timerManager;

    /**
     * @param AccountManager $accountManager
     * @param TradeManager   $tradeManager
     * @param TimerManager   $timerManager
     */
    public function __construct(
        AccountManager $accountManager,
        TradeManager $tradeManager,
        TimerManager $timerManager
    ) {
        $this->accountManager = $accountManager;
        $this->tradeManager = $tradeManager;
        $this->timerManager = $timerManager;
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
            new \Twig_SimpleFilter('human_duration', [$this, 'humanDuration']),
            new \Twig_SimpleFilter('human_remain_time', [$this, 'humanRemainTime'])
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
            new \Twig_SimpleFunction('humanize_work_days', [$this, 'humanizeWorkDays']),
            new \Twig_SimpleFunction('can_show_timer', [$this, 'canShowTimer'])
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

    /**
     * @param int $workDays
     *
     * @return array
     */
    public function humanizeWorkDays(int $workDays): array
    {
        $maps = [
            Schedule::MONDAY    => 'пн',
            Schedule::TUESDAY   => 'вт',
            Schedule::WEDNESDAY => 'ср',
            Schedule::THURSDAY  => 'чт',
            Schedule::FRIDAY    => 'пт',
            Schedule::SATURDAY  => 'сб',
            Schedule::SUNDAY    => 'вс'
        ];

        $humanizedWorkDays = [];

        for ($x = 1; $x <= Schedule::SUNDAY; $x *= 2) {
            if ($workDays & $x) {
                $humanizedWorkDays[] = $maps[$x];
            }
        }

        return $humanizedWorkDays;
    }

    /**
     * @param \DateTime $endAt
     *
     * @return null|string
     */
    public function humanRemainTime(\DateTime $endAt): ?string
    {

        $now = $this->timerManager->createDateTime();
        $remainInSeconds = Formatter::intervalInSeconds($now, $endAt);

        return Formatter::humanTimerRemain($remainInSeconds);
    }

    public function canShowTimer(Lead $lead): bool
    {
        if (!$lead->isExpected()) {
            return false;
        }

        if (!$lead->isPlatformWarranty()) {
            return false;
        }

        if (!$lead->hasTimer()) {
            return false;
        }

        $endAt = $lead->getTimerEndAt();

        if (empty($endAt)) {
            return false;
        }

        $now = $this->timerManager->createDateTime();

        if ($now >= $endAt) {
            return false;
        }

        return true;
    }
}