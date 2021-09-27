<?php

namespace LaraBlockList\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use LaraBlockList\Contracts\CanBeInBlocklist;
use LaraBlockList\Jobs\CheckForBlocklistJob;

class CheckForBlocklistCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blocklist:check
    {model : Model class}
    {id? : Entity id}
    {--F|from= : created started from; format Y-m-d}
    {--Q|queue=now : use queue or not}
    {--chunk=100 : Chunk count}
    {--delay=5 : Dispatch delay in seconds}
    {--model_id_column=id : Model identification column name}
    {--created_at_column=created_at : Created at column name}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function handle() {

        $job = CheckForBlocklistJob::class;

        $model = $this->argument( 'model' );

        if ( ! is_subclass_of( $model, Model::class ) || ! is_a( $model, CanBeInBlocklist::class, true ) ) {
            throw new \Exception( "Class [{$model}] should be valid model." );
        }

        if ( $id = $this->argument( 'id' ) ) {
            $this->dispatchJob( $job, $model, $id );
        } else {
            $query = $model::query();

            if ( $from = $this->option( 'from' ) ) {
                $query->where(
                    $this->createdAtColumnName(),
                    '>=',
                    Carbon::createFromFormat( 'Y-m-d', $from )->format( 'Y-m-d H:i:s' )
                );
            }

            $query->select( $this->modelIdColumnName() )->chunk(
                $this->chunkCount(),
                fn( Collection $contacts ) => $this->dispatchJob( $job, $model, $contacts->pluck( $this->modelIdColumnName() )->toArray() )
            );
        }

        return 0;
    }

    /**
     * @return string
     */
    public function modelIdColumnName(): string {
        return (string) $this->option( 'model_id_column' );
    }

    /**
     * @return string
     */
    public function createdAtColumnName(): string {
        return (string) $this->option( 'created_at_column' );
    }

    /**
     * @return int
     */
    public function getDispatchDelay(): int {
        return (int) $this->option( 'delay' );
    }

    /**
     * @return int
     */
    public function chunkCount(): int {
        return (int) $this->option( 'chunk' );
    }

    /**
     * @param string $job
     * @param ...$options
     */
    protected function dispatchJob( string $job, ...$options ) {
        $queueName = $this->option( 'queue' );

        switch ( $queueName ) {
            case 'now':
            case 'sync':
                $job::dispatchSync( ...$options );

                break;
            default:
                $job::dispatch( ...$options )->onQueue( $queueName ?? 'default' )
                    ->delay( now()->addMinutes( $this->getDispatchDelay() ) );

                break;
        }
    }
}
