<?php

class MyDecoder
{
    static function decodeBoolValue(string $originalBool): ?bool
    {
        $formatedBool = null;
        if (preg_match("/^true$/i", $originalBool)) {
            $formatedBool = true;
        } elseif (preg_match("/^false$/i", $originalBool)) {
            $formatedBool = false;
        }
        return $formatedBool;
    }

    static function decodeIntValue(string $originalInt): ?int
    {
        $formatedInt = null;
        if (preg_match("/^[0-9]+$/", $originalInt)) {
            $formatedInt = (int)$originalInt;
        }
        return $formatedInt;
    }
}