<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Lead;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueLeadValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueLead) {
            throw new UnexpectedTypeException($constraint, UniqueLead::class);
        }

        if (!$value instanceof Lead) {
            throw new UnexpectedTypeException($value, Lead::class);
        }

        $leads = $this->entityManager
            ->getRepository(Lead::class)
            ->getByPhoneAndWithNoFinishStatus($value->getPhone(), $value->getRoom());

        $violation = false;

        if ($value->getId()) {
            /** @var Lead $lead */
            foreach ($leads as $lead) {
                if ($lead->getId() === $value->getId()) {
                    continue;
                }
                $violation = true;
            }
        } elseif (count($leads)) {
            $violation = true;
        }

        if ($violation) {
            if ($value->hasRoom()) {
                $this->context
                    ->buildViolation($constraint->messageForRoom)
                    ->addViolation();
            } else {
                $this->context
                    ->buildViolation($constraint->messageForExchange)
                    ->addViolation();
            }
        }
    }
}