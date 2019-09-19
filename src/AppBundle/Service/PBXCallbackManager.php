<?php

namespace AppBundle\Service;

use AppBundle\Entity\PBX\Callback;
use AppBundle\Entity\PBX\Shoulder;

class PBXCallbackManager
{
    /**
     * @var int
     */
    private $minTalkDuration;

    /**
     * @param int $minTalkDuration
     */
    public function __construct(int $minTalkDuration)
    {
        $this->minTalkDuration = $minTalkDuration;
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
            && $durationInSecond > $this->minTalkDuration
        ) {
            $pbxCallback->setStatus(Callback::STATUS_SUCCESS);
        } else {
            $pbxCallback->setStatus(Callback::STATUS_FAIL);
        }
    }
}