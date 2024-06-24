<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repository\UserRepositoryImpl;
use App\Http\Service\UserService;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
     // USER REGISTER API - POST
    public function register(Request $request){
        $userRepository = new UserRepositoryImpl();
        $userService = new UserService($userRepository);
        $user =  $userService->createNewUser($request);
        return $user;
    }
    // USER LOGIN API - POST
    public function genToken(Request $request){  
        $userRepository = new UserRepositoryImpl();
        $userService = new UserService($userRepository);
        $user =  $userService->GenerateToken($request);
        return $user;
    }
    public function token(Request $request){  
        $userRepository = new UserRepositoryImpl();
        $userService = new UserService($userRepository);
        $user =  $userService->token($request);
        return $user;
    }
    public function getViewUsersbyAksesID(Request $request)
    {
        $userRepository = new UserRepositoryImpl();
        $userService = new UserService($userRepository);
        $user =  $userService->goViewUsersbyAksesID($request);
        return $user;
    }
    public function getLoginSimrs(Request $request){  
        $userRepository = new UserRepositoryImpl();
        $userService = new UserService($userRepository);
        $user =  $userService->getLoginSimrs($request);
        return $user;
    }
    public function getLoginSimrsToken(Request $request){  
        $userRepository = new UserRepositoryImpl();
        $userService = new UserService($userRepository);
        $user =  $userService->getLoginSimrsToken($request);
        return $user;
    }
    public function getLoginMitraMCU(Request $request){  
        $userRepository = new UserRepositoryImpl();
        $userService = new UserService($userRepository);
        $user =  $userService->getLoginMitraMCU($request);
        return $user;
    }
}
