<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Classes\GithubAccount;

class LookupCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUsernameExist()
    {
        
        $accountObj = new GithubAccount(null, 'hglattergotz');
        $this->assertTrue($accountObj->DoesUsernameExist);
        
        //Testing GithubAccount returns false for DoesUsernameExist
        $accountObj = new GithubAccount(null, 'alsjclksadlkjclkajdcls12456788765432345765432sfjs;lkfj');
        $this->assertFalse($accountObj->DoesUsernameExist);
             
    }

    //Testing that repos are in ascending order
    public function testAscendSort()
    {
        $accountObj = new GithubAccount(null, 'hglattergotz');

        function ascendTest($arr)
        {
            $min = -1;

            foreach( $arr as $repo )
            {
               
                if( $repo[1] < $min )
                    return false;
                
                $min = $repo[1];
                
            }

            return true;
        }

        $this->assertTrue( ascendTest( $accountObj->getAscending() ) );     

    }

    //Testing that repos are in descending order
    public function testDescendSort()
    {
        $accountObj = new GithubAccount(null, 'hglattergotz');

        function descendTest($arr)
        {
            $max = 999999;

            foreach( $arr as $repo )
            {
               
                if( $repo[1] > $max )
                    return false;
                
                $max = $repo[1];
                
            }

            return true;
        }

        $this->assertTrue( descendTest( $accountObj->getDescending() ) );     

    }

    

    
}
