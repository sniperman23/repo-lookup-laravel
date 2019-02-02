<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Classes\GithubAccount;

class Lookup extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'lookup';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Look up up all public repositories of a user on github.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    

    public function handle()
    {
        
        //First ask
        $username = $this->ask('Who would you like to lookup?');
        $accountObj = new GithubAccount($this, $username);

        //Keep asking if something goes wrong
        while( !$accountObj->DoesUsernameExist )
        {
            $username = $this->ask('Could not find github user. Try again, Who would you like to lookup?');
            $accountObj = new GithubAccount($this, $username);
        }

        $defaultIndex = 0;
        $order = $this->choice('Order by asending or descending?', ['Ascending', 'Descending'], $defaultIndex);

        $repoList;
        if( $order == 'Ascending' )
        {
            $repoList = $accountObj->getAscending();
        }
        else if( $order == 'Descending' )
        {
            $repoList = $accountObj->getDescending();
        }

        $headers = ['Repo Name', 'Star Gaze Count'];

        $this->table($headers, $repoList);
        
        //Recurse and keep program alive for a new lookup
        $this->handle();
        
        
    }

    //
    public function Output()
    {
        return $this->output;
    }
    
    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}




