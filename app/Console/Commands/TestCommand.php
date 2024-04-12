<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Almacenes\TestTarea;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creando registro test';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // return 0;
        $test   =   new TestTarea();
        $test->save();
    }
}
