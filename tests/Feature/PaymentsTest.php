<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     ** @test
     */
    public function not_authenticated_user_cant_create_a_new_payment()
    {
        $this->withoutExceptionHandling([AuthenticationException::class]);
        $user = User::factory()->create();
        $response = $this->get('payments/new');
            $response->assertStatus(302)->assertRedirect('login');
    }

    /**
     ** @test
     */
    public function customer_can_see_a_form_for_creating_new_payment()
    {
        $this->withoutExceptionHandling();
//       $user = factory(User::class, 1)->create();
        $user = User::factory()->create();
        $this->actingAs($user)->get('payments/new')
            ->assertStatus(200)
            ->assertSee('Create new Payment');
    }

    /**
     ** @test
     */
    public function not_authenticated_user_cant_create_a_new_payment2()
    {
        $response = $this->json('post', "payments", [
            'email' => 'bradle@cooper.com',
            'amount' => 5000,
            'currency' => 'usd',
            'name' => 'Bradley Cooper',
            'description' => "Pay me. Now",
            'message' => 'Hello'
        ]);
        $response->assertStatus(401);
        $this->assertEquals(0, Payment::count());
    }

    /**
     ** @test
     */
    public function user_can_create_a_new_payment()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->json('post', "payments", [
            'email' => 'bradle@cooper.com',
            'amount' => 5000,
            'currency' => 'usd',
            'name' => 'Bradley Cooper',
            'description' => "Pay me. Now",
            'message' => 'Hello'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(1, Payment::count());
        tap(Payment::first(), function($payment) use ($user) {
            $this->assertEquals($user->id, $payment->user_id);
            $this->assertEquals('bradle@cooper.com', $payment->email);
            $this->assertEquals(5000, $payment->amount);
            $this->assertEquals('usd', $payment->currency);
            $this->assertEquals('Bradley Cooper', $payment->name);
            $this->assertEquals('Pay me. Now', $payment->description);
            $this->assertEquals('Hello', $payment->message);
        });
    }

    /**
     ** @test
     */
    public function email_field_is_required_to_create_a_new_payment()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->json('post', "payments", [
//            'email' => 'bradle@cooper.com',
            'amount' => 5000,
            'currency' => 'usd',
            'name' => 'Bradley Cooper',
            'description' => "Pay me. Now",
            'message' => 'Hello'
        ]);
        $response->assertStatus(422);
        $this->assertEquals(0, Payment::count());
        $response->assertJsonValidationErrors('email');
    }

    /**
     ** @test
     */
    public function email_field_should_be_a_valid_email_to_create_a_new_payment()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->json('post', "payments", [
            'email' => 'not-valid-email',
            'amount' => 5000,
            'currency' => 'usd',
            'name' => 'Bradley Cooper',
            'description' => "Pay me. Now",
            'message' => 'Hello'
        ]);
        $response->assertStatus(422);
        $this->assertEquals(0, Payment::count());
        $response->assertJsonValidationErrors('email');
    }

    /**
     ** @test
     */
    public function amount_field_should_be_integer_to_create_a_new_payment()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->json('post', "payments", [
            'email' => 'bradle@cooper.com',
            'amount' => 'some-amount',
            'currency' => 'usd',
            'name' => 'Bradley Cooper',
            'description' => "Pay me. Now",
            'message' => 'Hello'
        ]);
        $response->assertStatus(422);
        $this->assertEquals(0, Payment::count());
        $response->assertJsonValidationErrors('amount');
    }

}
