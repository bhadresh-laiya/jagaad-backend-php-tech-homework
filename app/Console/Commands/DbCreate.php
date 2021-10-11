<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DbCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the database';

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
        $connectionType = env('DB_CONNECTION');
        $configDb = config('database.connections.'.$connectionType);
        if(empty($configDb['database'])){
            $this->info('You want to create a database not defined in the configuration file');
            return false;
        }
        try{
            $pdo = $this->getPdoConnection(
                $configDb['driver'],
                $configDb['host'],
                $configDb['port'],
                $configDb['username'],
                $configDb['password']
            );
            $sql = 'CREATE DATABASE IF NOT EXISTS '. $configDb['database'] .' CHARACTER SET '. $configDb['charset'] .
                ' COLLATE '.$configDb['collation'].';';
            $pdo->exec($sql);
            $this->info('Database created successfully');
        }catch (\Exception $ex){
            $this->error($ex->getMessage());
        }
        return true;

    }

    /**
     * @param string $type
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @return \PDO
     */
    private function getPdoConnection($type, $host, $port, $username, $password){
        return new \PDO("$type:host=$host;$port;", $username, $password);
    }

}
