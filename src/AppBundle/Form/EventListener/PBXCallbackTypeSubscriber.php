<?php

namespace AppBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class PBXCallbackTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $fieldsMap;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }

    /**
     * PBXCallbackTypeSubscriber constructor.
     *
     * @param array $fieldsMap
     */
    public function __construct(array $fieldsMap)
    {
        $this->propertyAccessor = new PropertyAccessor();
        $this->fieldsMap = $fieldsMap;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $mappedData = [];

        foreach ($this->fieldsMap as $source => $target) {
            if (!$this->propertyAccessor->isReadable($data, $source)) {
                continue;
            }
            $value = $this->propertyAccessor->getValue($data, $source);
            $this->propertyAccessor->setValue($mappedData, $target, $value);
        }

        $event->setData($mappedData);
    }
}