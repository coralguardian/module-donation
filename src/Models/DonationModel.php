<?php

namespace D4rk0snet\Donation\Models;

use D4rk0snet\CoralCustomer\Model\CustomerModel;
use D4rk0snet\Coralguardian\Enums\Language;
use D4rk0snet\CoralOrder\Enums\PaymentMethod;
use D4rk0snet\CoralOrder\Enums\Project;
use D4rk0snet\Donation\Enums\DonationRecurrencyEnum;
use DateTime;
use Exception;

class DonationModel implements \JsonSerializable
{
    /**
     * @required
     */
    private CustomerModel $customerModel;

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

    /**
     * @required
     */
    private Project $project;

    private ?Datetime $date = null;

    private bool $isPaid = false;

    private ?string $stripePaymentIntentId;

    private bool $isExtra = false;

    public function afterMapping()
    {
        if($this->amount < 1) {
            throw new Exception("Amount can not be < 1€");
        }
    }

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

    public function setDonationRecurrency(DonationRecurrencyEnum $donationRecurrency): DonationModel
    {
        $this->donationRecurrency = $donationRecurrency;

        return $this;
    }

    public function getLang(): Language
    {
        return $this->lang;
    }

    public function setLang(Language $lang): DonationModel
    {
        $this->lang = $lang;
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

    public function setDate(?DateTime $date): DonationModel
    {
        $this->date = $date;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function getCustomerModel(): CustomerModel
    {
        return $this->customerModel;
    }

    public function setCustomerModel(CustomerModel $customerModel): DonationModel
    {
        $this->customerModel = $customerModel;
        return $this;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): DonationModel
    {
        $this->isPaid = $isPaid;
        return $this;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): DonationModel
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;
        return $this;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     * @return DonationModel
     */
    public function setProject(Project $project): DonationModel
    {
        $this->project = $project;
        return $this;
    }

    public function isExtra(): bool
    {
        return $this->isExtra;
    }

    public function setIsExtra(bool $isExtra): DonationModel
    {
        $this->isExtra = $isExtra;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'customerModel' => $this->getCustomerModel(),
            'amount' => $this->getAmount(),
            'lang' => $this->getLang()->value,
            'donationRecurrency' => $this->getDonationRecurrency()->value,
            'paymentMethod' => $this->getPaymentMethod()->value,
            'isPaid' => $this->isPaid(),
            'project' => $this->project,
            'isExtra' => $this->isExtra
        ];
    }
}