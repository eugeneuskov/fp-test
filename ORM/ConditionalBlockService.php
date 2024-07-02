<?php

declare(strict_types=1);

namespace FpDbTest\ORM;

final class ConditionalBlockService
{
    private const string CONDITIONAL_BLOCK_PATTERN = '/\{([^}]+)}/';
    // По-хорошему это значение бы вынести в .env-файл и конфигурировать его не затрагивая код.
    private const int CONDITIONAL_BLOCK_SKIP = 9223372036854775807;

    public function getQueryConditionalBlocks(string $query): array
    {
        preg_match_all(self::CONDITIONAL_BLOCK_PATTERN, $query, $matches);

        return $matches[1];
    }

    public function getSkipParameter(): int
    {
        return self::CONDITIONAL_BLOCK_SKIP;
    }

    public function replaceQueryConditionalBlock(string $query, string $block, bool $skip): string
    {
        return $skip ?
            $this->skipBlockFromQuery($query, $block) :
            $this->includeBlockToQuery($query, $block);
    }

    private function skipBlockFromQuery(string $query, string $block): string
    {
        return str_replace('{' . $block . '}', '', $query);
    }

    private function includeBlockToQuery(string $query, string $block): string
    {
        return str_replace('{' . $block . '}', $block, $query);
    }
}
