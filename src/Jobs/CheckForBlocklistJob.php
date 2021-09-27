<?php

namespace LaraBlockList\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LaraBlockList\Contracts\CanBeInBlocklist;

class CheckForBlocklistJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $entities = [];

    protected string $class;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $class, array|int|string $entities = [])
    {
        $this->class    = $class;
        $this->entities = is_array($entities) ? $entities : [ $entities ];
    }

    public function handle()
    {
        /** @var CanBeInBlocklist|Model $entity */
        foreach ($this->entities as $entity) {
            if (!($entity instanceof $this->class)) {
                $entity = $this->class::find($entity);
            }

            if (!$entity->isAllowlisted() && !$entity->getBlocklistProcessor()->passed($entity)) {
                $entity->toBlocklist()->save();
            }
        }
    }
}
