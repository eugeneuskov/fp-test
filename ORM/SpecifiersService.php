<?php

declare(strict_types=1);

namespace FpDbTest\ORM;

use Exception;
use FpDbTest\ORM\Dictionary\Specifier;

final class SpecifiersService
{
    private const string SPECIFIER_PATTERN = '/\?d|\?f|\?a|\?#|\?/';

    /**
     * @return list<Specifier>
     *
     * @throws Exception
     */
    public function getSpecifiersFromQuery(string $query): array
    {
        $matches = [];
        if (preg_match_all(self::SPECIFIER_PATTERN, $query, $matches)) {
            $specifiers = [];
            foreach ($matches[0] as $match) {
                $specifier = Specifier::tryFrom($match);
                if ($specifier === null) {
                    throw new Exception('Unexpected specifier: ' . $match);
                }

                $specifiers[] = $specifier;
            }

            return $specifiers;
        }

        return [];
    }
}
