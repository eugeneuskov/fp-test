<?php

namespace FpDbTest;

use Exception;
use FpDbTest\ORM\ConditionalBlockService;
use FpDbTest\ORM\ConvertHelper\ArgumentConverter;
use FpDbTest\ORM\Dictionary\Specifier;
use FpDbTest\ORM\SpecifiersService;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private SpecifiersService $specifiersService;
    private ArgumentConverter $convertArgs;
    private ConditionalBlockService $conditionalBlockService;

    public function __construct(
        mysqli $mysqli,
        SpecifiersService $specifiersService,
        ArgumentConverter $convertArgs,
        ConditionalBlockService $conditionalBlockService,
    ) {
        $this->mysqli = $mysqli;
        $this->specifiersService = $specifiersService;
        $this->convertArgs = $convertArgs;
        $this->conditionalBlockService = $conditionalBlockService;
    }

    /**
     * @throws Exception
     */
    public function buildQuery(string $query, array $args = []): string
    {
        $querySpecifiers = $this->specifiersService->getSpecifiersFromQuery($query);
        if (empty($querySpecifiers)) {
            return $query;
        }

        $query = $this->buildQueryByArgs($query, $querySpecifiers, $args);

        $conditionalBlocks = $this->conditionalBlockService->getQueryConditionalBlocks($query);
        foreach ($conditionalBlocks as $block) {
            $query = $this->conditionalBlockService->replaceQueryConditionalBlock(
                $query,
                $block,
                str_contains($block, $this->skip())
            );
        }

        return $query;
    }

    public function skip(): int
    {
        return $this->conditionalBlockService->getSkipParameter();
    }

    /**
     * @throws Exception
     */
    private function buildQueryByArgs(string $query, array $querySpecifiers, array $args): string
    {
        $indefiniteSpecifiers = [];
        foreach ($querySpecifiers as $index => $specifier) {
            if ($specifier === Specifier::INDEFINITE) {
                // Если попадается спецификаторы '?', то надо обрабатывать их в последнюю очередь
                $indefiniteSpecifiers[$index] = $specifier;
                continue;
            }

            $query = $this->replaceSpecifier(
                $query,
                $specifier,
                $this->convertArgs->convertArg($specifier, $args[$index] ?? null),
            );
        }

        foreach ($indefiniteSpecifiers as $index => $specifier) {
            $query = $this->replaceSpecifier(
                $query,
                $specifier,
                $this->convertArgs->convertArg($specifier, $args[$index] ?? null),
            );
        }

        return $query;
    }

    private function replaceSpecifier(string $query, Specifier $specifier, string|int|float $arg): string
    {
        return preg_replace(
            '/' . preg_quote($specifier->value, '/') . '/',
            $arg,
            $query,
            1
        );
    }
}
