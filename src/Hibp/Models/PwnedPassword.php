<?php
/**
 * DESCRIPTION_HERE
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Models;

use GuzzleHttp\Psr7\Response;
use Tightenco\Collect\Support\Collection;

class PwnedPassword
{
    /**
     * @param Response $response
     * @param string $hashSnippet
     * @param string $hash
     *
     * @return Collection
     */
    public function getRangeData(Response $response, string $hashSnippet, string $hash): Collection
    {
        $hashSnippet = strtoupper($hashSnippet);
        $hash = strtoupper($hash);

        $results = collect(explode("\r\n", (string)$response->getBody()));

        return $results->map(function($hashSuffix) use ($hashSnippet, $hash) {
            list($suffix, $count) = explode(':', $hashSuffix);
            $fullHash = $hashSnippet . $suffix;

            return collect([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count' => (int)$count,
                    'matched' => $fullHash == $hash,
                ],
            ]);
        });
    }
}
