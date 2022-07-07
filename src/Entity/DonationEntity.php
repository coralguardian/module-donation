<?php

namespace D4rk0snet\Donation\Entity;

use D4rk0snet\Coralguardian\Entity\CustomerEntity;
use D4rk0snet\Coralguardian\Enums\Language;
use D4rk0snet\Donation\Enums\PaymentMethod;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="donation")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "uniqueDonation" = "\D4rk0snet\Donation\Entity\DonationEntity",
 *     "recurrentDonation" = "\D4rk0snet\Donation\Entity\RecurringDonationEntity",
 *     "regularAdoption" = "\D4rk0snet\Adoption\Entity\AdoptionEntity",
 *     "giftAdoption" = "\D4rk0snet\Adoption\Entity\GiftAdoption"
 * })
 */
class DonationEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary_ordered_time", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
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

    /**
     * @ORM\Column(type="string", enumType="\D4rk0snet\Donation\Enums\PaymentMethod")
     */
    private PaymentMethod $paymentMethod;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private bool $isPaid = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $fiscalReceiptNumber;

    public function __construct(
        CustomerEntity $customer,
        DateTime       $date,
        float          $amount,
        Language       $lang,
        bool           $isPaid,
        PaymentMethod  $paymentMethod
    ) {
        $this->customer = $customer;
        $this->date = $date;
        $this->amount = $amount;
        $this->lang = $lang;
        $this->isPaid = $isPaid;
        $this->paymentMethod = $paymentMethod;
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

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): DonationEntity
    {
        $this->isPaid = $isPaid;
        return $this;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): DonationEntity
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getFiscalReceiptNumber(): ?int
    {
        return $this->fiscalReceiptNumber;
    }

    public function setFiscalReceiptNumber(?int $fiscalReceiptNumber): DonationEntity
    {
        $this->fiscalReceiptNumber = $fiscalReceiptNumber;
        return $this;
    }
}
