<?php

namespace Icawebdesign\Hibp\Model;

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

class PwnedPassword
{
    /**
     * @param ResponseInterface $response
     * @param string $hash
     *
     * @return Collection<int, Collection<string, array{hashSnippet: string, count: int, matched: bool}>>
     */
    public function getRangeData(
        ResponseInterface $response,
        string $hash,
    ): Collection {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, offset: 0, length: 5);
        $results = Collection::make(explode("\r\n", (string)$response->getBody()));

        return $results->map(static function ($hashSuffix) use ($hashSnippet, $hash) {
            [$suffix, $count] = explode(':', $hashSuffix);
            $fullHash = "{$hashSnippet}{$suffix}";

            return Collection::make([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count' => (int)$count,
                    'matched' => $fullHash === $hash,
                ],
            ]);
        });
    }

    public function getRangeDataWithPadding(
        ResponseInterface $response,
        string $hash,
    ): Collection {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, offset: 0, length: 5);
        $results = Collection::make(explode("\r\n", (string)$response->getBody()));

        return $results->map(static function ($hashSuffix) use ($hashSnippet, $hash) {
            [$suffix, $count] = explode(':', $hashSuffix);
            $fullHash = "{$hashSnippet}{$suffix}";

            return Collection::make([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count' => (int)$count,
                    'matched' => $fullHash === $hash,
                ],
            ]);
        });
    }
}
