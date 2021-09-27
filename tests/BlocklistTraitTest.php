<?php

namespace LaraBlockList\Tests;

use LaraBlockList\Tests\Fixtures\Models\User;

class BlocklistTraitTest extends TestCase
{

    /** @test  */
    public function model_has_conditional_methods()
    {
        User::factory()->count(10)->create();
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertFalse($user->isBlocklisted());
        $this->assertFalse($user->isAllowlisted());

        $user->toAllowlist()->save();
        $user->refresh();
        $this->assertFalse($user->isBlocklisted());
        $this->assertTrue($user->isAllowlisted());

        $user->toBlocklist()->save();
        $user->refresh();
        $this->assertTrue($user->isBlocklisted());
        $this->assertFalse($user->isAllowlisted());

        $user->toAllowlist(true);
        $user->refresh();
        $this->assertFalse($user->isBlocklisted());
        $this->assertTrue($user->isAllowlisted());


        $user->toBlocklist(true);
        $user->refresh();
        $this->assertTrue($user->isBlocklisted());
        $this->assertFalse($user->isAllowlisted());
    }


    /** @test  */
    public function model_has_global_scope()
    {
        User::factory()->count(10)->create();
        /** @var User $user */
        $user = User::factory()->create();
        $user->toBlocklist(true);
        /** @var User $user2 */
        $user2 = User::factory()->create();
        $user2->toAllowlist(true);
        User::factory()->count(10)->create();

        $this->assertEquals(21, User::count());
        $this->assertEquals(22, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(1, User::blocklisted()->count());
    }
}
