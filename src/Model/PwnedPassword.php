<?php
/**
 * PwnedPassword
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Model;

use GuzzleHttp\Psr7\Response;

class PwnedPassword
{
    /**
     * @param Response $response
     * @param string $hash
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getRangeData(
        Response $response,
        string $hash
    ): \Tightenco\Collect\Support\Collection {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, 0, 5);
        $collection = new \Tightenco\Collect\Support\Collection();
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
}
