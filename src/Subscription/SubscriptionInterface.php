<?php

namespace Icawebdesign\Hibp\Subscription;

use Icawebdesign\Hibp\HibpHttp;

interface SubscriptionInterface
{
    public function __construct(HibpHttp $hibpHttp);

    public function status(): SubscriptionStatusEntity;
}
