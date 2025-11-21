<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト用の有効なユーザーデータを取得するヘルパーメソッド
     *
     * @param array $overrides 上書きするデータ
     * @return array
     */
    protected function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ], $overrides);
    }

    /**
     * [ID 1] 名前が入力されていない場合、バリデーションメッセージが表示されること
     *
     * @return void
     */
    public function test_name_is_required_for_registration(): void
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', $this->getValidData(['name' => '']));

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * [ID 1] メールアドレスが入力されていない場合、バリデーションメッセージが表示されること
     *
     * @return void
     */
    public function test_email_is_required_for_registration(): void
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', $this->getValidData(['email' => '']));

        $response->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * [ID 1] パスワードが入力されていない場合、バリデーションメッセージが表示されること
     *
     * @return void
     */
    public function test_password_is_required_for_registration(): void
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', $this->getValidData([
            'password' => '',
            'password_confirmation' => '',
        ]));

        $response->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * [ID 1] パスワードが7文字以下の場合、バリデーションメッセージが表示されること
     *
     * @return void
     */
    public function test_password_must_be_at_least_8_characters(): void
    {
        $this->get('/register')->assertStatus(200);

        $shortPassword = 'short7!';
        $response = $this->post('/register', $this->getValidData([
            'password' => $shortPassword,
            'password_confirmation' => $shortPassword,
        ]));

        $response->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * [ID 1] パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示されること
     *
     * @return void
     */
    public function test_password_must_match_confirmation(): void
    {
        $this->get('/register')->assertStatus(200);

        $response = $this->post('/register', $this->getValidData([
            'password' => 'MyPassword1234',
            'password_confirmation' => 'DifferentPassword1',
        ]));

        $response->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * [ID 1] 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移されること
     *
     * @return void
     */
    public function test_user_can_register_with_valid_data_and_is_redirected(): void
    {
        $this->get('/register')->assertStatus(200);

        $validData = $this->getValidData();
        $response = $this->post('/register', $validData);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => $validData['name'],
            'email' => $validData['email'],
        ]);

        $response->assertRedirect('/mypage/profile_edit');

        $this->assertAuthenticated();
    }
}