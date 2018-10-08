<?php
/**
 * PwnedPassword
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Models;

use GuzzleHttp\Psr7\Response;

class PwnedPassword
{
    /**
     * @param Response $response
     * @param string $hashSnippet
     * @param string $hash
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getRangeData(Response $response, string $hashSnippet, string $hash): \Tightenco\Collect\Support\Collection
    {
        $hashSnippet = strtoupper($hashSnippet);
        $hash = strtoupper($hash);

        $results = \Tightenco\Collect\Support\Collection::make(explode("\r\n", (string)$response->getBody()));

        return $results->map(function ($hashSuffix) use ($hashSnippet, $hash) {
            list($suffix, $count) = explode(':', $hashSuffix);
            $fullHash = $hashSnippet . $suffix;

            return \Tightenco\Collect\Support\Collection::make([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count' => (int)$count,
                    'matched' => $fullHash == $hash,
                ],
            ]);
        });
    }
}
