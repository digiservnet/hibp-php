<?php
/**
 * PwnedPassword
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Model;

use Psr\Http\Message\ResponseInterface;
use Tightenco\Collect\Support\Collection;

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
        $collection = new Collection();
        $results = $collection->make(explode("\r\n", (string)$response->getBody()));

        return $results->map(function ($hashSuffix) use ($hashSnippet, $hash, $collection) {
            list($suffix, $count) = explode(':', $hashSuffix);
            $fullHash = $hashSnippet . $suffix;

            return $collection->make([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count' => (int)$count,
                    'matched' => $fullHash == $hash,
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
    public function getRangeDataWithoutPadding(
        ResponseInterface $response,
        string $hash
    ): Collection {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, 0, 5);
        $collection = new Collection();
        $results = $collection->make(explode("\r\n", (string)$response->getBody()));

        return $results->map(function ($hashSuffix) use ($hashSnippet, $hash, $collection) {
            list($suffix, $count) = explode(':', $hashSuffix);
            $fullHash = $hashSnippet . $suffix;

            if ((int)$count > 0) {
                return $collection->make([
                    $fullHash => [
                        'hashSnippet' => $fullHash,
                        'count'       => (int) $count,
                        'matched'     => $fullHash == $hash,
                    ],
                ]);
            }
        });
    }
}
