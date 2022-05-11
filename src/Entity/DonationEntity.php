<?php

namespace D4rk0snet\Donation\Entity;

use D4rk0snet\Donation\Enums\Language;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

/**
 * @Entity
 * @ORM\Table(name="donation")
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
     * @ORM\Column(type="string")
     */
    private string $firstname;

    /**
     * @ORM\Column(type="string")
     */
    private string $lastname;

    /**
     * @ORM\Column(type="string")
     */
    private string $address;

    /**
     * @ORM\Column(type="string")
     */
    private string $city;

    /**
     * @ORM\Column(type="string")
     */
    private string $country;

    /**
     * @ORM\Column(type="string")
     */
    private string $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $donationStart;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $stripePaymentIntentId;

    /**
     * @ORM\Column(type="integer")
     */
    private int $amount;

    /**
     * @ORM\Column(type="string", enumType="\D4rk0snet\Adoption\Enums\Language")
     */
    private Language $lang;

    public function __construct(string $firstname,
                                string $lastname,
                                string $address,
                                string $city,
                                string $country,
                                string $email,
                                DateTime $donationStart,
                                int $amount,
                                Language $lang)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->address = $address;
        $this->city = $city;
        $this->country = $country;
        $this->email = $email;
        $this->donationStart = $donationStart;
        $this->amount = $amount;
        $this->lang = $lang;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDonationStart(): DateTime
    {
        return $this->donationStart;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getLang(): Language
    {
        return $this->lang;
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
}