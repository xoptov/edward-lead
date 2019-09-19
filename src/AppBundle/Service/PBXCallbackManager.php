<?php

namespace AppBundle\Service;

use AppBundle\Entity\PBX\Callback;
use AppBundle\Entity\PBX\Shoulder;

class PBXCallbackManager
{
    /**
     * @var int
     */
    private $minimumTalkDuration;

    /**
     * @param int $minimumTalkDuration
     */
    public function __construct(int $minimumTalkDuration)
    {
        $this->minimumTalkDuration = $minimumTalkDuration;
    }

    /**
     * @param Callback $pbxCallback
     */
    public function process(Callback $pbxCallback): void
    {
        $secondShoulder = $pbxCallback->getSecondShoulder();
        $durationInSecond = $secondShoulder->getDurationInSecond();

        if (
            $secondShoulder->getStatus() === Shoulder::STATUS_ANSWER
            && $durationInSecond > $this->minimumTalkDuration
        ) {
            $pbxCallback->setStatus(Callback::STATUS_SUCCESS);
        } else {
            $pbxCallback->setStatus(Callback::STATUS_FAIL);
        }
    }
}