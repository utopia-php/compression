<?php

namespace Utopia\Compression\Algorithms;

use Utopia\Compression\Compression;

class XZ extends Compression
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return Compression::XZ;
    }

    /**
     * Compress.
     *
     * @param string $data
     *
     * @return string
     */
    public function compress(string $data): string
    {
        return \xzencode($data);
    }

    /**
     * Decompress.
     *
     * @param string $data
     *
     * @return string
     */
    public function decompress(string $data): string
    {
        return \xzdecode($data);
    }

    /**
     * Check if the algorithm is supported.
     * 
     * @return bool
     */
    public static function isSupported(): bool
    {
        return \function_exists('xzencode');
    }
}
