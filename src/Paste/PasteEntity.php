<?php
/**
 * DESCRIPTION_HERE
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Paste;

use Carbon\Carbon;
use Exception;
use stdClass;

class PasteEntity
{
    /** @var string */
    protected string $source;

    /** @var string */
    protected string $id;

    /** @var string */
    protected string $title;

    /** @var ?Carbon */
    protected ?Carbon $date;

    /** @var int */
    protected int $emailCount;

    /** @var string */
    protected string $link;

    /** @var array */
    protected array $pasteSites = [
        'pastebin' => 'https://pastebin.com/',
    ];

    /**
     * PasteEntity constructor.
     *
     * @param stdClass $data
     *
     * @throws Exception
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
     * @return PasteEntity
     */
    public function setSource(string $source): PasteEntity
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
     * @return PasteEntity
     */
    public function setId(string $id): PasteEntity
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
     * @return PasteEntity
     */
    public function setTitle(string $title = null): PasteEntity
    {
        if (null !== $title) {
            $this->title = $title;
        }

        return $this;
    }

    /**
     * @return Carbon|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string|null $date
     *
     * @return PasteEntity
     * @throws Exception
     */
    public function setDate(string $date = null): PasteEntity
    {
        if (null === $date) {
            $this->date = null;

            return $this;
        }

        $pasteDate = Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $date);

        $this->date = (false !== $pasteDate) ? $pasteDate : null;

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
     * @return PasteEntity
     */
    public function setEmailCount(int $emailCount): PasteEntity
    {
        $this->emailCount = $emailCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return PasteEntity
     */
    public function setLink(string $link): PasteEntity
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @param stdClass $data
     *
     * @throws Exception
     */
    public function map(stdClass $data): void
    {
        $sourceKey = strtolower($data->Source);
        $sourceLink = $data->Id;

        if (array_key_exists($sourceKey, $this->pasteSites)) {
            $sourceLink = $this->pasteSites[$sourceKey] . $data->Id;
        }

        if (!property_exists($data, 'Title')) {
            $data->Title = '';
        }

        $this
            ->setSource($data->Source)
            ->setId($data->Id)
            ->setTitle($data->Title)
            ->setDate($data->Date)
            ->setEmailCount($data->EmailCount)
            ->setLink($sourceLink);
    }
}
