<?php

namespace App\Console\Commands;

use App\Events\UserVisitedTracksPage;
use App\User;
use Event;
use Illuminate\Console\Command;

class RefreshUserTracks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracks:refresh {user : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh a user\'s tracks';

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
        Event::fire(new UserVisitedTracksPage(User::find($this->argument('user'))));
    }
}
