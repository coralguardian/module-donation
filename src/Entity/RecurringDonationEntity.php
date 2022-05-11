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
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $setupIntentId;

    public function getSetupIntentId(): string
    {
        return $this->setupIntentId;
    }

    public function setSetupIntentId(string $setupIntentId): RecurringDonationEntity
    {
        $this->setupIntentId = $setupIntentId;
        return $this;
    }
}