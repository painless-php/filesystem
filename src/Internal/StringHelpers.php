<?php

namespace PainlessPHP\Filesystem\Internal;

class StringHelpers
{
    /**
     * Remove prefix from beginning of subject string
     *
     */
    public static function removePrefix(string $subject, string $prefix) : string
    {
        if(str_starts_with($subject, $prefix)) {
            $subject = mb_substr($subject, mb_strlen($prefix));
        }

        return $subject;
    }

    /**
     * Append a given suffix to string. Suffix is not appended if the given
     * subject already has that suffix.
     *
     */
    public static function addSuffix(string $subject, string $suffix) : string
    {
        if(! str_ends_with($subject, $suffix)) {
            $subject .= $suffix;
        }

        return $subject;
    }

    /**
     * Remove a suffix from end of the subject string
     *
     */
    public static function removeSuffix(string $subject, string $suffix) : string
    {
        if(str_ends_with($subject, $suffix)) {
            $len = mb_strlen($subject) - mb_strlen($suffix);
            $subject = mb_substr($subject, 0, $len);
        }

        return $subject;
    }
}
