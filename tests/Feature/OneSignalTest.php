<?php

namespace Shokme\OneSignal\Tests;

use Shokme\OneSignal\Enums\Channel;
use Shokme\OneSignal\Enums\SignalType;
use Shokme\OneSignal\Facades\OneSignal;

class OneSignalTest extends TestCase
{
    /** @test */
    public function it_can_send_a_push_notification()
    {
        // TODO: coverage testing
        $response = OneSignal::title([
            'en' => 'Test Title',
        ])->contents([
            'en' => 'Hello World',
        ])->channel(Channel::Push)->sendTo(SignalType::Users, [131019]);

        dd($response);
    }
}
