<?php

declare(strict_types=1);

namespace Himelali\Support;

use ErrorException;
use Himelali\Exception;
use Himelali\Exceptions\ConversionException;
use Throwable;

/**
 * Runs a callback so that every failure surfaces as a package exception:
 * PHP warnings / notices become exceptions and unexpected errors (\Error, TypeError,
 * other exceptions) are wrapped in a ConversionException, keeping the original as previous.
 */
final class ErrorGuard
{
    /**
     * @param callable $callback
     * @return mixed The callback's result.
     * @throws Exception|ErrorException
     */
    public static function run($callback)
    {
        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            return $callback();
        } catch (Exception $exception) {
            throw $exception;
        } catch (Throwable $error) {
            throw ConversionException::fromError($error);
        } finally {
            restore_error_handler();
        }
    }
}
