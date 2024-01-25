<?php

namespace App\Http\Controllers;

use App\Jobs\CfdiCreateFileExport;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        dispatch(new CfdiCreateFileExport('xlsx',[],[],['uuid']));
    }
}
