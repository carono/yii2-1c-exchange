<?php

namespace carono\exchange1c\helpers;

class ByteHelper
{
    /**
     * Convert number with unit byte to bytes unit
     *
     * @link https://en.wikipedia.org/wiki/Metric_prefix
     *
     * @param string $value a number of bytes with optinal SI decimal prefix (e.g. 7k, 5mb, 3GB or 1 Tb)
     *
     * @return integer|float A number representation of the size in BYTES (can be 0). otherwise FALSE
     */
    public static function str2bytes($value)
    {
        // only string
        $unit_byte = preg_replace('/[^a-zA-Z]/', '', $value);
        $unit_byte = strtolower($unit_byte);

        // only number (allow decimal point)
        $num_val = preg_replace('/\D\.\D/', '', $value);
        switch ($unit_byte) {
            case 'p':    // petabyte
                break;
            case 'pb':
                $num_val *= 1024;
                break;
            case 't':    // terabyte
            case 'tb':
                $num_val *= 1024;
                break;
            case 'g':    // gigabyte
            case 'gb':
                $num_val *= 1024;
                break;
            case 'm':    // megabyte
                $num_val *= 1024;
                break;
            case 'mb':
                $num_val *= 1024;
                break;
            case 'k':    // kilobyte
                break;
            case 'kb':
                $num_val *= 1024;
                break;
            case 'b':    // byte
                return $num_val *= 1;
                break; // make sure
            default:
                return false;
        }
        return $num_val;
    }

    /**
     * The maximum file upload size by getting PHP settings
     *
     * @return integer|float file size limit in BYTES based
     */
    public static function maximum_upload_size()
    {
        static $upload_size = null;
        if ($upload_size === null) {
            $post_max_size = self::str2bytes(ini_get('post_max_size'));
            $upload_max_filesize = self::str2bytes(ini_get('upload_max_filesize'));
            $memory_limit = self::str2bytes(ini_get('memory_limit'));
            if (empty($post_max_size) && empty($upload_max_filesize) && empty($memory_limit)) {
                return false;
            }
            $upload_size = min($post_max_size, $upload_max_filesize, $memory_limit);
        }
        return $upload_size;
    }
}