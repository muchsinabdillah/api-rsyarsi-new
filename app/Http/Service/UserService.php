<?php 
 
namespace App\Http\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\UserRepositoryImpl;
use App\Http\Controllers\Controller;

class UserService extends Controller {

    private $userRepository;

    public function __construct(UserRepositoryImpl $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createNewUser(Request $request){
        // validator 
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users",
            "username" => "required|unique:users",
            "password" => "required|confirmed"
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        // create new user
        $createnewuser = $this->userRepository->register($request);
        if ($createnewuser) {
            //response
            return response()->json([
                "status" => 1,
                "message" => "User Register Successfully"
            ], 200);
        } else {
            //response
            return response()->json([
                "status" => 0,
                "message" => "User Register Failed"
            ], 500);
        }
    }
    public function GenerateToken(Request $request)
    {
        // validator 
        $validator = Validator::make($request->all(), [
            "username" => "required", 
            "password" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //login
        $loginUser = $this->userRepository->getTokenData($request);
        if ($loginUser) {
            //response 
            return response()->json([
                "status" => 1,
                "message" => "Logged in successfully",
                "access_token" => "Bearer ".$loginUser
            ]);
        } else {
            //response
            return response()->json([
                "status" => 0,
                "message" => "Invalid Credential"
            ]);
        }
    }
    public function token()
    {
        // validator 
        $headers = apache_request_headers();
     
        $username = $headers['X-Username'];
        $password = $headers['X-Password'];

        //login
        $loginUser = $this->userRepository->token($username,$password);
        if ($loginUser) {
            $response = array(
                'token' => "Bearer ".$loginUser, // Set array status dengan success     
            );
            $metadata = array(
                'message' => 'Ok', // Set array status dengan success 
                'code' => 200, // Set array nama dengan isi kolom nama pada tabel siswa 
            );
            //response 
            return response()->json(['response' => $response, 'metadata' => $metadata], 200);
        } else {
            //response
            return response()->json([
                'code' => 201,
                "message" => "Username atau Password Tidak Sesuai."
            ]);
        }
    }
    public function goViewUsersbyAksesID(Request $request){
        // validator 
        $count = $this->userRepository->getViewUsersbyAksesID($request)->count();
      
        if ($count>0) 
        {
            $data = $this->userRepository->getViewUsersbyAksesID($request);
            return $this->sendResponse($data, "Data User ditemukan.");
        } else {
            return $this->sendError("Data User Not Found.", [], 400);
        }
    }
    public function getLoginSimrs(Request $request)
    {
        // validator  
        $request->validate([
            "username" => "required",
            "password" => "required" 
        ]);

        //login
        $loginUser = $this->userRepository->getLoginSimrs($request);
        
        if ($loginUser) {
        
            return $this->sendResponse($loginUser ,"Login berhasil.");  


        } else {
            //response
            return $this->sendError("login Gagal.", []);
        }
    }
    public function getLoginSimrsToken(Request $request)
    {
        // validator  
        $request->validate([
            "username" => "required",
            "password" => "required" 
        ]);

        //login
        $loginUser = $this->userRepository->getLoginSimrs($request);
        $token = $this->userRepository->token($request->username,$request->password);
        $response = [
            'userlogin' => $loginUser, 
            'token' => $token , 
        ];
        if ($loginUser) {
        
            return $this->sendResponse($response ,"Login berhasil.");  


        } else {
            //response
            return $this->sendError("login Gagal.", []);
        }
    }
    public function getLoginMitraMCU(Request $request)
    {
        // validator  
        $request->validate([
            "username" => "required",
            "password" => "required" 
        ]);

        //login
        $loginUser = $this->userRepository->getLoginMitraMCU($request);
        
        if ($loginUser) {
        
            return $this->sendResponse($loginUser ,"Login berhasil.");  


        } else {
            //response
            return $this->sendError("login Gagal.", []);
        }
    }
}
