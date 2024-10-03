<?php

namespace Icawebdesign\Hibp\Paste;

use stdClass;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class PasteEntity
{
    public readonly string $source;

    public readonly string $id;

    public readonly string $title;

    public readonly ?Carbon $date;

    public readonly int $emailCount;

    public readonly string $link;

    public array $pasteSites = [
        'pastebin' => 'https://pastebin.com/',
        'pastie' => 'http://pastie.org/',
        'ghostbin' => 'https://ghostbin.site/',
        'justpaste' => 'https://justpasteit.org/',
    ];

    public function __construct(stdClass $data)
    {
        $sourceKey = strtolower($data->Source);
        $sourceLink = $data->Id;

        if (array_key_exists($sourceKey, $this->pasteSites)) {
            $sourceLink = "{$this->pasteSites[$sourceKey]}{$data->Id}";
        }

        $this->source = $data->Source;
        $this->id = $data->Id;
        $this->title = $data->Title ?? '';
        $this->date = $this->dateStringToCarbon($data->Date ?? null);
        $this->emailCount = $data->EmailCount;
        $this->link = $sourceLink;
    }

    public function dateStringToCarbon(?string $date = null): ?Carbon
    {
        if (null === $date) {
            return null;
        }

        try {
            $pasteDate = Carbon::createFromFormat(
                'Y-m-d\TH:i:s\Z',
                $date
            );
        } catch (InvalidFormatException) {
            $pasteDate = null;
        }

        return $pasteDate;
    }
}
