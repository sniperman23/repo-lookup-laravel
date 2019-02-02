<?php

namespace App\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;


class GithubAccount
{

    public $username;
    public $DoesUsernameExist;
    private $repoArrayAscend;
    private $repoArrayDescend;
    private $commandLine;
    function __construct( $commandLine, $username ) {

        
        $this->commandLine = $commandLine;

        $githubClient = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.github.com',
            'timeout'  => 2.0,
        ]);

        
        
        try {

            if( $this->commandLine != null )
                $this->commandLine->line('Looking up user: '. $username . ' on GitHub...', null, 'v');

            $response = $githubClient->request('GET', 'users/' . $username . '/repos');
            

            $this->username = $username;
            $this->DoesUsernameExist = true;
            $repos = json_decode($response->getBody(), true);

            //Progress bar
            if( $this->commandLine != null )
            {
                $this->commandLine->loadRepoBar = $commandLine->output()->createProgressBar(count($repos));
                $this->commandLine->loadRepoBar->start();
            }
            

            //Load repos into repo array
            $this->repoArrayAscend = array(); 
            foreach( $repos as $repo )
            {
                $this->repoArrayAscend[] = new Repo( $repo['name'] , $repo['stargazers_count'] );
                if( $this->commandLine != null )
                    $this->commandLine->loadRepoBar->advance();
            }
            
            if( $this->commandLine != null )
            {
                $this->commandLine->loadRepoBar->finish();
                $this->commandLine->info(' Done.');
                $this->commandLine->line('Sorting repos by star gazes...', null, 'v');
            }

            //Sort forwards and backwards
            usort( $this->repoArrayAscend, array($this, 'ascendSort'));
            $this->repoArrayDescend = array_reverse($this->repoArrayAscend);        

        } catch (ClientException $e) {

            $this->DoesUsernameExist = false;
            if( $this->commandLine != null )
            {
                $this->commandLine->line( Psr7\str($e->getRequest()) , null, 'v');
                $this->commandLine->line( Psr7\str($e->getResponse()) , null, 'v');
            }
                     
            
        }
        
    }

    //Sorting function for usort
    private function ascendSort( $first, $second )
    {
        return $first->starGazeCount - $second->starGazeCount;
    }

    //Getter ascend
    public function getAscending()
    {

        $data = array();

        foreach( $this->repoArrayAscend as $repo )
        {
            $data[] = [ $repo->name, $repo->starGazeCount ];
        }

        return $data;
    }

    //Getter descend
    public function getDescending()
    {
        $data = array();

        foreach( $this->repoArrayDescend as $repo )
        {
            $data[] = [ $repo->name, $repo->starGazeCount ];
        }

        return $data;
    }    
}


class Repo
{

    public $name;
    public $starGazeCount;

    function __construct( string $name, int $starGazeCount ) {
        
        $this->name = $name;
        $this->starGazeCount = $starGazeCount;
    }

}

?>

