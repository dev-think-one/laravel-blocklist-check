<?php

namespace LaraBlockList\Contracts;

use BlockListCheck\BlocklistProcessor;

interface CanBeInBlocklist {

    /**
     * Check is row allowlisted.
     *
     * @return bool
     */
    public function isAllowlisted(): bool;

    /**
     * Check is row blocklisted.
     *
     * @return bool
     */
    public function isBlocklisted(): bool;

    /**
     * Add entity to blocklist.
     *
     * @param bool $permanently
     *
     * @return static
     */
    public function toBlocklist(bool $permanently = false): static;

    /**
     * Add entity to allowlist.
     *
     * @param bool $permanently
     *
     * @return static
     */
    public function toAllowlist(bool $permanently = false): static;

    /**
     * Get blocklist processor.
     *
     * @param array $args
     *
     * @return BlocklistProcessor
     */
    public function getBlocklistProcessor(array $args = []): BlocklistProcessor;
}
