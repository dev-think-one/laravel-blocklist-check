<?php

namespace LaraBlockList\Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use LaraBlockList\Jobs\CheckForBlocklistJob;
use LaraBlockList\Tests\Fixtures\Models\User;

class CheckCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function check_command()
    {
        User::factory()->count(10)->create();
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'hacker@email.ru',
        ]);
        $user->toAllowlist(true);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'email' => 'hacker@email.ru',
        ]);
        User::factory()->count(10)->create();

        $this->assertEquals(22, User::count());
        $this->assertEquals(22, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(0, User::blocklisted()->count());

        $this->artisan('blocklist:check', [
            'model' => User::class,
        ])->assertExitCode(0);

        $this->assertEquals(21, User::count());
        $this->assertEquals(22, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(1, User::blocklisted()->count());
    }

    /** @test */
    public function check_command_by_id()
    {
        User::factory()->count(10)->create();
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'hacker@email.ru',
        ]);
        $user->toAllowlist(true);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'email' => 'hacker@email.ru',
        ]);
        /** @var User $user3 */
        $user3 = User::factory()->create([
            'email' => 'hacker@email.ru',
        ]);
        User::factory()->count(10)->create();

        $this->assertEquals(23, User::count());
        $this->assertEquals(23, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(0, User::blocklisted()->count());

        $this->artisan('blocklist:check', [
            'model' => User::class,
            'id'    => $user3->getKey(),
        ])->assertExitCode(0);

        $this->assertEquals(22, User::count());
        $this->assertEquals(23, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(1, User::blocklisted()->count());
        $this->assertFalse($user2->refresh()->isBlocklisted());
        $this->assertTrue($user3->refresh()->isBlocklisted());

        $this->artisan('blocklist:check', [
            'model' => User::class,
            'id'    => $user2->getKey(),
        ])->assertExitCode(0);
        $this->assertEquals(21, User::count());
        $this->assertEquals(23, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(2, User::blocklisted()->count());
        $this->assertTrue($user2->refresh()->isBlocklisted());
        $this->assertTrue($user3->refresh()->isBlocklisted());

        $this->artisan('blocklist:check', [
            'model'   => User::class,
            'id'      => $user->getKey(),
            '--queue' => 'sync',
        ])->assertExitCode(0);
        $this->assertEquals(21, User::count());
        $this->assertEquals(23, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(2, User::blocklisted()->count());
        $this->assertFalse($user->refresh()->isBlocklisted());
        $this->assertTrue($user2->refresh()->isBlocklisted());
        $this->assertTrue($user3->refresh()->isBlocklisted());
    }

    /** @test */
    public function error_if_not_model()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Class [Illuminate\Foundation\Auth\User] should be valid model.");

        $this->artisan('blocklist:check', [
            'model' => \Illuminate\Foundation\Auth\User::class,
        ]);
    }

    /** @test */
    public function test_with_from_parameter()
    {
        User::factory()->count(10)->create([
            'created_at' => Carbon::now()->subWeek(),
        ]);
        User::factory()->count(10)->create();
        /** @var User $user */
        $user = User::factory()->create([
            'email'      => 'hacker@email.ru',
            'created_at' => Carbon::now()->subWeek(),
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'email' => 'hacker@email.ru',
        ]);
        User::factory()->count(10)->create([
            'created_at' => Carbon::now()->subWeek(),
        ]);
        User::factory()->count(10)->create();

        $this->artisan('blocklist:check', [
            'model'  => User::class,
            '--from' => Carbon::now()->subDays(2)->format('Y-m-d'),
        ])->assertExitCode(0);

        $this->assertEquals(41, User::count());
        $this->assertEquals(42, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(1, User::blocklisted()->count());

        $this->artisan('blocklist:check', [
            'model'  => User::class,
            '--from' => Carbon::now()->subDays(20)->format('Y-m-d'),
        ])->assertExitCode(0);

        $this->assertEquals(40, User::count());
        $this->assertEquals(42, User::withoutGlobalScope(User::globalScopeAllowlisted())->count());
        $this->assertEquals(2, User::blocklisted()->count());
    }

    /** @test */
    public function use_specific_queue()
    {
        Queue::fake();

        User::factory()->count(10)->create();
        /** @var User $user */
        $user = User::factory()->create([
            'email'      => 'hacker@email.ru',
        ]);
        /** @var User $user2 */
        $user2 = User::factory()->create([
            'email' => 'hacker@email.ru',
        ]);
        User::factory()->count(10)->create();

        Queue::assertNothingPushed();

        $this->artisan('blocklist:check', [
            'model'   => User::class,
            '--queue' => 'rand_queue',
        ])->assertExitCode(0);


        Queue::assertPushedOn('rand_queue', CheckForBlocklistJob::class);
    }
}
