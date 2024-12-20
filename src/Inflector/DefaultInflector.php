<?php

/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JDZ\Language\Inflector;

use Symfony\Component\String\Inflector\InflectorInterface;

/**
 * Default Inflector
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
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
