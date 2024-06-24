<?php

namespace App\Http\Repository;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepositoryImpl  implements UserRepositoryInterface
{

    public function register($request){
         

         return  DB::connection('sqlsrv2')->table("users")->insert([
            'username' => $request->username,
            'email' => $request->email, 
            'name' => $request->name,
            'email' => $request->email,
            'api' => $request->api, 
            'password'  => bcrypt($request->password)
        ]);
    }
    public function getTokenData($request)
    {
        $token = auth()->attempt(
            [
                "username" => $request->username,
                "password" => $request->password
            ]);
        return $token;
        
    }
    public function token($username,$password)
    {
        $token = auth()->attempt(
            [
                "username" => $username,
                "password" => $password
            ]);
        return $token;
        
    }
     
    public function logout($request)
    {
        return auth()->logout();
    }   
    public function getViewUsersbyAksesID($request){
        return  DB::connection('sqlsrv2')->table("users")
        ->where('id', $request->sesiduser)->get();
    }
    public function getLoginSimrs($request){
        return  DB::connection('sqlsrv2')->table("Employees")
        ->select('First Name','Last Name','GroupUser' ,'JenisAntrian')
        ->where('NoPIN', $request->username)
        ->where('password', $request->password)
        ->get();
    }
    public function getLoginSimrsPin($NoPIN){
        return  DB::connection('sqlsrv2')->table("Employees")
        ->select(DB::raw("[First Name] as NamaDepan") ,'Last Name','GroupUser','JenisAntrian' )
        ->where('TTD_Pegawai', $NoPIN) 
        ->get();
    }
    public function getLoginSimrswithUserNameOnly($username){
        return  DB::connection('sqlsrv2')->table("Employees")
        ->select('First Name','Last Name','GroupUser','JenisAntrian' )
        ->where('NoPIN', $username) 
        ->get();
    }
    public function getLoginMitraMCU($request){
        return  DB::connection('sqlsrv2')->table("user_mitra")
        ->select(DB::raw("nama as [First Name]"),DB::raw("null as [Last Name]"),DB::raw("0 as GroupUser"),DB::raw("0 as JenisAntrian"),'group_jaminan','id_jaminan')
        ->where('username', $request->username)
        ->where('password', $request->password)
        ->where('is_active','1')
        ->get();
    }
}
