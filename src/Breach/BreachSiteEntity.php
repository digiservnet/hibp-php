<?php

namespace Icawebdesign\Hibp\Breach;

use stdClass;
use Carbon\Carbon;
use RuntimeException;
use Illuminate\Support\Collection;

class BreachSiteEntity
{
    public readonly string $title;

    public readonly string $name;

    public readonly string $domain;

    public readonly Carbon $breachDate;

    public readonly Carbon $addedDate;

    public readonly Carbon $modifiedDate;

    public readonly int $pwnCount;

    public readonly string $description;

    public readonly Collection $dataClasses;

    public readonly bool $verified;

    public readonly bool $fabricated;

    public readonly bool $sensitive;

    public readonly bool $retired;

    public readonly bool $spamList;

    public readonly bool $malware;

    public readonly string $logoPath;

    public function __construct(stdClass $data = null)
    {
        if (null === $data) {
            throw new RuntimeException('Invalid BreachSite data');
        }

        $this->title = $data->Title;
        $this->name = $data->Name;
        $this->domain = $data->Domain;
        $this->breachDate = new Carbon($data->BreachDate);
        $this->addedDate = $this->dateStringToCarbon($data->AddedDate);
        $this->modifiedDate = $this->dateStringToCarbon($data->ModifiedDate);
        $this->pwnCount = $data->PwnCount;
        $this->description = $data->Description;
        $this->dataClasses = Collection::make($data->DataClasses);
        $this->verified = $data->IsVerified;
        $this->fabricated = $data->IsFabricated;
        $this->sensitive = $data->IsSensitive;
        $this->retired = $data->IsRetired;
        $this->spamList = $data->IsSpamList;
        $this->malware = $data->IsMalware;
        $this->logoPath = $data->LogoPath;
    }

    protected function dateStringToCarbon(string $date): Carbon
    {
        $dateObject = Carbon::createFromFormat(
            'Y-m-d\TH:i:s\Z',
            $date,
        );

        return (false !== $dateObject) ? $dateObject : Carbon::now();
    }
}
