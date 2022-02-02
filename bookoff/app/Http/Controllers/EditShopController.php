<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Qoo10\Api\Qoo10ApiProcessor;
use Qoo10\Api\Qoo10CertGenerator;
use Illuminate\Support\Facades\Log;
use Spatie\Async\Pool;

       
class EditShopController extends Controller
{
    //
    public function index(){
        return view('front.Editshop');
    }
    
    public function getKey(Request $request){
        $user_id = $request -> user_id;
        // $store_id = $request -> store_id;
        $store_login_id = $request -> store_login_id;
        $store_login_pwd = $request -> store_login_pwd;
        $qoo10_api_key = $request -> qoo10_key;
        $url = 'https://api.qoo10.jp/GMKT.INC.Front.QAPIService/ebayjapan.qapi/CertificationAPI.CreateCertificationKey';
        $data = [

                'user_id' => $user_id,
                'pwd' => $store_login_pwd,
                'key' => $qoo10_api_key
        ];

         $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt(
            $ch, 
            CURLOPT_HTTPHEADER, 
            array(
                'Content-Type: application/x-www-form-urlencoded', // for define content type that is json
               
                'QAPIVersion: 1.0'
            ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 36000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
 

        return response()->json([

            'data' => $server_output
        ]);
        // $certGenerator = new Qoo10CertGenerator();
        // $cert = $certGenerator->certGenerate($qoo10_api_key, $store_login_id, $store_login_pwd);
        // $xml=simplexml_load_string($cert) or die("Error: Cannot create object");
        // $json = json_encode($xml);
        // $array = json_decode($json,TRUE);
        // $result = array(
        //         'user_id' => $user_id1,
        //         'store_id' => $store_id,
        //         'qoo10_api_key' => $qoo10_api_key,
        //         'store_login_id' => $store_login_id,
        //         'store_login_pwd' => $store_login_pwd,
        //         'qoo10_api_key' => $qoo10_api_key,
        //         'qoo10_auth_key' => $array['ResultObject']
        //     );
        // return response()->json([
        //       'key' => $array['ResultObject']
        // ]);
    }


    public function registerStore(Request $request){
        
        $user_id = $request -> user_id;
        $store_id = $request -> store_id;
        $store_login_id = $request -> store_login_id;
        $store_login_pwd = $request -> store_login_pwd;
        $qoo10_api_key = $request -> qoo10_key;
        $qoo10_auth_key = $request -> qoo10_auth_key;
        // $certGenerator = new Qoo10CertGenerator();
        // $cert = $certGenerator->certGenerate($qoo10_api_key, $store_login_id, $store_login_pwd);
      
            // Do a thing
        
    
            $check_store = Store::where(['user_id'=>$user_id]) -> get();
            if(!count($check_store)){
                  
                  $record = Store::create([
                        'user_id' => $user_id,
                        'store_id' => $store_login_id,
                        'store_login_id' => $store_login_id,
                        'store_login_pwd' => $store_login_pwd,
                        'qoo10_api_key' => $qoo10_api_key,
                        'qoo10_auth_key' => $qoo10_auth_key,

                  ]);

            }

            else{
                
                $update_store = Store::where(['user_id' => $user_id]) ->
                                 update(['store_id'=>$store_login_id,'store_login_id'=>$store_login_id,
                                        'store_login_pwd' => $store_login_pwd,'qoo10_api_key'=>$qoo10_api_key,
                                        'qoo10_auth_key' => $qoo10_auth_key]);

            }
            
    
        return response() -> json([
            'data' => 'true'
        ]);
       
   

        
    }
}
