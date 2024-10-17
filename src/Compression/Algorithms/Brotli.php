<?php

namespace Utopia\Compression\Algorithms;

use Utopia\Compression\Compression;

class Brotli extends Compression
{
    protected int $level = 6; //BROTLI_COMPRESS_LEVEL_DEFAULT;

    protected int $mode = 0; //BROTLI_GENERIC;

    /**
     * @return string
     */
    public function getName(): string
    {
        return Compression::BROTLI;
    }

    /**
     * Get the compression level.
     *
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Sets the brotli compression mode to generic.
     *
     * This is the default mode
     */
    public function useGenericMode(): void
    {
        $this->mode = 0; //BROTLI_GENERIC;
    }

    /**
     * Sets the brotli compression mode to UTF-8 text mode.
     *
     * Optimizes compression for UTF-8 formatted text
     *
     * @link https://github.com/kjdev/php-ext-brotli#parameters
     */
    public function useTextMode(): void
    {
        $this->mode = 1; //BROTLI_GENERIC;
    }

    /**
     * Sets the brotli compression mode to font mode.
     *
     * Optimized compression for WOFF 2.0 Fonts
     *
     * @link https://github.com/kjdev/php-ext-brotli#parameters
     */
    public function useFontMode(): void
    {
        $this->mode = 2; //BROTLI_GENERIC;
    }

    /**
     * Set the compression level.
     *
     * Allow values from 0 up to a current max of 11.
     *
     * @param  int  $level
     * @return void
     */
    public function setLevel(int $level): void
    {
        $min = 1; //BROTLI_COMPRESS_LEVEL_MIN;
        $max = 9; //BROTLI_COMPRESS_LEVEL_MAX;
        if ($level < $min || $level > $max) {
            throw new \InvalidArgumentException("Level must be between {$min} and {$max}");
        }
        $this->level = $level; // $level;
    }

    /**
     * Compress.
     *
     * @param  string  $data
     * @return string
     */
    public function compress(string $data): string
    {
        return \brotli_compress($data, $this->getLevel(), $this->mode);
    }

    /**
     * Decompress.
     *
     * @param  string  $data
     * @return string
     */
    public function decompress(string $data): string
    {
        return \brotli_uncompress($data);
    }

    /**
     * Check if the algorithm is supported.
     * 
     * @return bool
     */
    public static function isSupported(): bool
    {
        return \function_exists('brotli_compress');
    }
}
