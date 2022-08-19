<?php

namespace Shokme\OneSignal\Testing\Fakes;

use Illuminate\Support\Facades\Http;
use Shokme\OneSignal\Enums\Channel;
use Shokme\OneSignal\OneSignal;

class OneSignalFake extends OneSignal
{
    public array $body = [];

    public array|Channel $channels = Channel::All;

    public function __construct()
    {
        parent::__construct('app-id', 'test-key');

        $this->http = Http::fake()
            ->acceptJson()
            ->withHeaders(['Authorization' => "Basic test-key"])
            ->baseUrl('https://fake.com');
    }
}
