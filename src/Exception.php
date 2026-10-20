<?php

declare(strict_types=1);

namespace Himelali;

/**
 * Base exception of the package.
 *
 * Every error thrown by InWord extends this class, so catching it handles them all.
 */
class Exception extends \Exception
{
    /**
     * Short, safe description of any value for exception messages.
     *
     * @param mixed $value
     */
    protected static function describe($value): string
    {
        if (is_string($value)) {
            return "'" . (strlen($value) > 40 ? substr($value, 0, 40) . '...' : $value) . "'";
        }

        if (is_int($value) || is_float($value)) {
            return var_export($value, true);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        return is_object($value) ? 'object(' . get_class($value) . ')' : gettype($value);
    }
}
