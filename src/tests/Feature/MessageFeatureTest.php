<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MessageFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createPurchaseWithUsers()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        $purchase = Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        return [$buyer, $seller, $purchase];
    }

    /** @test */
    public function message_requires_text()
    {
        [$buyer, $seller, $purchase] = $this->createPurchaseWithUsers();

        $this->actingAs($buyer)
            ->post(route('chat.store', $purchase), [
                'message' => '',
            ])
            ->assertSessionHasErrors('message');
    }

    /** @test */
    public function message_must_be_within_400_characters()
    {
        [$buyer, $seller, $purchase] = $this->createPurchaseWithUsers();

        $longText = str_repeat('a', 401);

        $this->actingAs($buyer)
            ->post(route('chat.store', $purchase), [
                'message' => $longText,
            ])
            ->assertSessionHasErrors('message');
    }

    /** @test */
    public function image_must_be_png_or_jpeg()
    {
        Storage::fake('public');

        [$buyer, $seller, $purchase] = $this->createPurchaseWithUsers();

        $file = UploadedFile::fake()->create('file.pdf', 100);

        $this->actingAs($buyer)
            ->post(route('chat.store', $purchase), [
                'message' => 'test',
                'image' => $file,
            ])
            ->assertSessionHasErrors('image');
    }

    /** @test */
    public function user_can_edit_own_message()
    {
        [$buyer, $seller, $purchase] = $this->createPurchaseWithUsers();

        $message = Message::factory()->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $buyer->id,
            'receiver_id' => $seller->id,
            'message' => 'before',
        ]);

        $this->actingAs($buyer)
            ->put(route('messages.update', $message), [
                'message' => 'after',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'message' => 'after',
        ]);
    }

    /** @test */
    public function user_cannot_edit_others_message()
    {
        [$buyer, $seller, $purchase] = $this->createPurchaseWithUsers();

        $message = Message::factory()->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $seller->id,
            'receiver_id' => $buyer->id,
            'message' => 'original',
        ]);

        $this->actingAs($buyer)
            ->put(route('messages.update', $message), [
                'message' => 'hacked',
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_own_message()
    {
        [$buyer, $seller, $purchase] = $this->createPurchaseWithUsers();

        $message = Message::factory()->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $buyer->id,
            'receiver_id' => $seller->id,
        ]);

        $this->actingAs($buyer)
            ->delete(route('messages.destroy', $message))
            ->assertRedirect();

        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_message()
    {
        [$buyer, $seller, $purchase] = $this->createPurchaseWithUsers();

        $message = Message::factory()->create([
            'purchase_id' => $purchase->id,
            'sender_id' => $seller->id,
            'receiver_id' => $buyer->id,
        ]);

        $this->actingAs($buyer)
            ->delete(route('messages.destroy', $message))
            ->assertStatus(403);
    }
}
