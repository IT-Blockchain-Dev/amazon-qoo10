<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductInformation;
use Illuminate\Support\Facades\Log;
use App\Models\Store;
use App\Models\ProductImage;

class ListingController extends Controller
{
    //
    public function index(){
        return view('front.listing.listing');
    }

    public function listProducts(Request $request){
        
      $user_id = $request -> user_id;
      $importname_id = $request -> importname_id;
      $qoo10_auth_key = Store::where(['user_id' => $user_id]) -> get();
      $price_multiple = $qoo10_auth_key[0]['price_multiple'];
      $search_result = ProductInformation::where(['user_id' => $user_id,'importname_id' => $importname_id])->get();
      
      $products = ProductInformation::select("product_information.*","table_categorymatching.qoo10_category_name","table_categorymatching.qoo10_category_id")
                ->join("table_categorymatching",function($join){
                     $join -> on("table_categorymatching.amazon_category_name","=","product_information.category");
                })->where(['product_information.user_id'=>$user_id,'product_information.importname_id'=>$importname_id])->get();
    
      foreach($products as $product){
       
        $price = $product['price'] * $price_multiple;
        $url = 'http://api.qoo10.jp/GMKT.INC.Front.QAPIService/ebayjapan.qapi/ItemsBasic.SetNewGoods';
        $data = [

            'SecondSubCat' => $product['qoo10_category_id'],
            'OuterSecondSubCat' => '',
            'Drugtype' => '',
            'BrandNo' => '0',
            'ItemTitle' => $product['title'],
            'PromotionName' => '',
            'SellerCode' => '',
            'IndustrialCodeType' => '',
            'IndustrialCode' => '',
            'ModelNM' => '',
            'ManufactureDate' => '',
            'ProductionPlaceType' => '3',
            'ProductionPlace' => 'no known',
            'Weight' => '',
            'Material' => '',
            'AdultYN' => '',
            'ContactInfo' => '',
            'AdditionalOption' => '',
            'StandardImage' => $product['main_imageURL'],
            'VideoURL' => '',
            'ItemDescription' => $product['description'],
            'ItemPrice' => $price,
            'ItemQty' => $product['quantity'],
            'RetailPrice' => '',
            'ExpireDate' => '',
            'ShippingNo' => '0',
            'AvailableDateType' => '0',
            'AvailableDateValue' => '3',
            'returnType' => 'json',
            'key' => $qoo10_auth_key[0]['qoo10_auth_key'],
            'Keyword' => '',
            'ItemType' => ''

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
                
                'QAPIVersion: 1.1'
            ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 36000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $result = json_decode($server_output);
        $GoodNo = $result->ResultObject->GdNo;
        Log::info($GoodNo);
        $register_item = ProductInformation::where([
                            'asin' => $product['asin']
                        ])
                        ->update([
                             'itemNo' => $GoodNo
                        ]);

        
      }
      
      return response()->json([
          'data' => 'true'
      ]);

   ///////////SetSellerCheck API

    // $url = 'https://api.qoo10.jp/GMKT.INC.Front.QAPIService/ebayjapan.qapi/ShippingBasic.SetSellerCheckYN_V2';
    //     $data = [

    //             'OrderNo' => '697129307',
    //             'EstShipDt' => '20211225',
    //             'DelayType' => '1',
    //             'DelayMemo' => 'aaa'
                

    //     ];

    //      $ch = curl_init();

    //     curl_setopt($ch, CURLOPT_URL,$url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
    //     curl_setopt(
    //         $ch, 
    //         CURLOPT_HTTPHEADER, 
    //         array(
    //             'Content-Type: application/x-www-form-urlencoded', // for define content type that is json
    //             'GiosisCertificationKey: S5bnbfynQvOKjKOOi6rqr2kyaKwK_g_1__g_2_tyLHiSIJ7jifeGJ_g_2_tYHO_g_2_9lvlRVaBoXZdsquVBC3VERFI1swTZz8Jx_g_2_LTtMbTKziDCTpt62xvJx39u3jjQuqwyYOeGwPXWePCD',
    //             'QAPIVersion: 1.0'
    //         ));
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 36000);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     $server_output = curl_exec($ch);
    //     curl_close ($ch);
 

    //     return response()->json([

    //         'data' => $server_output
    //     ]);


    }

    public function getLog(Request $request){
        $user_id = $request ->user_id;
        $importname_id = $request ->importname_id;
        $get_log = ProductInformation::where([
                        'user_id' => $user_id,
                        'importname_id' => $importname_id
                    ])->get();

        return response()->json([
            'data' => $get_log
        ]);
    }

    public function getManageProduct(Request $request){
         $user_id = $request ->user_id;
         $result = ProductInformation::where([
               'user_id' =>$user_id
         ])
         ->whereNotNull('itemNo')
         ->get();

         return response()->json([
                'data' => $result
         ]);
    }
}
