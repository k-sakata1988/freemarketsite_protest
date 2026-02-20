<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class TransactionCompletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function buyer_can_submit_rating()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status'  => 'purchased',
        ]);

        $response = $this->actingAs($buyer)->post(
            route('purchase.complete', $purchase),
            [
                'rating'     => 5,
                'rating_for' => 'seller',
            ]
        );

        $purchase->refresh();

        $this->assertEquals(5, $purchase->buyer_rating);

        $this->assertEquals('purchased', $purchase->status);

        $response->assertRedirect(route('items.index'));
    }

    /** @test */
    public function seller_can_submit_rating_and_complete_transaction()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = Purchase::factory()->create([
            'item_id'      => $item->id,
            'user_id'      => $buyer->id,
            'status'       => 'purchased',
            'buyer_rating' => 4,
        ]);

        $response = $this->actingAs($seller)->post(
            route('purchase.complete', $purchase),
            [
                'rating'     => 5,
                'rating_for' => 'buyer',
            ]
        );

        $purchase->refresh();

        $this->assertEquals(5, $purchase->seller_rating);

        $this->assertEquals('completed', $purchase->status);

        $this->assertNotNull($purchase->completed_at);

        $response->assertRedirect(route('items.index'));
    }

    /** @test */
    public function after_rating_submission_user_is_redirected_to_items_index()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status'  => 'purchased',
        ]);

        $response = $this->actingAs($buyer)->post(
            route('purchase.complete', $purchase),
            [
                'rating'     => 5,
                'rating_for' => 'seller',
            ]
        );

        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('success', '評価を送信しました');
    }

    /** @test */
    public function seller_receives_email_when_buyer_completes_transaction()
    {
        Mail::fake();

        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status'  => 'purchased',
        ]);

        $this->actingAs($buyer)->post(
            route('purchase.complete', $purchase),
            [
                'rating'     => 5,
                'rating_for' => 'seller',
            ]
        );

        Mail::assertSent(TransactionCompletedMail::class, function ($mail) use ($seller, $purchase) {
            return $mail->hasTo($seller->email)
                && $mail->purchase->id === $purchase->id;
        });
    }

}
