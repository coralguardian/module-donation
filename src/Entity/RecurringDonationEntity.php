<?php

namespace D4rk0snet\Donation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @ORM\Table(name="donation_recurring")
 */
class RecurringDonationEntity extends DonationEntity
{
    /**
     * @ORM\Column(type="string")
     */
    private string $subscriptionId;

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): RecurringDonationEntity
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }
}