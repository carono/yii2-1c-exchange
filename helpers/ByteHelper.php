<?php

namespace carono\exchange1c\helpers;

class ByteHelper
{
    /**
     * @param $value
     * @return mixed
     */
    public static function str2bytes($value)
    {
        $unit_byte = preg_replace('/[^a-zA-Z]/', '', $value);
        $num_val = preg_replace('/[^\d]/', '', $value);
        switch ($unit_byte) {
            case 'M':
                $k = 2;
                break;
            default:
                $k = 1;
        }
        return $num_val * pow(1024, $k);
    }

    /**
     * The maximum file upload size by getting PHP settings
     *
     * @return integer|float|false file size limit in BYTES based
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