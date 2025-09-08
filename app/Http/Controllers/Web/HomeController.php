<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return 'ttt';
    }
    public function cancel() {}
    public function success()
    {
        return View('success');
    }
    public function checkoutPay() {}
}
