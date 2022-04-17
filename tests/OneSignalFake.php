<?php

namespace Shokme\OneSignal\Tests;

use Illuminate\Support\Facades\Http;
use Shokme\OneSignal\Enums\Channel;

class OneSignalFake extends \Shokme\OneSignal\OneSignal
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
