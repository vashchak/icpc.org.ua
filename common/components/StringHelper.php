<?php

namespace common\components;

class StringHelper extends \CApplicationComponent
{
    /**
     * Implements ucfirst() for multibyte strings
     *
     * @param array $array Origin array
     * @param array $rules List of keys to replace
     */
    public function mb_ucfirst($str, $enc = 'UTF-8')
    {
        return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
    }
}
