<?php

declare(strict_types=1);

namespace FpDbTest\ORM\ConvertHelper;

use Exception;
use FpDbTest\ORM\Dictionary\Specifier;

final class ArgumentConverter
{
    private const string ARRAY_SEPARATOR = ', ';
    private const string IS_NULL = 'NULL';

    private ConvertArrayHelper $convertArrayHelper;

    public function __construct(ConvertArrayHelper $convertArrayHelper)
    {
        $this->convertArrayHelper = $convertArrayHelper;
    }

    /**
     * @throws Exception
     */
    public function convertArg(Specifier $specifier, mixed $arg): string|int|float
    {
        if ($arg === null) {
            return self::IS_NULL;
        }

        return match ($specifier) {
            Specifier::ARRAY => $this->convertArray($arg),
            Specifier::INDEFINITE => $this->convertIndefinite($arg),
            Specifier::IDENTIFIER => $this->convertIdentifier($arg),
            Specifier::DIGIT => $this->convertInt($arg),
            Specifier::FLOAT => $this->convertFloat($arg),
        };
    }

    /**
     * @throws Exception
     */
    private function convertArray(array $arg): string
    {
        return $this->convertArrayHelper->isAssociative($arg) ?
            $this->convertAssociativeArray($arg) :
            $this->convertIndexedArray($arg);
    }

    /**
     * @throws Exception
     */
    private function convertIndexedArray(array $arg): string
    {
        $args = [];
        foreach ($arg as $value) {
            $args[] = $this->convertIndefinite($value);
        }

        return implode(self::ARRAY_SEPARATOR, $args);
    }

    /**
     * @throws Exception
     */
    private function convertAssociativeArray(array $arg): string
    {
        $args = [];
        foreach ($arg as $identifier => $value) {
            $args[] = sprintf('`%s` = %s', $identifier, $this->convertIndefinite($value));
        }

        return implode(self::ARRAY_SEPARATOR, $args);
    }

    /**
     * @throws Exception
     */
    private function convertIndefinite(mixed $arg): string|int|float
    {
        if ($arg === null) {
            return self::IS_NULL;
        } elseif (is_bool($arg)) {
            return $arg ? 1 : 0;
        } elseif (is_int($arg)) {
            return $this->convertInt($arg);
        } elseif (is_float($arg)) {
            return $this->convertFloat($arg);
        } elseif (is_string($arg)) {
            return "'$arg'";
        } else {
            throw new Exception();
        }
    }

    /**
     * @param string|list<string>|null $arg
     * @throws Exception
     */
    private function convertIdentifier(array|string|null $arg): string
    {
        if (is_array($arg)) {
            $identifierArgs = [];
            foreach ($arg as $value) {
                $identifierArgs[] = $this->convertIdentifier($value);
            }

            return implode(', ', $identifierArgs);
        }

        if ($arg === null) {
            return self::IS_NULL;
        }

        return "`$arg`";
    }

    private function convertInt(int|string|bool|null $arg): int|string
    {
        if ($arg === null) {
            return self::IS_NULL;
        }

        return intval($arg);
    }

    private function convertFloat(float|string|null $arg): float|string
    {
        if ($arg === null) {
            return self::IS_NULL;
        }

        return floatval($arg);
    }
}
