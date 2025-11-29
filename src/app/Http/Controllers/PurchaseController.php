<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Address;
// use Illuminate\Http\Request;
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
    public function store(PurchaseRequest $request, Item $item)
    {
        \Log::info('Current environment: ' . app()->environment());

        $user = Auth::user();

        if (!$address = Address::where('user_id', $user->id)->first()) {
            return back()->with('error', '配送先住所が登録されていません。');
        }

        if ($item->isSoldOut()) {
            return back()->with('error', 'この商品は売り切れました。');
        }

        if (app()->environment('testing')) {
            $paymentIntent = (object)['id' => 'pi_mock'];

            Purchase::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'address_id' => $address->id,
                'payment_method' => 'credit_card',
                'status' => 'purchased',
                'stripe_id' => $paymentIntent->id,
            ]);

            return redirect()->route('items.index')
                ->with('success', '商品を購入しました！（テスト動作）');
        }

        if (!$user->stripe_id) {
            try {
                $user->createAsStripeCustomer();
            } catch (\Exception $e) {
                \Log::error('Stripe顧客登録エラー: ' . $e->getMessage());
                return back()->with('error', 'Stripe顧客の初期設定に失敗しました。');
            }
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            if ($request->payment_method_type === 'credit') {

                $paymentIntent = PaymentIntent::create([
                    'amount' => $item->price,
                    'currency' => 'jpy',
                    'payment_method' => $request->payment_method_id,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'customer' => $user->stripe_id,
                ]);

                $status = 'purchased';
                $redirect = redirect()->route('items.index')
                ->with('success', '商品を購入しました！決済が完了しています。');
            } elseif ($request->payment_method_type === 'convenience') {
                $intent = PaymentIntent::create([
                    'amount' => $item->price,
                    'currency' => 'jpy',
                    'payment_method_types' => ['konbini'],
                    'customer' => $user->stripe_id,
                    'confirm' => true,
                    'return_url' => route('purchase.return', $item->id),
                ]);

                $status = 'pending';
                $redirect = redirect()->away($intent->next_action->konbini_display_details->hosted_voucher_url);
            } else {
                throw new \Exception('無効な支払い方法が選択されました。');
            }


            Purchase::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'address_id' => $address->id,
                'payment_method' => $request->payment_method_type,
                'status' => $status,
                'stripe_id' => $paymentIntent->id ?? $intent->id,
            ]);

            return $redirect;
        } catch (\Exception $e) {
            \Log::error('決済エラー: ' . $e->getMessage());
            return back()->with('error', '決済処理に失敗しました：' . $e->getMessage());
        }
    }


    public function konbiniReturn(Item $item)
    {
        return redirect()->route('items.index')->with('info', 'コンビニ決済の手続きが完了しました。コンビニでお支払い後、購入が確定します。');
    }

}
