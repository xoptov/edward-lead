<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Part\EnabledTrait;
use AppBundle\Entity\Part\UpdatedAtTrait;
use AppBundle\Entity\Part\IdentificatorTrait;

/**
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=8)
 * @ORM\DiscriminatorMap({
 *     "system" = "Account",
 *     "income" = "IncomeAccount",
 *     "outgoing" = "OutgoingAccount",
 *     "client" = "ClientAccount"
 * })
 * @ORM\HasLifecycleCallbacks
 */
class Account implements IdentifiableInterface
{
    const DIVISOR = 100;

    use IdentificatorTrait;

    use UpdatedAtTrait;

    use EnabledTrait;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="balance", type="bigint")
     */
    private $balance = 0;

    /**
     * @param string $type
     *
     * @return Account
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $description
     *
     * @return Account
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param integer $balance
     *
     * @return Account
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param int $amount
     */
    public function changeBalance(int $amount): void
    {
        $this->balance += $amount;
    }
}
