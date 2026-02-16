<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use Laravel\Cashier\Cashier;
use App\Models\Purchase;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PurchaseController extends Controller
{
    /**
     * 購入画面の表示とStripe SetupIntentの生成
     *
     * @param int $item_id
     * @return \Illuminate\View\View
     */
    public function create($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if (!$user->stripe_id) {
            try {
                $user->createAsStripeCustomer();
            } catch (\Exception $e) {
                \Log::error('Stripe顧客登録エラー: ' . $e->getMessage());
                return back()->with('error', 'Stripe顧客の初期設定に失敗しました。');
            }
        }

        $intent = $user->createSetupIntent();
        $address = Address::where('user_id', $user->id)->first();

        return view('purchase.create', compact('item', 'user', 'address', 'intent'));
    }


    /**
     * 決済処理
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Item $item)
    {
        $user = Auth::user();

        $paymentMethodType = $request->input('payment_method_type');

        $rules = ['payment_method_type' => 'required|string|in:credit,convenience'];
        if ($paymentMethodType === 'credit') {
            $rules['payment_method_id'] = 'required|string';
        }
        $request->validate($rules);

        $address = Address::where('user_id', $user->id)->first();
        if (!$address) {
            return back()->with('error', '配送先住所が登録されていません。');
        }

        if ($item->isSoldOut()) {
            return back()->with('error', 'この商品は売り切れました。');
        }
        if (app()->environment('testing')) {
            Purchase::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'address_id' => $address->id,
                'payment_method' => 'credit_card',
                'status' => 'purchased',
                'stripe_id' => 'pi_mock',
            ]);

            $item->update([
                'status' => 'sold'
            ]);

            return redirect()->route('items.index')->with('success', '商品を購入しました！（テスト動作）');
        }

        $status = null;
        $redirect = null;
        $stripeId = null;

        try {
            if ($paymentMethodType === 'credit') {
                $method = 'credit_card';
                $paymentMethodId = $request->input('payment_method_id');
                $user->addPaymentMethod($paymentMethodId);

                $payment = $user->charge(
                    $item->price,
                    $paymentMethodId,
                    [
                        'currency' => 'jpy',
                        'description' => '商品購入: ' . $item->name,
                    ]
                );

                $status = 'purchased';
                $stripeId = $payment->id;
                $redirect = redirect()->route('items.index')->with('success', '商品を購入しました！決済が完了しています。');
            } elseif ($paymentMethodType === 'convenience') {
                $method = 'convenience_store';
                Stripe::setApiKey(config('cashier.secret'));
                $intent = PaymentIntent::create([
                    'amount' => $item->price,
                    'currency' => 'jpy',
                    'payment_method_types' => ['konbini'],
                    'customer' => $user->stripe_id,
                    'description' => '商品購入(コンビニ): ' . $item->name,
                    'metadata' => [
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                ],
                'payment_method_data' => [
                    'type' => 'konbini',
                    'billing_details' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ],
                'payment_method_options' => [
                    'konbini' => [
                        'product_description' => $item->name,
                        'expires_after_days' => 7,
                    ],
                ],
                'confirm' => true,
                'return_url' => route('purchase.return', $item->id),
            ]);

            $status = 'pending';
            $stripeId = $intent->id;

            $redirect = redirect()->away(
            $intent->next_action->konbini_display_details->hosted_voucher_url
            );
            }else {
                throw new \Exception('無効な支払い方法が選択されました。');
            }
            Purchase::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'address_id' => $address->id,
                'method' => $method, 
                'status' => $status,
                'stripe_id' => $stripeId,
            ]);

            $item->update([
                'status' => 'sold'
            ]);

            return $redirect; 
        } catch (\Exception $e) {
            \Log::error('決済エラー: ' . $e->getMessage());
            return back()->with('error', '決済処理に失敗しました。:' . $e->getMessage());
        }
    }
    public function konbiniReturn(Item $item)
    {
        return redirect()->route('items.index')->with('info', 'コンビニ決済の手続きが完了しました。コンビニでお支払い後、購入が確定します。');
        }

}