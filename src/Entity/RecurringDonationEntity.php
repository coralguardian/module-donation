<?php

namespace D4rk0snet\Donation\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Entity
 * @ORM\Table(name="recurring_donation")
 */
class RecurringDonationEntity extends DonationEntity
{
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