<?php
/**
 * PwnedPassword
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Model;

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

class PwnedPassword
{
    /**
     * @param ResponseInterface $response
     * @param string $hash
     *
     * @return Collection
     */
    public function getRangeData(
        ResponseInterface $response,
        string $hash
    ): Collection {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, 0, 5);
        $results = Collection::make(explode("\r\n", (string)$response->getBody()));

        return $results->map(static function ($hashSuffix) use ($hashSnippet, $hash) {
            list($suffix, $count) = explode(':', $hashSuffix);
            $fullHash = sprintf('%s%s', $hashSnippet, $suffix);

            return Collection::make([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count' => (int)$count,
                    'matched' => $fullHash === $hash,
                ],
            ]);
        });
    }

    /**
     * @param ResponseInterface $response
     * @param string $hash
     *
     * @return Collection
     */
    public function getRangeDataWithPadding(
        ResponseInterface $response,
        string $hash
    ): Collection {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, 0, 5);
        $results = Collection::make(explode("\r\n", (string)$response->getBody()));

        return $results->map(static function ($hashSuffix) use ($hashSnippet, $hash) {
            [$suffix, $count] = explode(':', $hashSuffix);
            $fullHash = sprintf('%s%s', $hashSnippet, $suffix);

            return Collection::make([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count'       => (int)$count,
                    'matched'     => $fullHash === $hash,
                ],
            ]);
        });
    }
}
