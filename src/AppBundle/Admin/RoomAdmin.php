<?php

namespace AppBundle\Admin;

use AppBundle\Entity\City;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Region;
use AppBundle\Entity\Account;
use AppBundle\Service\RoomManager;
use AppBundle\Form\Type\ScheduleType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Form\Type\RoomChannelType;
use Symfony\Component\Form\FormInterface;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use AppBundle\Admin\Field\MoneyFieldDescription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RoomAdmin extends AbstractAdmin
{
    /**
     * @var RoomManager
     */
    private $roomManager;

    /**
     * @inheritdoc
     */
    public function __construct(string $code, string $class, string $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->formOptions = [
            'validation_groups' => function(FormInterface $form) {
                $groups = ['Default'];
                $data = $form->getData();
                /** @var Room $data */
                if ($data->isTimer()) {
                    $groups[] = 'timer';
                }
                if ($data->isPublicOffer()) {
                    $groups[] = 'offer';
                }
                return $groups;
            }
        ];
    }

    /**
     * @param RoomManager $roomManager
     */
    public function setRoomManager(RoomManager $roomManager)
    {
        $this->roomManager = $roomManager;
    }

    /**
     * @param Room $object
     *
     * @throws \Exception
     */
    public function prePersist($object)
    {
        $owner = $object->getOwner();
        $this->roomManager->joinInRoom($object, $owner);
        $this->roomManager->updateInviteToken($object);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $leadPrice = new MoneyFieldDescription();
        $leadPrice->setName('leadPrice');

        $filter
            ->add('id')
            ->add('owner.name')
            ->add('sphere')
            ->add('platformWarranty')
            ->add($leadPrice)
            ->add('enabled');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $list)
    {
        $leadPrice = new MoneyFieldDescription();
        $leadPrice->setName('leadPrice');

        $hiddenMargin = new MoneyFieldDescription();
        $hiddenMargin->setName('hiddenMargin');

        $list
            ->addIdentifier('id')
            ->add('name')
            ->add('sphere')
            ->add('leadCriteria')
            ->add('platformWarranty')
            ->add('timer')
            ->add($leadPrice)
            ->add('buyerFee', null, [
                'template' => '@App/CRUD/list_buyer_fee.html.twig'
            ])
            ->add($hiddenMargin)
            ->add('hideFee')
            ->add('enabled')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('Main')
                ->add('name')
                ->add('owner', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'to_string_callback' => function(User $entity) {
                        return $entity->getId() . ' : ' . $entity->getName();
                    }
                ])
                ->add('sphere')
                ->add('leadCriteria')
                ->add('leadPrice', MoneyType::class, [
                    'currency' => 'RUB',
                    'divisor' => Account::DIVISOR,
                    'required' => false
                ])
                ->add('platformWarranty', null, [
                    'required' => false
                ])
                ->add('buyerFee')
                ->add('hiddenMargin', MoneyType::class, [
                    'currency' => 'RUB',
                    'divisor' => Account::DIVISOR,
                    'required' => false
                ])
                ->add('hideFee')
                ->add('enabled')
            ->end()
            ->with('Timer')
                ->add('timer', null, [
                    'required' => false
                ])
                ->add('city', EntityType::class, [
                    'class' => City::class,
                    'choice_label' => 'name',
                    'required' => false
                ])
                ->add('schedule', ScheduleType::class, [
                    'required' => false
                ])
                ->add('executionHours', null, [
                    'required' => false
                ])
                ->add('leadsPerDay', null, [
                    'required' => false
                ])
            ->end()
            ->with('Offer')
                ->add('publicOffer')
                ->add('channels', CollectionType::class, [
                    'entry_type' => RoomChannelType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => false
                ])
                ->add('regions', ModelType::class, [
                    'class' => Region::class,
                    'property' => 'name',
                    'multiple' => true,
                    'btn_add' => false,
                    'btn_delete' => false,
                    'required' => false
                ])
                ->add('cities', ModelType::class, [
                    'class' => City::class,
                    'property' => 'name',
                    'multiple' => true,
                    'btn_add' => false,
                    'btn_delete' => false,
                    'required' => false
                ])
            ->end();
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $leadPrice = new MoneyFieldDescription();
        $leadPrice->setName('leadPrice');

        $hiddenMargin = new MoneyFieldDescription();
        $hiddenMargin->setName('hiddenMargin');

        $show
            ->with('Main')
                ->add('name')
                ->add('owner.name')
                ->add('sphere')
                ->add('leadCriteria')
                ->add('platformWarranty')
                ->add($leadPrice)
                ->add('buyerFee', null, [
                    'template' => '@App/CRUD/show_buyer_fee.html.twig'
                ])
                ->add($hiddenMargin)
                ->add('hideFee')
                ->add('timer')
                ->add('enabled')
            ->end();

        if ($this->subject instanceof Room) {
            $show
                ->ifTrue($this->subject->isTimer())
                    ->with('Timer')
                        ->add('city.name', null, [
                            'label' => 'Work City'
                        ])
                        ->add('schedule.workTime.startAt', 'datetime', [
                            'label' => 'Work Start At',
                            'format' => 'H:i'
                        ])
                        ->add('schedule.workTime.endAt', 'datetime', [
                            'label' => 'Work End At',
                            'format' => 'H:i'
                        ])
                        ->add('schedule.workDays', null, [
                            'label' => 'Work Days',
                            'template' => '@App/CRUD/show_work_days_field.html.twig'
                        ])
                        ->add('executionHours')
                        ->add('leadsPerDay')
                    ->end()
                ->ifEnd()
                ->ifTrue($this->subject->isPublicOffer())
                    ->with('Offer')
                        ->add('publicOffer')
                        ->add('channels', null, [
                            'template' => '@App/CRUD/show_room_channels.html.twig'
                        ])
                        ->add('regions', null, [
                            'associated_property' => 'name'
                        ])
                        ->add('cities', null, [
                            'associated_property' => 'name'
                        ])
                    ->end()
                ->ifEnd();
        }
    }

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'edit', 'list', 'show']);
    }
}