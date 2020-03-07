<?php
/**
 * Data entity for breached site
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

namespace Icawebdesign\Hibp\Breach;

use Carbon\Carbon;
use Exception;
use stdClass;
use Tightenco\Collect\Support\Collection;

class BreachSiteEntity
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $name;

    /** @var string */
    protected $domain;

    /** @var Carbon */
    protected $breachDate;

    /** @var Carbon */
    protected $addedDate;

    /** @var Carbon */
    protected $modifiedDate;

    /** @var int */
    protected $pwnCount;

    /** @var string */
    protected $description;

    /** @var Collection */
    protected $dataClasses;

    /** @var bool */
    protected $verified;

    /** @var bool */
    protected $fabricated;

    /** @var bool */
    protected $sensitive;

    /** @var bool */
    protected $retired;

    /** @var bool */
    protected $spamList;

    /** @var string */
    protected $logoPath;

    /**
     * BreachSiteEntity constructor.
     *
     * @param stdClass|null $data
     *
     * @throws Exception
     */
    public function __construct(stdClass $data = null)
    {
        $this->map($data);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return BreachSiteEntity
     */
    public function setTitle(string $title): BreachSiteEntity
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return BreachSiteEntity
     */
    public function setName(string $name): BreachSiteEntity
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return BreachSiteEntity
     */
    public function setDomain(string $domain): BreachSiteEntity
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getBreachDate(): Carbon
    {
        return $this->breachDate;
    }

    /**
     * @param string $breachDate
     * @return BreachSiteEntity
     * @throws Exception
     */
    public function setBreachDate(string $breachDate): BreachSiteEntity
    {
        $this->breachDate = new Carbon($breachDate);

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getAddedDate(): Carbon
    {
        return $this->addedDate;
    }

    /**
     * @param string $addedDate
     * @return BreachSiteEntity
     * @throws Exception
     */
    public function setAddedDate(string $addedDate): BreachSiteEntity
    {
        $this->addedDate = Carbon::createFromFormat(
            'Y-m-d\TH:i:s\Z',
            $addedDate
        );

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getModifiedDate(): Carbon
    {
        return $this->modifiedDate;
    }

    /**
     * @param string $modifiedDate
     * @return BreachSiteEntity
     * @throws Exception
     */
    public function setModifiedDate(string $modifiedDate): BreachSiteEntity
    {
        $this->modifiedDate = Carbon::createFromFormat(
            'Y-m-d\TH:i:s\Z',
            $modifiedDate
        );

        return $this;
    }

    /**
     * @return int
     */
    public function getPwnCount(): int
    {
        return $this->pwnCount;
    }

    /**
     * @param int $pwnCount
     *
     * @return BreachSiteEntity
     */
    public function setPwnCount(int $pwnCount): BreachSiteEntity
    {
        $this->pwnCount = $pwnCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return BreachSiteEntity
     */
    public function setDescription(string $description): BreachSiteEntity
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDataClasses(): Collection
    {
        return $this->dataClasses;
    }

    /**
     * @param array $dataClasses
     *
     * @return BreachSiteEntity
     */
    public function setDataClasses(array $dataClasses): BreachSiteEntity
    {
        $this->dataClasses = Collection::make($dataClasses);

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     *
     * @return BreachSiteEntity
     */
    public function setVerified(bool $verified): BreachSiteEntity
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFabricated(): bool
    {
        return $this->fabricated;
    }

    /**
     * @param bool $fabricated
     *
     * @return BreachSiteEntity
     */
    public function setFabricated(bool $fabricated): BreachSiteEntity
    {
        $this->fabricated = $fabricated;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSensitive(): bool
    {
        return $this->sensitive;
    }

    /**
     * @param bool $sensitive
     *
     * @return BreachSiteEntity
     */
    public function setSensitive(bool $sensitive): BreachSiteEntity
    {
        $this->sensitive = $sensitive;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRetired(): bool
    {
        return $this->retired;
    }

    /**
     * @param bool $retired
     *
     * @return BreachSiteEntity
     */
    public function setRetired(bool $retired): BreachSiteEntity
    {
        $this->retired = $retired;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSpamList(): bool
    {
        return $this->spamList;
    }

    /**
     * @param bool $spamList
     *
     * @return BreachSiteEntity
     */
    public function setSpamList(bool $spamList): BreachSiteEntity
    {
        $this->spamList = $spamList;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogoPath(): string
    {
        return $this->logoPath;
    }

    /**
     * @param string $logoPath
     *
     * @return BreachSiteEntity
     */
    public function setLogoPath(string $logoPath): BreachSiteEntity
    {
        $this->logoPath = $logoPath;

        return $this;
    }

    /**
     * @param stdClass $data
     * @throws Exception
     */
    public function map(stdClass $data)
    {
        $this
            ->setTitle($data->Title)
            ->setName($data->Name)
            ->setDomain($data->Domain)
            ->setBreachDate($data->BreachDate)
            ->setAddedDate($data->AddedDate)
            ->setModifiedDate($data->ModifiedDate)
            ->setPwnCount($data->PwnCount)
            ->setDescription($data->Description)
            ->setDataClasses($data->DataClasses)
            ->setVerified($data->IsVerified)
            ->setFabricated($data->IsFabricated)
            ->setSensitive($data->IsSensitive)
            ->setRetired($data->IsRetired)
            ->setSpamList($data->IsSpamList)
            ->setLogoPath($data->LogoPath);
    }
}
