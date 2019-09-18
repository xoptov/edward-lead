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
        $intervalInSeconds = 0;

        if (
            $secondShoulder->getAnswerAt() instanceof \DateTime
            && $secondShoulder->getHangupAt() instanceof \DateTime
        ) {
            $answerAt = $secondShoulder->getAnswerAt();
            $hangupAt = $secondShoulder->getHangupAt();

            $diff = $answerAt->diff($hangupAt);

            $intervalInSeconds = $diff->s * $diff->i * $diff->h;
        }

        if (
            $secondShoulder->getStatus() === Shoulder::STATUS_ANSWER
            && $intervalInSeconds > $this->minimumTalkDuration
        ) {
            $pbxCallback->setStatus(Callback::STATUS_SUCCESS);
        } else {
            $pbxCallback->setStatus(Callback::STATUS_FAIL);
        }
    }
}