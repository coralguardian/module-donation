<?php

namespace D4rk0snet\Donation\Entity;

use D4rk0snet\Coralguardian\Entity\CustomerEntity;
use D4rk0snet\Coralguardian\Enums\Language;
use D4rk0snet\Donation\Enums\PaymentMethod;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @ORM\Table(name="donation_recurring")
 */
class RecurringDonationEntity extends DonationEntity
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $subscriptionId;

    public function __construct(
        CustomerEntity $customer,
        DateTime       $date,
        float          $amount,
        Language       $lang,
        PaymentMethod  $paymentMethod,
        bool           $isPaid)
    {
        parent::__construct(
            customer: $customer,
            date: $date,
            amount: $amount,
            lang: $lang,
            isPaid: $isPaid,
            paymentMethod: $paymentMethod
        );
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(?string $subscriptionId): RecurringDonationEntity
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }
}