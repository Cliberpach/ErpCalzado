<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendResumenes',
        'App\Console\Commands\StockCommand',
        'App\Console\Commands\SendNotasCreditoCommand',
        'App\Console\Commands\SendFacturas',
        'App\Console\Commands\SendBoletasEspeciales',
        'App\Console\Commands\CrearBackupAutomaticoCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('notas_credito:send')->dailyAt('23:00');
        $schedule->command('facturas:send')->dailyAt('23:10');
        $schedule->command('stock:send')->dailyAt('23:20');
        $schedule->command('resumenes:send')->dailyAt('23:40');
        // 03:00 hora de America/Lima (config app.timezone). withoutOverlapping
        // evita que una noche lenta solape con la siguiente ejecución.
        $schedule->command('backup:automatico')->dailyAt('03:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
