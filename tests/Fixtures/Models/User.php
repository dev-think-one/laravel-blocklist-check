<?php

namespace LaraBlockList\Tests\Fixtures\Models;

use BlockListCheck\BlocklistProcessor;
use BlockListCheck\Checkers\RegexChecker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaraBlockList\Contracts\CanBeInBlocklist;
use LaraBlockList\Models\HasBlocklist;
use LaraBlockList\Tests\Fixtures\Factories\UserFactory;

class User extends Model implements CanBeInBlocklist
{
    use HasBlocklist, HasFactory;

    /**
     * @inheritDoc
     */
    public function getBlocklistProcessor(array $args = []): BlocklistProcessor
    {
        return new BlocklistProcessor([
            new RegexChecker([ '/\.ru$/', ], [ 'email' ]),
            new RegexChecker([ /* contain cyrillic */ '/[А-Яа-яЁё]+/u', ], [ 'name', ]),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
