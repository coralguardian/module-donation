<?php

namespace D4rk0snet\Donation\Entity;

use D4rk0snet\Coralguardian\Entity\CustomerEntity;
use D4rk0snet\Coralguardian\Enums\Language;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;

/**
 * @Entity
 * @ORM\Table(name="donation")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({
 *     "uniqueDonation" = "\D4rk0snet\Donation\Entity\DonationEntity",
 *     "recurrentDonation" = "\D4rk0snet\Donation\Entity\RecurringDonationEntity",
 *     "regularAdoption" = "\D4rk0snet\Adoption\Entity\AdoptionEntity"
 * })
 */
class DonationEntity
{
    /**
     * @Id
     * @Column(type="uuid_binary_ordered_time", unique=true)
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
     */
    private $uuid;

    /**
     * @ORM\ManyToOne(targetEntity="\D4rk0snet\Coralguardian\Entity\CustomerEntity")
     * @ORM\JoinColumn(name="customer", referencedColumnName="uuid")
     */
    private CustomerEntity $customer;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $date;

    /**
     * @ORM\Column(type="float")
     */
    private float $amount;

    /**
     * @ORM\Column(type="string", enumType="\D4rk0snet\Coralguardian\Enums\Language")
     */
    private Language $lang;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $stripePaymentIntentId;

    public function __construct(
        CustomerEntity $customer,
        DateTime       $date,
        float          $amount,
        Language       $lang
    ) {
        $this->customer = $customer;
        $this->date = $date;
        $this->amount = $amount;
        $this->lang = $lang;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(CustomerEntity $customer): DonationEntity
    {
        $this->customer = $customer;
        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): DonationEntity
    {
        $this->date = $date;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): DonationEntity
    {
        $this->amount = $amount;
        return $this;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): DonationEntity
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;
        return $this;
    }

    public function getLang(): Language
    {
        return $this->lang;
    }

    public function setLang(Language $lang): DonationEntity
    {
        $this->lang = $lang;
        return $this;
    }
}
