<?php

namespace D4rk0snet\Donation\Models;

use D4rk0snet\Coralguardian\Enums\Language;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Enums\PaymentMethod;

class DonationModel
{
    /**
     * @required
     */
    private string $customerUUID;

    /**
     * @required
     */
    private float $amount;

    /**
     * @required
     */
    private Language $lang;

    /**
     * @required
     */
    private DonationRecurrencyEnum $donationRecurrency;

    /**
     * @required
     */
    private PaymentMethod $paymentMethod;

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

    public function getCustomerUUID(): string
    {
        return $this->customerUUID;
    }

    public function setCustomerUUID(string $customerUUID): DonationModel
    {
        $this->customerUUID = $customerUUID;
        return $this;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): DonationModel
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }
}