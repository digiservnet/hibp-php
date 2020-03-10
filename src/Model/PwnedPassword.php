<?php
/**
 * PwnedPassword
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Model;

use Icawebdesign\Hibp\Exception\PaddingHashCollisionException;
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
        $results = (new Collection())->make(explode("\r\n", (string)$response->getBody()));

        return $results->map(function ($hashSuffix) use ($hashSnippet, $hash) {
            [$suffix, $count] = explode(':', $hashSuffix);
            $fullHash = sprintf('%s%s', $hashSnippet, $suffix);

            return (new Collection())->make([
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
        $results = (new Collection())->make(explode("\r\n", (string)$response->getBody()));

        return $results->map(function ($hashSuffix) use ($hashSnippet, $hash) {
            [$suffix, $count] = explode(':', $hashSuffix);
            $fullHash = sprintf('%s%s', $hashSnippet, $suffix);

            return (new Collection())->make([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count'       => (int)$count,
                    'matched'     => $fullHash === $hash,
                ],
            ]);
        });
    }
}
