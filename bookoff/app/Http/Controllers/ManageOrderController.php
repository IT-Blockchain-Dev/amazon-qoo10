<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDemo;
use Symfony\Component\HttpFoundation\Response;
use Qoo10\Api\Items\Goods;
use Qoo10\Api\Qoo10ApiProcessor;
use Qoo10\Api\Qoo10CertGenerator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Store;

class ManageOrderController extends Controller
{
    //

    public function getOrder(Request $request){

        date_default_timezone_set("Asia/Tokyo");
        $currentTime = Carbon::now();
        $currentTime = $currentTime->toArray();
        $year = $currentTime['year'];
        $month = $currentTime['month'];
        $day = $currentTime['day'];
        $user_id = $request->user_id;
        $currentDate = strval($year).strval($month).strval($day);
        Log::info($currentDate);
        // Log::info(strval($year));
        $store_result = Store::where(['user_id'=>$user_id])->first();
        Log::info($store_result['qoo10_auth_key']);
        $url = 'https://api.qoo10.jp/GMKT.INC.Front.QAPIService/ebayjapan.qapi/ShippingBasic.GetShippingInfo_v2';
        $data = [

             'ShippingStat' => '2',
             'search_Sdate' => $currentDate,
             'search_Edate' => $currentDate,
             'search_condition' => '1',  
             'key' => $store_result['qoo10_auth_key'] 
        ];

         $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        curl_setopt(
            $ch, 
            CURLOPT_HTTPHEADER, 
            array(
                'Content-Type: application/x-www-form-urlencoded' // for define content type that is json
                // 'GiosisCertificationKey: S5bnbfynQvOKjKOOi6rqr2kyaKwK_g_1__g_2_tyLHiSIJ7jifeGJ_g_2_tYHO_g_2_9lvlRVaBoXZdsquVBC3VERFI1swTZz8Jx_g_2_LTtMbTKziDCTpt62xvJx39u3jjQuqwyYOeGwPXWePCD',
              
            ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 36000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
 
        $json_result = json_decode($server_output,true);
        if(is_array($json_result)){
            foreach($json_result['ResultObject'] as $result){
                // Log::info($result);
                $check_result = Order::where([
                       'user_id' => $user_id,
                        'orderNo' => $result['orderNo'],
                        'itemCode' => $result['itemCode'],
                        'itemTitle' => $result['itemTitle'],
                        'orderPrice' => $result['orderPrice'],
                        'orderQty' => $result['orderQty'],
                        'receiver' => $result['receiver'],
                        'zipCode' => $result['zipCode'],
                        'shippingAddr' => $result['shippingAddr'],
                        'receiveMobile' => $result['receiverMobile'],
                        'paymentMethod' => $result['PaymentMethod'],
                        'receiverEmail' => $result['buyerEmail']
                ])->get();

                if(!count($check_result)){
                      $create_result = Order::create([
                        'user_id' => $user_id,
                        'shippingStatus' => $result['shippingStatus'],
                        'orderDate' => $result['orderDate'],
                        'paymentDate' => $result['PaymentDate'],
                        'estShippingDate' => $result['EstShippingDate'],
                        'orderNo' => $result['orderNo'],
                        'itemCode' => $result['itemCode'],
                        'itemTitle' => $result['itemTitle'],
                        'orderPrice' => $result['orderPrice'],
                        'orderQty' => $result['orderQty'],
                        'receiver' => $result['receiver'],
                        'zipCode' => $result['zipCode'],
                        'shippingAddr' => $result['shippingAddr'],
                        'receiveMobile' => $result['receiverMobile'],
                        'paymentMethod' => $result['PaymentMethod'],
                        'receiverEmail' => $result['buyerEmail']

                    ]); 
                }

                else{
                      $create_result = Order::where([

                        'user_id' => $user_id,
                         'orderNo' => $result['orderNo'],
                         'itemCode' => $result['itemCode'],

                      ])->update([
                        'user_id' => $user_id,
                        'shippingStatus' => $result['shippingStatus'],
                        'orderDate' => $result['orderDate'],
                        'paymentDate' => $result['PaymentDate'],
                        'estShippingDate' => $result['EstShippingDate'],
                        'orderNo' => $result['orderNo'],
                        'itemCode' => $result['itemCode'],
                        'itemTitle' => $result['itemTitle'],
                        'orderPrice' => $result['orderPrice'],
                        'orderQty' => $result['orderQty'],
                        'receiver' => $result['receiver'],
                        'zipCode' => $result['zipCode'],
                        'shippingAddr' => $result['shippingAddr'],
                        'receiveMobile' => $result['receiverMobile'],
                        'paymentMethod' => $result['PaymentMethod'],
                        'receiverEmail' => $result['buyerEmail']

                    ]); 

                }
                
             }
        }

        else{

            $result = $json_result['ResultObject'];
            $check_result = Order::where([
                'user_id' => $user_id,
                 'orderNo' => $result['orderNo'],
                 'itemCode' => $result['itemCode'],
                 'itemTitle' => $result['itemTitle'],
                 'orderPrice' => $result['orderPrice'],
                 'orderQty' => $result['orderQty'],
                 'receiver' => $result['receiver'],
                 'zipCode' => $result['zipCode'],
                 'shippingAddr' => $result['shippingAddr'],
                 'receiveMobile' => $result['receiverMobile'],
                 'paymentMethod' => $result['PaymentMethod'],
                 'receiverEmail' => $result['buyerEmail']
               ])->get();

            if(!count($check_result)){
               $create_result = Order::create([
                 'user_id' => $user_id,
                 'shippingStatus' => $result['shippingStatus'],
                 'orderDate' => $result['orderDate'],
                 'paymentDate' => $result['PaymentDate'],
                 'estShippingDate' => $result['EstShippingDate'],
                 'orderNo' => $result['orderNo'],
                 'itemCode' => $result['itemCode'],
                 'itemTitle' => $result['itemTitle'],
                 'orderPrice' => $result['orderPrice'],
                 'orderQty' => $result['orderQty'],
                 'receiver' => $result['receiver'],
                 'zipCode' => $result['zipCode'],
                 'shippingAddr' => $result['shippingAddr'],
                 'receiveMobile' => $result['receiverMobile'],
                 'paymentMethod' => $result['PaymentMethod'],
                 'receiverEmail' => $result['buyerEmail']

             ]); 
         }

         else{
               $create_result = Order::where([

                 'user_id' => $user_id,
                  'orderNo' => $result['orderNo'],
                  'itemCode' => $result['itemCode'],

               ])->update([
                 'user_id' => $user_id,
                 'shippingStatus' => $result['shippingStatus'],
                 'orderDate' => $result['orderDate'],
                 'paymentDate' => $result['PaymentDate'],
                 'estShippingDate' => $result['EstShippingDate'],
                 'orderNo' => $result['orderNo'],
                 'itemCode' => $result['itemCode'],
                 'itemTitle' => $result['itemTitle'],
                 'orderPrice' => $result['orderPrice'],
                 'orderQty' => $result['orderQty'],
                 'receiver' => $result['receiver'],
                 'zipCode' => $result['zipCode'],
                 'shippingAddr' => $result['shippingAddr'],
                 'receiveMobile' => $result['receiverMobile'],
                 'paymentMethod' => $result['PaymentMethod'],
                 'receiverEmail' => $result['buyerEmail']

             ]); 

         }

        }
       

        return response()->json([

            'data' => $server_output
        ]);
    }

    public function displayOrder(Request $request){
        
        $user_id = $request->user_id;
        $get_result = Order::where([
            'user_id' => $user_id
        ])->get();

        return response()->json([

            'data' => $get_result   
        ]);
    }

    public function displaySingleOrder(Request $request){
        Log::info($request->orderNo);
        $order_id = intval($request->orderNo);
        $get_single_order = Order::where([
            'orderNo' => $order_id
        ])->get();
        
        return response()->json([
            'data' => $get_single_order
        ]);

     }

   
     public function selfShipping(Request $request){

        $user_id = $request -> user_id;
        $order_no = $request -> order_no;
        $get_store_info = Store::where(['user_id'=>$user_id])
                           ->get();
        $store_auth_key = $get_store_info[0]['qoo10_auth_key'];

        //get current time
        date_default_timezone_set("Asia/Tokyo");
        $currentTime = Carbon::now();
        $currentTime = $currentTime->toArray();
        $year = $currentTime['year'];
        $month = $currentTime['month'];
        $day = $currentTime['day'];
        $user_id = $request->user_id;
        $currentDate = strval($year).'-'.strval($month).'-'.strval($day);
        $estimatedDate = date('Y-m-d',strtotime($currentDate .'+3 days'));
        $parts = explode('-',$estimatedDate);
        $stringdate = $parts[0].$parts[1].$parts[2];

        $url = 'https://api.qoo10.jp/GMKT.INC.Front.QAPIService/ebayjapan.qapi/ShippingBasic.SetSellerCheckYN_V2';
        $data = [
                'OrderNo' => $order_no,
                'EstShipDt' => $stringdate,
                'DelayType' => '1',
                'DelayMemo' => 'aaa',
                'key' => $store_auth_key
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
        $result_code = json_decode($server_output);
        $result_code = $result_code->ResultCode;
        if($result_code == 0){
            $update_order = Order::where([
                'orderNo'=>order_no
            ])
            ->update([
                'orderStatus' => '3'
            ]);
        }
         
        return response()->json([

            'data' => $result_code
        ]);

     }

     public function saveShippingInfo(Request $request){
         
         $orderNo = $request -> order_no;
         $shippingCompany = $request -> shipping_company;
         $shippingNo = $request -> shipping_no;
         $save_result = Order::where(['orderNo'=>$orderNo])
                        ->update([
                            'shippingCompany'=>$shippingCompany,
                            'shippingNo'=>$shippingNo
                        ]);
         return response()->json([
             'data'=> 'true'
         ]);
     }

     public function finishShipping(Request $request){
         
        $orderNo = $request -> order_no;
        $user_id = $request -> user_id;
        $get_store_info = Store::where(['user_id'=>$user_id])
        ->get();
        $store_auth_key = $get_store_info[0]['qoo10_auth_key'];
        $order_info = Order::where([
                         'orderNo'=>$orderNo
                     ])
                     ->get();
        $shippingCompany = $order_info[0]['shippingCompany'];
        $shippingNo = $order_info[0]['shippingNo']; 
        $url = 'https://api.qoo10.jp/GMKT.INC.Front.QAPIService/ebayjapan.qapi/ShippingBasic.SetSendingInfo';
        $data = [
                'OrderNo' => $orderNo,
                'ShippingCorp' => $shippingCompany,
                'TrackingNo' => $shippingNo,
                'key' => $store_auth_key
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
        $result_code = json_decode($server_output);
        $result_code = $result_code->ResultCode;
        if($result_code == 0){
            $update_order = Order::where([
                'orderNo'=>order_no
            ])
            ->update([
                'orderStatus' => '4'
            ]);
        }
         
        return response()->json([

            'data' => $result_code
        ]);
       
       
    }

    //send email function
    public function sendEmail(Request $request){

        // $email = $request -> email_address;
        $email = $request->email_address;
        $mailData = [
            'title' => 'this is test email'
            
        ];

        Mail::to($email)->send(new EmailDemo($mailData));
        
        return response()->json([
            'message' => 'Email has been sent.'
        ],Response::HTTP_OK);
    }

    //get info item function
    public function getInfoItem(Request $request){
             
        $url = 'https://api.qoo10.jp/GMKT.INC.Front.QAPIService/ebayjapan.qapi/ItemsLookup.GetGoodsOptionInfo';
        $data = [
               'ItemCode'=>'956628364',
               'SellerCode'=>''
               
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
                'QAPIVersion: 1.0',
                'GiosisCertificationKey: S5bnbfynQvOKjKOOi6rqr2kyaKwK_g_1__g_2_tyLHiSIJ7jifeGJ_g_2_tYHO_g_2_9lvlRVaBoXZdsquVBC3VERFI1swTZz8Jx_g_2_LTtMbTKziDCTpt62xvJx39u3jjQuqwyYOeGwPXWePCD',

            ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 36000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $result_code = json_decode($server_output);
        // $result_code = $result_code->ResultCode;
    
        return response()->json([

            'data' => $result_code
        ]);
    }
    

}