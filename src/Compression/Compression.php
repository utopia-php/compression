<?php

namespace Utopia\Compression;

abstract class Compression
{
    public const IDENTITY = 'identity';

    public const BROTLI = 'brotli';

    public const DEFLATE = 'deflate';

    public const GZIP = 'gzip';

    public const LZ4 = 'lz4';

    public const SNAPPY = 'snappy';

    public const XZ = 'xz';

    public const ZSTD = 'zstd';

    public function __construct()
    {
        if (!self::isSupported()) {
            throw new \Exception('Compression algorithm' . $this->getName() . ' is not supported, missing extension');
        }
    }

    /**
     * Return the name of compression algorithm.
     *
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Return the id of compression algorithm used in content-encoding and accept-encoding headers.
     *
     * @return string
     */
    public function getContentEncoding(): string
    {
        return strtolower($this->getName());
    }

    /**
     * Compress data.
     *
     * @param $data
     * @return string
     */
    abstract public function compress(string $data);

    /**
     * Decompress data.
     *
     * @param $data
     * @return string
     */
    abstract public function decompress(string $data);

    /**
     * Return true if the compression algorithm is supported.
     *
     * @return bool
     */
    abstract public static function isSupported(): bool;

    /**
     * Create a compression algorithm from the name.
     *
     * @param  string  $name
     */
    public static function fromName(string $name): ?Compression
    {
        $name = strtolower($name);

        switch ($name) {
            case Compression::BROTLI:
            case 'br':
                return new Algorithms\Brotli();
            case Compression::DEFLATE:
                return new Algorithms\Deflate();
            case Compression::GZIP:
                return new Algorithms\Gzip();
            case Compression::LZ4:
                return new Algorithms\LZ4();
            case Compression::SNAPPY:
                return new Algorithms\Snappy();
            case Compression::XZ:
                return new Algorithms\XZ();
            case Compression::ZSTD:
                return new Algorithms\Zstd();
            case Compression::IDENTITY:
            default:
                return null;
        }
    }

    /**
     * @param  string  $acceptEncoding String in format <encoding-method1>[;q=<weight>], <encoding-method2>[;q=<weight>], ...
     *  Where:
     *      - <encoding-method> is the name of an encoding algorithm
     *      - [;q=<weight>] is an optional quality value from 0 to 1, indicating preference (1 being the highest)
     * @param  array  $supported List of supported compression algorithms, if not provided, the default list will be used
     *  The default list is [br, gzip, deflate, identity]
     * @return Compression|null
     */
    public static function fromAcceptEncoding(string $acceptEncoding, array $supported = []): ?Compression
    {
        if (empty($acceptEncoding)) {
            return null;
        }

        if (empty($supported)) {
            $supported = [
                self::BROTLI => Algorithms\Brotli::isSupported(),
                self::GZIP => Algorithms\Gzip::isSupported(),
                self::DEFLATE => Algorithms\Deflate::isSupported(),
                self::IDENTITY => true,
            ];
        }

        $encodings = \array_map('trim', \explode(',', $acceptEncoding));
        $encodings = \array_map('strtolower', $encodings);

        $encodings = \array_map(function ($encoding) {
            $parts = \explode(';', $encoding);
            $encoding = $parts[0];
            $quality = 1.0;

            if (isset($parts[1])) {
                $quality = \floatval(\str_replace('q=', '', $parts[1]));
            }

            return [
                'encoding' => $encoding,
                'quality' => $quality,
            ];
        }, $encodings);

        $encodings = \array_filter($encodings, function ($encoding) use ($supported) {
            return \in_array($encoding['encoding'], $supported);
        });

        if (empty($encodings)) {
            return null;
        }

        usort($encodings, function ($a, $b) {
            return $a['quality'] <=> $b['quality'];
        });

        return self::fromName($encodings[0]['encoding']);
    }
}
