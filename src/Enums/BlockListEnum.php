<?php

namespace LaraBlockList\Enums;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @method static self STANDARD()
 * @method static self BLOCKLISTED()
 * @method static self ALLOWLISTED()
 */
class BlockListEnum extends Enum
{
    private const STANDARD    = 'standard';
    private const BLOCKLISTED = 'blocklisted';
    private const ALLOWLISTED = 'allowlisted';
}
