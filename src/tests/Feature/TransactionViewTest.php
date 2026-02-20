<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Purchase;

class TransactionViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_purchased_items_on_mypage()
    {
        $user = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $purchase = \App\Models\Purchase::factory()
            ->purchased()
            ->create([
                'item_id' => $item->id,
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    /** @test */
    public function other_users_trades_are_not_displayed()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        \App\Models\Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $otherUser->id,
            'status' => 'purchased',
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertDontSee($item->name);
    }

    /** @test */
    public function unread_message_count_is_displayed()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = \App\Models\Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        Message::factory()->count(3)->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $seller->id,
            'receiver_id' => $user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertSee('3');
    }


    /** @test */
    public function zero_message_count_is_displayed()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        \App\Models\Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertSee('0');
    }

    /** @test */
    public function multiple_message_count_is_correct()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = \App\Models\Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        Message::factory()->count(5)->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $seller->id,
            'receiver_id' => $user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertSee('5');
    }

    /** @test */
    public function guest_cannot_access_mypage()
    {
        $response = $this->get(route('mypage.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_access_mypage()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_navigate_to_chat_from_mypage()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = \App\Models\Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'purchased',
        ]);

        $response = $this->actingAs($buyer)
            ->get(route('mypage.index'));

        $response->assertSee(route('chat.show', $purchase));

        $chatResponse = $this->actingAs($buyer)
            ->get(route('chat.show', $purchase));

        $chatResponse->assertStatus(200);
    }

    /** @test */
    public function user_can_switch_to_other_trade_from_sidebar()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item1 = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase1 = \App\Models\Purchase::factory()->create([
            'item_id' => $item1->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        $item2 = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase2 = \App\Models\Purchase::factory()->create([
            'item_id' => $item2->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        $response = $this->actingAs($user)
            ->get(route('chat.show', $purchase1));

        $response->assertStatus(200);

        $response->assertSee($item2->name);

        $response2 = $this->actingAs($user)
            ->get(route('chat.show', $purchase2));

        $response2->assertStatus(200);
        $response2->assertSee($item2->name);
    }

    /** @test */
    public function trades_are_sorted_by_latest_message_desc()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $itemA = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Old Trade Item'
        ]);

        $purchaseA = \App\Models\Purchase::factory()->create([
            'item_id' => $itemA->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        $itemB = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'New Trade Item'
        ]);

        $purchaseB = \App\Models\Purchase::factory()->create([
            'item_id' => $itemB->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        \App\Models\Message::factory()->create([
            'purchase_id' => $purchaseA->id,
            'sender_id' => $seller->id,
            'receiver_id' => $user->id,
            'created_at' => now()->subDays(1),
        ]);

        \App\Models\Message::factory()->create([
            'purchase_id' => $purchaseB->id,
            'sender_id' => $seller->id,
            'receiver_id' => $user->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            'New Trade Item',
            'Old Trade Item'
        ]);
    }

    /** @test */
    public function only_unread_messages_are_counted_for_notification()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = \App\Models\Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        Message::factory()->count(2)->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $seller->id,
            'receiver_id' => $user->id,
            'read_at' => null,
        ]);

        Message::factory()->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $seller->id,
            'receiver_id' => $user->id,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertSee('2');
    }

    /** @test */
    public function messages_not_addressed_to_user_are_not_counted()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $otherUser = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = \App\Models\Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

        Message::factory()->count(3)->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $seller->id,
            'receiver_id' => $otherUser->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertSee('0');
    }

    /** @test */
    public function average_rating_is_displayed_correctly()
    {
        $user = User::factory()->create();

        $buyer1 = User::factory()->create();
        $item1 = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        Purchase::factory()->create([
            'item_id' => $item1->id,
            'user_id' => $buyer1->id,
            'status' => 'completed',
            'buyer_rating' => 4,
        ]);

        $seller2 = User::factory()->create();
        $item2 = Item::factory()->create([
            'user_id' => $seller2->id,
        ]);

        Purchase::factory()->create([
            'item_id' => $item2->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'seller_rating' => 5,
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertSee('5');
    }

    /** @test */
    public function average_rating_is_rounded()
    {
        $user = User::factory()->create();

        $buyer1 = User::factory()->create();
        $item1 = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        Purchase::factory()->create([
            'item_id' => $item1->id,
            'user_id' => $buyer1->id,
            'status' => 'completed',
            'buyer_rating' => 3,
        ]);

        $buyer2 = User::factory()->create();
        $item2 = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        Purchase::factory()->create([
            'item_id' => $item2->id,
            'user_id' => $buyer2->id,
            'status' => 'completed',
            'buyer_rating' => 4,
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertSee('4');
    }

    /** @test */
    public function rating_is_not_displayed_when_no_rating_exists()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('mypage.index'));

        $response->assertSee('まだ評価はありません');
        $response->assertDontSee('★');
    }


}
