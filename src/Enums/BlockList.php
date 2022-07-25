<?php

namespace LaraBlockList\Enums;

enum BlockList: string
{
    case STANDARD    = 'standard';
    case BLOCKLISTED = 'blocklisted';
    case ALLOWLISTED = 'allowlisted';
}
