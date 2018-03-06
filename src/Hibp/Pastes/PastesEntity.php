<?php
/**
 * DESCRIPTION_HERE
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Pastes;

use Carbon\Carbon;
use stdClass;

class PastesEntity
{
    /** @var string */
    protected $source;

    /** @var string */
    protected $id;

    /** @var string */
    protected $title;

    /** @var Carbon|null */
    protected $date;

    /** @var int */
    protected $emailCount;

    /**
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        $this->map($data);
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return PastesEntity
     */
    public function setSource(string $source): PastesEntity
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return PastesEntity
     */
    public function setId(string $id): PastesEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return PastesEntity
     */
    public function setTitle(string $title = null): PastesEntity
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @param string|null $date
     *
     * @return PastesEntity
     */
    public function setDate(string $date = null): PastesEntity
    {
        if (null === $date) {
            $this->date = null;

            return $this;
        }

        $this->date = Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $date);

        return $this;
    }

    /**
     * @return int
     */
    public function getEmailCount(): int
    {
        return $this->emailCount;
    }

    /**
     * @param int $emailCount
     *
     * @return PastesEntity
     */
    public function setEmailCount(int $emailCount): PastesEntity
    {
        $this->emailCount = $emailCount;

        return $this;
    }

    /**
     * @param stdClass $data
     */
    public function map(stdClass $data)
    {
        $this
            ->setSource($data->Source)
            ->setId($data->Id)
            ->setTitle($data->Title)
            ->setDate($data->Date)
            ->setEmailCount($data->EmailCount);
    }
}
