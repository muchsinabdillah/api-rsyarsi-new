<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\bTrsPaymentService;
use App\Http\Repository\bVisitRepositoryImpl;
use App\Http\Repository\bPaymentRepositoryImpl;

class PaymentController extends Controller
{
    public function createTrs(Request $request){
        $visit = new bVisitRepositoryImpl();  
        $paymentrepo = new bPaymentRepositoryImpl();  
        $userService = new bTrsPaymentService($visit,$paymentrepo);
        $user =  $userService->createTrs($request);
        return $user; 
    }
    
}


