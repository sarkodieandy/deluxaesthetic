<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutLeadershipTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_introduces_the_complete_leadership_team(): void
    {
        $this->get(route('web.about'))
            ->assertOk()
            ->assertSee('Meet our leadership team.')
            ->assertSee('Dr Evelyn Ejaife')
            ->assertSee('Chief Executive Officer')
            ->assertSee('Egedge Gift Collins')
            ->assertSee('Manager')
            ->assertSee('Emmanuel Better Amos')
            ->assertSee('Assistant Manager')
            ->assertSee('Barinua Nainesi')
            ->assertSee('Human Resources Manager')
            ->assertSee('assets/web/images/leadership/dr-evelyn-ejaife.webp')
            ->assertSee('assets/web/images/leadership/egedge-gift-collins.webp')
            ->assertSee('assets/web/images/leadership/emmanuel-better-amos.webp')
            ->assertSee('assets/web/images/leadership/barinua-nainesi.webp');

        $this->get(route('web.home'))
            ->assertOk()
            ->assertDontSee('Meet our leadership team.');
    }
}
