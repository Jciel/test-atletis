<?php

namespace app\resources;

abstract class BaseResource
{
    abstract public static function make(object $model): array;

    public static function collection(array $items): array
    {
        return array_map(fn ($item) => static::make($item), $items);
    }
}
