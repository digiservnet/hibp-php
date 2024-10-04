<?php

namespace Icawebdesign\Hibp\Subscription;

use stdClass;
use Carbon\Carbon;
use RuntimeException;
use Icawebdesign\Hibp\Enums\SubscriptionTier;

class SubscriptionStatusEntity
{
    public readonly SubscriptionTier $tier;

    public readonly string $description;

    public readonly Carbon $expires;

    public readonly int $requestsPerSecond;

    public readonly int $domainSearchMaxBreachedAccounts;

    public function __construct(?stdClass $data = null)
    {
        if (null === $data) {
            throw new RuntimeException('Invalid subscription status data');
        }

        $tier = SubscriptionTier::tryFrom($data->SubscriptionName);

        if (null === $tier) {
            throw new RuntimeException('Invalid subscription tier');
        }

        $this->tier = $tier;
        $this->description = $data->Description;
        $this->expires = Carbon::parse($data->SubscribedUntil);
        $this->requestsPerSecond = $data->Rpm;
        $this->domainSearchMaxBreachedAccounts = $data->DomainSearchMaxBreachedAccounts;
    }
}
