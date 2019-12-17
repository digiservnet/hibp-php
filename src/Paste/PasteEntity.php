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
use Tightenco\Collect\Support\Collection;

class PasteEntity
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

    /** @var string */
    protected $link;

    /** @var array */
    protected $pasteSites = [
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
        $this->title = $title;

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

        $this->date = (new Carbon())->createFromFormat('Y-m-d\TH:i:s\Z', $date);

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
    public function map(stdClass $data)
    {
        $sourceKey = strtolower($data->Source);
        $sourceLink = $data->Id;

        if (array_key_exists($sourceKey, $this->pasteSites)) {
            $sourceLink = $this->pasteSites[$sourceKey] . $data->Id;
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
