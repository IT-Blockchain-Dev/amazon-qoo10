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

    public function registerStore(Request $request){
        
        $user_id1 = $request -> user_id;
        $store_id = $request -> store_id;
        $store_login_id = $request -> store_login_id;
        $store_login_pwd = $request -> store_login_pwd;
        $qoo10_api_key = $request -> qoo10_key;
        
        $pool = Pool::create();
        $pool->add(function () use ($user_id1,$qoo10_api_key,$store_id, $store_login_id,$store_login_pwd) {
            // Do a thing
            $certGenerator = new Qoo10CertGenerator();
            $cert = $certGenerator->certGenerate($qoo10_api_key, $store_login_id, $store_login_pwd);
            $xml=simplexml_load_string($cert) or die("Error: Cannot create object");
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            $result = array(
                'user_id' => $user_id1,
                'store_id' => $store_id,
                'qoo10_api_key' => $qoo10_api_key,
                'store_login_id' => $store_login_id,
                'store_login_pwd' => $store_login_pwd,
                'qoo10_api_key' => $qoo10_api_key,
                'qoo10_auth_key' => $array['ResultObject']
            );
            return $result;
        })->then(function ($result) {
            // Handle success
            $check_store = Store::where(['user_id'=>$result['user_id']]) -> get();
            if(!count($check_store)){
                  
                  $record = Store::create([
                        'user_id' => $result['user_id'],
                        'store_id' => $result['store_id'],
                        'store_login_id' => $result['store_login_id'],
                        'store_login_pwd' => $result['store_login_pwd'],
                        'qoo10_api_key' => $result['qoo10_api_key'],
                        'qoo10_auth_key' => $result['qoo10_auth_key']
                  ]);

            }

            else{
                
                $update_store = Store::where(['user_id' => $result['user_id']]) ->
                                 update(['store_id'=>$result['store_id'],'store_login_id'=>$result['store_login_id'],
                                        'store_login_pwd' => $result['store_login_pwd'],'qoo10_api_key'=>$result['qoo10_api_key'],
                                        'qoo10_auth_key' => $result['qoo10_auth_key']]);

            }
            
        })->catch(function (Throwable $exception) {
            // Handle exception
        });

        $pool->wait();
        return response() -> json([
            'status' => 'true'
        ]);
       
   

        
    }
}
