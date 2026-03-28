<?php

declare(strict_types=1);

namespace JDZ\Language\Contract;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
interface LanguageInterface
{
    public function get(string $key, array $parameters = [], ?string $default = null): string;

    public function getIf(string $key, array $parameters = [], ?string $default = null): string;

    public function has(string $key, array $parameters = [], ?string $domain = null, ?string $locale = null): bool;
}
