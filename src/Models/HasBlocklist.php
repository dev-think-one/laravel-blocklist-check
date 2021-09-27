<?php

namespace LaraBlockList\Models;

use Illuminate\Database\Eloquent\Builder;
use LaraBlockList\Enums\BlockListEnum;

trait HasBlocklist
{

    /**
     * Boot trait.
     */
    public static function bootHasBlocklist(): void
    {
        static::addGlobalScope(static::globalScopeAllowlisted(), function (Builder $builder) {
            $builder->where(function (Builder $builder) {
                $builder->where(static::blocklistFieldName(), '<>', (string) BlockListEnum::BLOCKLISTED())
                        ->orWhereNull(static::blocklistFieldName());
            });
        });
    }

    /**
     * Scope a query to only include blocklisted rows.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeBlocklisted($query): mixed
    {
        return $query->withoutGlobalScope(static::globalScopeAllowlisted())
                     ->where(static::blocklistFieldName(), '=', (string) BlockListEnum::BLOCKLISTED());
    }

    /**
     * Global scope name.
     *
     * @return string
     */
    public static function globalScopeAllowlisted(): string
    {
        return 'withoutBlocklist';
    }

    /**
     * Blocklist attribute name.
     *
     * @return string
     */
    public static function blocklistFieldName(): string
    {
        return config('blocklist.default.db_field_name');
    }

    /**
     * Check is row allowlisted.
     *
     * @return bool
     */
    public function isAllowlisted(): bool
    {
        return $this->{static::blocklistFieldName()} == (string) BlockListEnum::ALLOWLISTED();
    }

    /**
     * Check is row blocklisted.
     *
     * @return bool
     */
    public function isBlocklisted(): bool
    {
        return $this->{static::blocklistFieldName()} == (string) BlockListEnum::BLOCKLISTED();
    }

    /**
     * Add entity to blocklist.
     *
     * @param bool $permanently
     *
     * @return static
     */
    public function toBlocklist(bool $permanently = false): static
    {
        $this->{static::blocklistFieldName()} = (string) BlockListEnum::BLOCKLISTED();
        if ($permanently) {
            $this->save();
        }

        return $this;
    }

    /**
     * Add entity to allowlist.
     *
     * @param bool $permanently
     *
     * @return static
     */
    public function toAllowlist(bool $permanently = false): static
    {
        $this->{static::blocklistFieldName()} = BlockListEnum::ALLOWLISTED();
        if ($permanently) {
            $this->save();
        }

        return $this;
    }
}
