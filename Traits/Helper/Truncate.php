<?php

namespace Illarra\CoreBundle\Traits\Helper;

trait Truncate
{
    public function truncate($getter = 'getId', $length = 30, $preserve = false, $separator = '...', $charset = 'UTF-8')
    {
        $value = $this->$getter();

        if (function_exists('mb_get_info')) {
            if (mb_strlen($value, $charset) > $length) {
                if ($preserve) {
                    if (false !== ($breakpoint = mb_strpos($value, ' ', $length, $charset))) {
                        $length = $breakpoint;
                    }
                }

                return rtrim(mb_substr($value, 0, $length, $charset)) . $separator;
            }
        } else {
            if (strlen($value) > $length) {
                if ($preserve) {
                    if (false !== ($breakpoint = strpos($value, ' ', $length))) {
                        $length = $breakpoint;
                    }
                }

                return rtrim(substr($value, 0, $length)) . $separator;
            }
        }

        return $value;
    }
}
