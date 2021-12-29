<?php

/**
 *
 */
final class Doctrine_Utils_FixSerializedBlob
{
    /**
     * Восстанавливаем неверные длины строк
     *
     * @param string $data
     * @return string
     */
    public static function fixStringSize(string $data): string
    {
        if (!$data) {
            return $data;
        }

        // если сериализована строка, то выделяем её и остальные фиксы в ней
        if (preg_match('/^s:(?:\d+):\"(?<substring>.*)\";$/', $data, $matches)) {
            $fixed = self::fixStringSize($matches['substring']);

            return serialize($fixed);
        }

        return preg_replace_callback(
            '/s:(?:\d+):\"(?<string>.*?)\";/',
            function ($matches) { return "s:" . strlen($matches['string']) . ':"' . $matches['string'] . '";'; },
            $data
        );
    }
}


