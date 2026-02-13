<?php

namespace Biostate\FilamentMenuBuilder\Contracts;

interface MenuItemResourceInterface
{
    /**
     * @return array<int, object>
     */
    public static function getFormSchema(): array;

    /**
     * @return array<int, object>
     */
    public static function getFormSchemaArray(): array;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function getUrl(string $name = 'index', array $parameters = []): string;
}
