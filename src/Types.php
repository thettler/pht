<?php

namespace Thettler\Pht;

class Types
{
    public static $types = [
        'string' => [
            'php' => 'string',
        ],
        'int' => [
            'php' => 'int',
        ],
        'integer' => [
            'php' => 'integer',
        ],
        'arrayKey' => [
            'php' => 'string',
        ],
        'bool' => [
            'php' => 'bool',
        ],
        'boolean' => [
            'php' => 'bool',
        ],
        'true' => [
            'php' => 'bool',
        ],
        'false' => [
            'php' => 'bool',
        ],
        'null' => [
            'php' => 'null',
        ],
        'float' => [
            'php' => 'float',
        ],
        'double' => [
            'php' => 'float',
        ],
        'scalar' => [
            'php' => 'bool|float|int|string',
        ],
        'array' => [
            'php' => 'array',
        ],
        'iterable' => [
            'php' => 'array',
        ],
        'callable' => [
            'php' => 'callable',
        ],
        'resource' => [
            'php' => 'mixed',
        ],
        'void' => [
            'php' => 'void',
        ],
        'object' => [
            'php' => 'object',
        ],
        'positiveInt' => [
            'php' => 'int',
            'stan' => 'positive-int',
        ],
        'negativeInt' => [
            'php' => 'int',
            'stan' => 'negative-int',
        ],
        'noEmptyArray' => [
            'php' => 'array',
            'stan' => 'no-empty-array',
        ],
        'keyOf' => [
            'php' => 'string',
            'stan' => 'key-of',
        ],
        'valueOf' => [
            'php' => 'mixed',
            'stan' => 'value-of',
        ],
        'mixed' => [
            'php' => 'mixed',
        ],
        'classString' => [
            'php' => 'string',
            'stan' => 'class-string',
        ],
        'numericString' => [
            'php' => 'string',
            'stan' => 'numeric-string',
        ],
        'callableString' => [
            'php' => 'string',
            'stan' => 'callable-string',
        ],
        'noEmptyString' => [
            'php' => 'string',
            'stan' => 'no-empty-string',
        ],
        'never' => [
            'php' => 'never',
        ],
    ];

    public static function toPhpType(string $type): string
    {
        $type = trim($type);

        if (preg_match('/\[.*\]/m', $type)) {
            return 'array';
        }

        $type = preg_replace('/<.*>/m', '', $type);


        if (array_key_exists($type, static::$types)) {
            return static::$types[$type]['php'];
        }

        return $type;
    }

    public static function phtToStan(string $type): string
    {
        $type = trim($type);

        preg_match_all('/[<\[](.*)[>\]]/m', $type, $matches, PREG_SET_ORDER);
        $genericType = '';

        if ($matches) {
            $generics = array_map(function (string $genericType) {
                return static::phtToStan(trim($genericType));
            }, explode(',', $matches[0][1]));

            $genericType = str_replace($matches[0][1], implode(', ', $generics), $matches[0][0]);
            $type = str_replace($matches[0][0], '', $type);
        }

        if (array_key_exists($type, static::$types)) {
            return (static::$types[$type]['stan'] ?? $type).$genericType;
        }

        return $type.$genericType;
    }
}
