<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReqresUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reqres:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull reqres users and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
