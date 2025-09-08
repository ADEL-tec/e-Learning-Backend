<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PayController extends Controller
{
    public function checkout(Request $request)
    {

        try {
            $user = $request->user();
            $token = $user->token;
            $courseId = $request->id;



            Stripe::setApiKey(env('STRIPE_API_KEY'));

            $courseResult = Course::where('id', '=', $courseId)->first();

            if (empty($courseResult)) {
                // invalid request
                return response()->json([
                    'code' => 400,
                    'msg' => 'Course doesn\'t exist',
                ], 400);
            }

            $orderMap = [];

            $orderMap['course_id'] = $courseId;
            $orderMap['user_token'] = $token;
            $orderMap['status'] = 1;

            ///  if the order has been placed befor or not
            /// so we need order model

            $orderRes = Order::where($orderMap)->first();

            if (!empty($orderRes)) {
                return response()->json([
                    'code' => 400,
                    'msg' => 'You already boughtt this course',
                    'data' => "",
                ]);
            }

            /// New order for the user 

            $YOUR_DOMAIN = env('APP_URL');
            $map = [];
            $map['user_token'] = $token;
            $map['course_id']  = $courseId;
            $map['total_amount'] = $courseResult->price;
            $map['status'] = 0;
            $map['created_at'] = Carbon::now();
            $orderNum = Order::insertGetId($map);

            // Create payment session
            $checkOutSession = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'USD',
                        'product_data' => [
                            'name' => $courseResult->name,
                            'description' => $courseResult->description,
                        ],
                        'unit_amount' => intval(($courseResult->price) * 100),
                    ],
                    'quantity' => 1,
                ]],
                'payment_intent_data' => [
                    'metadata' => ['order_num' => $orderNum, 'user_token' => $token],
                ],
                'metadata' => ['order_num' => $orderNum, 'user_token' => $token],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . 'success',
                'cancel_url' => $YOUR_DOMAIN . 'cancel',
            ]);

            return response()->json([
                'code' => 200,
                'msg' => 'Success',
                'data' => $checkOutSession->url,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function webGoHooks()
    {
        Log::info('starts here.....');
        Stripe::setApiKey(env('STRIPE_API_KEY'));
        $endPointSecret = env('STRIPE_WEBHOOKS_SIGNING_SECRET_KEY');
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        Log::info('set up buffer and handshake done.....');
        try {
            $event =  \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endPointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::info('UnexpectedValueException ' . $e);
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::info('SignatureVerificationException ' . $e);
            http_response_code(400);
            exit();
        }

        if ($event->type == "charge.succeeded") {
            $session = $event->data->object;
            $metadata = $session["metadata"];
            $orderNum = $metadata->order_num;
            $userToken = $metadata->user_token;
            Log::info('order id' . $orderNum);
            $map = [];
            $map['status'] = 1;
            $map['updated_at'] = Carbon::now();
            $whereMap = [];
            $whereMap['user_token'] = $userToken;
            $whereMap['id'] = $orderNum;
            Order::where($whereMap)->update($map);
        }

        http_response_code(200);
    }
}
