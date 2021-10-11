<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DbRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh migration and Seed the database at same time';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Migrate freshing');
        $this->call('migrate:fresh');
        $this->info('Seeding the database');
        $this->call('db:seed');
    }
}
