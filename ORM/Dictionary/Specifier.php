<?php

declare(strict_types=1);

namespace FpDbTest\ORM\Dictionary;

enum Specifier: string
{
    case DIGIT = '?d';
    case FLOAT = '?f';
    case ARRAY = '?a';
    case IDENTIFIER = '?#';
    case INDEFINITE = '?';
}
