<?php

namespace Shokme\OneSignal\Tests\Unit;

use Carbon\Carbon;
use Shokme\OneSignal\Enums\Channel;
use Shokme\OneSignal\Enums\Delay;
use Shokme\OneSignal\Facades\OneSignal;
use Shokme\OneSignal\Tests\TestCase;

class OneSignalTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        OneSignal::fake();
    }

    /** @test */
    public function it_can_set_message()
    {
        $data = OneSignal::make()
            ->subject('Test title')
            ->subtitle('Test subtitle')
            ->contents('Test message');

        $this->assertEquals(['en' => 'Test title'], $data->body['headings']);
        $this->assertEquals(['en' => 'Test subtitle'], $data->body['subtitle']);
        $this->assertEquals(['en' => 'Test message'], $data->body['contents']);
    }

    /** @test */
    public function it_can_set_translated_message()
    {
        $data = OneSignal::make()
            ->subject(['en' => 'Test title', 'es' => 'Test title es'])
            ->subtitle(['en' => 'Test subtitle', 'de' => 'Test subtitle de'])
            ->contents(['en' => 'Test message en', 'fr' => 'Test message fr']);

        $this->assertEquals(['en' => 'Test title', 'es' => 'Test title es'], $data->body['headings']);
        $this->assertEquals(['en' => 'Test subtitle', 'de' => 'Test subtitle de'], $data->body['subtitle']);
        $this->assertEquals(['en' => 'Test message en', 'fr' => 'Test message fr'], $data->body['contents']);
    }

    /** @test */
    public function it_can_set_an_url()
    {
        $data = OneSignal::url('https://app.com');

        $this->assertEquals('https://app.com', $data->body['url']);
    }


    /** @test */
    public function it_can_be_scheduled()
    {
        $data = OneSignal::schedule(Carbon::parse('17 april 2022')->timezone('GMT+3'));

        $this->assertEquals('Sun Apr 17 2022 03:00:00 GMT+0300', $data->body['send_after']);
    }

    /** @test */
    public function it_can_be_delayed()
    {
        $lastActive = OneSignal::delay(Delay::LastActive);

        $this->assertEquals(Delay::LastActive, $lastActive->body['delayed_option']);

        $timeZone = OneSignal::delay(Delay::Timezone, '9:00AM');

        $this->assertEquals(Delay::Timezone, $timeZone->body['delayed_option']);
        $this->assertEquals('9:00AM', $timeZone->body['delivery_time_of_day']);
    }

    /** @test */
    public function it_can_set_channel()
    {
        $data = OneSignal::channel(Channel::Sms);

        $this->assertEquals(Channel::Sms, $data->channels);
    }

    /** @test */
    public function it_can_set_channels()
    {
        $data = OneSignal::channels([Channel::Sms, Channel::Push]);

        $this->assertEquals([Channel::Sms, Channel::Push], $data->channels);
    }

    /** @test */
    public function it_throw_an_error_if_channel_not_enum()
    {
        $this->expectException(\InvalidArgumentException::class);

        OneSignal::channels(['push', 'sms']);
    }

    /** @test */
    public function it_can_set_filter()
    {
        OneSignal::filter('tag', 'test', 'test');
        $data = OneSignal::filter('tag', 'test2', '<=', 'test2');

        $this->assertEquals([
            ['field' => 'tag', 'key' => 'test', 'relation' => '=', 'value' => 'test'],
            ['field' => 'tag', 'key' => 'test2', 'relation' => '<=', 'value' => 'test2'],
        ], $data->body['filters']);
    }

    /** @test */
    public function it_can_set_filters()
    {
        $data = OneSignal::filters([
            ['field' => 'tag', 'key' => 'test', 'relation' => '=', 'value' => 'test'],
            ['field' => 'tag', 'key' => 'test2', 'relation' => '>=', 'value' => 'test2'],
        ]);

        $this->assertEquals([
            ['field' => 'tag', 'key' => 'test', 'relation' => '=', 'value' => 'test'],
            ['field' => 'tag', 'key' => 'test2', 'relation' => '>=', 'value' => 'test2'],
        ], $data->body['filters']);
    }
}
