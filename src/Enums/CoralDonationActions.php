<?php

namespace D4rk0snet\Donation\Enums;

enum CoralDonationActions : string
{
    case PENDING_DONATION = 'coraldonation_pending_donation';
    case DONATION_CREATED = 'coraldonation_new_donation';
}