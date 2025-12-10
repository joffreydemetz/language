<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Language\Inflector;

use Symfony\Component\String\Inflector\InflectorInterface;

class DefaultInflector implements InflectorInterface
{
    public function singularize(string $plural): array
    {
        return [$plural];
    }

    public function pluralize(string $singular): array
    {
        return [$singular];
    }
}
