<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{

    public function checkConnection() {
        try {
           $dbconnect = DB::connection()->getPDO();
           $dbname = DB::connection()->getDatabaseName();
           echo "Kết nối thành công với DB. DB là : ".$dbname;
        } catch(Exception $e) {
           echo "Kết nối thất bại!";
        }
     }

}
