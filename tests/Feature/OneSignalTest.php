<?php

namespace Shokme\OneSignal\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Shokme\OneSignal\Enums\Channel;
use Shokme\OneSignal\Enums\SignalType;
use Shokme\OneSignal\Facades\OneSignal;
use Shokme\OneSignal\Tests\TestCase;

class OneSignalTest extends TestCase
{
    /** @test */
    public function it_can_send_a_push_notification()
    {
        Http::fake();

        OneSignal::title([
            'en' => 'Test Title',
        ])->contents([
            'en' => 'Hello World',
        ])->channel(Channel::Push)->sendTo(SignalType::Users, [131019]);

        Http::assertSentCount(1);
    }
}
