<?php

namespace Tests\unit\AppBundle\Form\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use AppBundle\Form\EventListener\PBXCallbackTypeSubscriber;

class PBXCallbackTypeSubscriberTest extends TestCase
{
    public function testPreSubmit_firstCase()
    {
        $data = [
            'event' => 'hangup',
            'call1_phone' => '79883310019',
            'call1_billsec' => '0',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1567240573',
            'call1_answer_at' => '',
            'call1_hangup_at' => '1567240603',
            'call1_status' => 'cancel',
            'recording' => 'http://159.253.123.139:82/2019/08/31/force-79883310019-unknown-20190831-123613-1567240573.2940.wav',
            'call_id' => '1567240573.2939'
        ];

        $form = $this->createMock(Form::class);

        /** @var FormInterface $form */
        $event = new FormEvent($form, $data);

        $fieldsMap = [
            '[call_id]'         => '[phoneCall]',
            '[event]'           => '[event]',
            '[recording]'       => '[audioRecord]',
            '[call1_phone]'     => '[firstShoulder][phone]',
            '[call1_billsec]'   => '[firstShoulder][billSec]',
            '[call1_tarif]'     => '[firstShoulder][tariff]',
            '[call1_start_at]'  => '[firstShoulder][startAt]',
            '[call1_answer_at]' => '[firstShoulder][answerAt]',
            '[call1_hangup_at]' => '[firstShoulder][hangupAt]',
            '[call1_status]'    => '[firstShoulder][status]',
            '[call2_phone]'     => '[secondShoulder][phone]',
            '[call2_billsec]'   => '[secondShoulder][billSec]',
            '[call2_tarif]'     => '[secondShoulder][tariff]',
            '[call2_start_at]'  => '[secondShoulder][startAt]',
            '[call2_answer_at]' => '[secondShoulder][answerAt]',
            '[call2_hangup_at]' => '[secondShoulder][hangupAt]',
            '[call2_status]'    => '[secondShoulder][status]'
        ];

        $pbxCallbackTypeSubscriber = new PBXCallbackTypeSubscriber($fieldsMap);

        $pbxCallbackTypeSubscriber->preSubmit($event);

        $result = $event->getData();

        $this->assertArrayHasKey('phoneCall', $result);
        $this->assertEquals('1567240573.2939', $result['phoneCall']);

        $this->assertArrayHasKey('event', $result);
        $this->assertEquals('hangup', $result['event']);

        $this->assertArrayHasKey('audioRecord', $result);
        $this->assertEquals('http://159.253.123.139:82/2019/08/31/force-79883310019-unknown-20190831-123613-1567240573.2940.wav', $result['audioRecord']);

        $this->assertArrayHasKey('firstShoulder', $result);
        $this->assertArraySubset(
            [
                'phone'    => '79883310019',
                'billSec'  => '0',
                'tariff'   => 'mobile',
                'startAt'  => '1567240573',
                'answerAt' => '',
                'hangupAt' => '1567240603',
                'status'   => 'cancel'
            ],
            $result['firstShoulder']
        );
    }

    public function testPreSubmit_thirdCase()
    {
        $data = [
            'event' => 'hangup',
            'call1_phone' => '79883310019',
            'call1_billsec' => '43',
            'call1_tarif' => 'mobile',
            'call1_start_at' => '1567241916',
            'call1_answer_at' => '1567241925',
            'call1_hangup_at' => '1567241935',
            'call1_status' => 'answer',
            'call2_phone' => '79892969151',
            'call2_billsec' => '0',
            'call2_tarif' => 'mobile',
            'call2_start_at' => '1567241926',
            'call2_answer_at' => '',
            'call2_hangup_at' => '1567241934',
            'call2_status' => 'cancel',
            'recording' => 'http://159.253.123.139:82/2019/08/31/force-79883310019-unknown-20190831-125826-1567241906.2958',
            'call_id' => '1567241906.2957'
        ];

        $form = $this->createMock(Form::class);

        /** @var FormInterface $form */
        $event = new FormEvent($form, $data);

        $fieldsMap = [
            '[call_id]'         => '[phoneCall]',
            '[event]'           => '[event]',
            '[recording]'       => '[audioRecord]',
            '[call1_phone]'     => '[firstShoulder][phone]',
            '[call1_billsec]'   => '[firstShoulder][billSec]',
            '[call1_tarif]'     => '[firstShoulder][tariff]',
            '[call1_start_at]'  => '[firstShoulder][startAt]',
            '[call1_answer_at]' => '[firstShoulder][answerAt]',
            '[call1_hangup_at]' => '[firstShoulder][hangupAt]',
            '[call1_status]'    => '[firstShoulder][status]',
            '[call2_phone]'     => '[secondShoulder][phone]',
            '[call2_billsec]'   => '[secondShoulder][billSec]',
            '[call2_tarif]'     => '[secondShoulder][tariff]',
            '[call2_start_at]'  => '[secondShoulder][startAt]',
            '[call2_answer_at]' => '[secondShoulder][answerAt]',
            '[call2_hangup_at]' => '[secondShoulder][hangupAt]',
            '[call2_status]'    => '[secondShoulder][status]'
        ];

        $pbxCallbackTypeSubscriber = new PBXCallbackTypeSubscriber($fieldsMap);

        $pbxCallbackTypeSubscriber->preSubmit($event);

        $result = $event->getData();

        $this->assertArrayHasKey('phoneCall', $result);
        $this->assertEquals('1567241906.2957', $result['phoneCall']);

        $this->assertArrayHasKey('event', $result);
        $this->assertEquals('hangup', $result['event']);

        $this->assertArrayHasKey('audioRecord', $result);
        $this->assertEquals('http://159.253.123.139:82/2019/08/31/force-79883310019-unknown-20190831-125826-1567241906.2958', $result['audioRecord']);

        $this->assertArrayHasKey('firstShoulder', $result);
        $this->assertArraySubset(
            [
                'phone'    => '79883310019',
                'billSec'  => '43',
                'tariff'   => 'mobile',
                'startAt'  => '1567241916',
                'answerAt' => '1567241925',
                'hangupAt' => '1567241935',
                'status'   => 'answer'
            ],
            $result['firstShoulder']
        );

        $this->assertArrayHasKey('secondShoulder', $result);
        $this->assertArraySubset(
            [
                'phone'    => '79892969151',
                'billSec'  => '0',
                'tariff'   => 'mobile',
                'startAt'  => '1567241926',
                'answerAt' => '',
                'hangupAt' => '1567241934',
                'status'   => 'cancel'
            ],
            $result['secondShoulder']
        );
    }
}
