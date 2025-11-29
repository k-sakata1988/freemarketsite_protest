<?php
//2,3_ログイン機能、ログアウト機能
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * テストで使用するユーザー情報。
     * ログインテストの前にこのユーザーを作成します。
     */
    protected const TEST_EMAIL = 'login@example.com';
    protected const TEST_PASSWORD = 'correctpassword';

    /**
     * 各テストメソッドの前に実行される処理。
     * ログインテストに使用するユーザーをデータベースに作成します。
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::TEST_PASSWORD),
        ]);
    }

    /**
     * [ID 2] メールアドレスが入力されていない場合、バリデーションメッセージが表示されること
     *
     * @return void
     */
    /** @test */
    public function メールアドレスが入力されていない場合、バリデーションメッセージが表示されること()
    {
        $this->get('/login')->assertStatus(200);

        $response = $this->post('/login', [
            'email' => '',
            'password' => self::TEST_PASSWORD,
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /**
     * [ID 2] パスワードが入力されていない場合、バリデーションメッセージが表示されること
     *
     * @return void
     */
    /** @test */
    public function パスワードが入力されていない場合、バリデーションメッセージが表示されること()
    {
        $this->get('/login')->assertStatus(200);

        $response = $this->post('/login', [
            'email' => self::TEST_EMAIL,
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    /**
     * [ID 2] 入力情報が間違っている場合、バリデーションメッセージが表示されること
     * (メールアドレスまたはパスワードが不正な場合)
     *
     * @return void
     */
    /** @test */
    public function 入力情報が間違っている場合、バリデーションメッセージが表示されること()
    {
        $this->get('/login')->assertStatus(200);

        $response = $this->post('/login', [
            'email' => self::TEST_EMAIL,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /**
     * [ID 2] 正しい情報が入力された場合、ログイン処理が実行されること
     *
     * @return void
     */
    /** @test */
    public function 正しい情報が入力された場合、ログイン処理が実行されること()
    {
        $this->get('/login')->assertStatus(200);

        $response = $this->post('/login', [
            'email' => self::TEST_EMAIL,
            'password' => self::TEST_PASSWORD,
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticated();
    }


    /**
     * [ID 3] ログアウトができること
     *
     * @return void
     */
    /** @test */
    public function ログアウトができること()
    {
        $user = User::where('email', self::TEST_EMAIL)->first();
        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}