<?php

declare(strict_types=1);

namespace FpDbTest\ORM\ConvertHelper;

final class ConvertArrayHelper
{
    public function isAssociative(array $array): bool
    {
        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                return true;
            }
        }

        return false;
    }
}
