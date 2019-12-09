<?php

namespace NotificationBundle\Constants;

use NotificationBundle\Channels\EmailChannel;
use NotificationBundle\Channels\TelegramChannel;
use NotificationBundle\Channels\WebPushChannel;

class Cases
{
    const NAME_USER_API_TOKEN_CHANGED = 'NAME_USER_API_TOKEN_CHANGED';
    const NAME_LEAD_NEW_PLACED = 'NAME_LEAD_NEW_PLACED';
    const NAME_LEAD_EXPECT_TOO_LONG = 'NAME_LEAD_EXPECT_TOO_LONG';
    const NAME_LEAD_IN_WORK_TOO_LONG = 'NAME_LEAD_IN_WORK_TOO_LONG';

    const CASE_CHANNELS = [

        self::NAME_USER_API_TOKEN_CHANGED => [
            EmailChannel::NAME,
        ],
        self::NAME_LEAD_NEW_PLACED => [
            EmailChannel::NAME,
            TelegramChannel::NAME,
            WebPushChannel::NAME,
        ],
        self::NAME_LEAD_EXPECT_TOO_LONG => [
            TelegramChannel::NAME,
            WebPushChannel::NAME,
        ],
        self::NAME_LEAD_IN_WORK_TOO_LONG => [
            TelegramChannel::NAME,
            WebPushChannel::NAME,
        ],

    ];

    /**
     * @return array
     */
    static public function getCases(): array
    {
        return [
            self::NAME_USER_API_TOKEN_CHANGED,
            self::NAME_LEAD_NEW_PLACED,
            self::NAME_LEAD_EXPECT_TOO_LONG,
            self::NAME_LEAD_IN_WORK_TOO_LONG,
        ];
    }
}