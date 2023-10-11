<?php


namespace App\Traits;


trait Helpers
{
    function superAdminCheck()
    {
        return auth()->user()->role_id == 1;
    }
   
}