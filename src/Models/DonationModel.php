<?php

namespace D4rk0snet\Donation\Models;

use D4rk0snet\Coralguardian\Enums\Language;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use D4rk0snet\Donation\Enums\PaymentMethod;
use DateTime;

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

    private ?Datetime $date = null;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): DonationModel
    {
//        if ($amount < 1) {
//            throw new \Exception("Minimum amount value is 1");
//        }
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

    public function setPaymentMethod(string $paymentMethod): DonationModel
    {
        try {
            $this->paymentMethod = PaymentMethod::from($paymentMethod);
            return $this;
        } catch (\ValueError $exception) {
            throw new \Exception("PaymentMethod has not a valid value");
        }
    }

    public function setDate(?DateTime $date): DonationModel
    {
        $this->date = $date;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function toArray(): array
    {
        return [
            'customerUUID' => $this->getCustomerUUID(),
            'amount' => $this->getAmount(),
            'lang' => $this->getLang()->value,
            'donationRecurrency' => $this->getDonationRecurrency()->value,
            'paymentMethod' => $this->getPaymentMethod()->value
        ];
    }
}