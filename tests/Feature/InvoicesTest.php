<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoicesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     ** @test
     */
    public function not_authenticated_user_cant_create_a_new_invoice()
    {
        $this->withoutExceptionHandling([AuthenticationException::class]);
        $user = User::factory()->create();
        $response = $this->get('invoices/new');
            $response->assertStatus(302)->assertRedirect('login');
    }


    /**
     ** @test
     */
    public function customer_can_see_a_form_for_creating_new_invoice()
    {
        $this->withoutExceptionHandling();
        //        $user = factory(User::class, 1)->create();
        $user = User::factory()->create();
        $this->actingAs($user)->get('invoices/new')
            ->assertStatus(200)
            ->assertSee('Create new Invoice');
    }
}
