<?php

namespace D4rk0snet\Donation\Models;

use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Enums\Language;

class DonationModel
{
    /**
     * @required
     */
    private string $firstname;

    /**
     * @required
     */
    private string $lastname;

    /**
     * @required
     */
    private string $address;

    /**
     * @required
     */
    private string $postalCode;

    /**
     * @required
     */
    private string $city;

    /**
     * @required
     */
    private string $country;

    /**
     * @required
     */
    private string $email;

    /**
     * @required
     */
    private float $amount;

    /**
     * @required
     */
    private Language $lang;


    private string $stripePaymentMethodId;


    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): DonationModel
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): DonationModel
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): DonationModel
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): DonationModel
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): DonationModel
    {
        $this->country = $country;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): DonationModel
    {
        $this->email = $email;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): DonationModel
    {
        if($amount < 1) {
            throw new \Exception("Minimum amount value is 1");
        }
        $this->amount = $amount;
        return $this;
    }

    public function getDonationRecurrency(): DonationRecurrencyEnum
    {
        return $this->donationRecurrency;
    }

    public function setDonationRecurrency(string $donationRecurrency): DonationModel
    {
        try {
            $this->donationRecurrency = DonationRecurrencyEnum::from($donationRecurrency);
            return $this;
        } catch (\ValueError $exception) {
            throw new \Exception("Donation recurrency is not a valid one");
        }
    }

    public function getLang(): Language
    {
        return $this->lang;
    }

    public function setLang(string $lang): DonationModel
    {
        try {
            $this->lang = Language::from($lang);
            return $this;
        } catch (\ValueError $exception) {
            throw new \Exception("Language has not a valid value");
        }
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): DonationModel
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getStripePaymentMethodId(): string
    {
        return $this->stripePaymentMethodId;
    }
}