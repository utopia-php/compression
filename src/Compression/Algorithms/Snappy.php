<?php

namespace Utopia\Compression\Algorithms;

use Utopia\Compression\Compression;

class Snappy extends Compression
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return Compression::SNAPPY;
    }

    /**
     * Compress.
     *
     * @param  string  $data
     * @return string
     */
    public function compress(string $data): string
    {
        return \snappy_compress($data);
    }

    /**
     * Decompress.
     *
     * @param  string  $data
     * @return string
     */
    public function decompress(string $data): string
    {
        return \snappy_uncompress($data);
    }

    /**
     * Check if the algorithm is supported.
     *
     * @return bool
     */
    public static function isSupported(): bool
    {
        return \function_exists('snappy_compress');
    }
}
