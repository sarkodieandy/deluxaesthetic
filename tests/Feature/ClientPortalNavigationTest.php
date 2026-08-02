<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ClientPortalNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_account_portal_routes_are_not_registered(): void
    {
        foreach ([
            'client.dashboard',
            'client.appointments.index',
            'client.consultations.index',
            'client.payments.index',
            'client.orders.index',
            'client.loyalty.index',
            'client.notifications.index',
            'client.profile.edit',
        ] as $routeName) {
            $this->assertFalse(Route::has($routeName));
        }
    }
}
