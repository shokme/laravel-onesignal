<?php

namespace Shokme\OneSignal\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Shokme\OneSignal\Enums\Channel;
use Shokme\OneSignal\Enums\DeviceType;
use Shokme\OneSignal\Enums\Kind;
use Shokme\OneSignal\Enums\SignalType;
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
    public function it_can_send_to_segment()
    {
        OneSignal::sendTo(SignalType::Segments, ['Active Segment', 'Users Segment']);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);

            return $body['included_segments'][0] === 'Active Segment' && $body['included_segments'][1] === 'Users Segment';
        });
    }

    /** @test */
    public function it_can_send_to_filters()
    {
        OneSignal::filter('tag', 'group', 54)->sendTo(SignalType::Filters);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);

            return $body['filters'][0] === ['field' => 'tag', 'key' => 'group', 'relation' => '=', 'value' => '54'];
        });
    }

    /** @test */
    public function it_can_send_to_users()
    {
        OneSignal::make()
            ->subject('Test Subject')
            ->contents(['en' => 'English Message', 'fr' => 'French Message', 'nl' => 'Dutch Message'])
            ->url('https://example.com')
            ->channel(Channel::Push)
            ->sendTo(SignalType::Users, [1, 2, 3]);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);

            return $body['headings'] === ['en' => 'Test Subject']
                && $body['contents'] === ['en' => 'English Message', 'fr' => 'French Message', 'nl' => 'Dutch Message']
                && $body['url'] === 'https://example.com'
                && $body['include_external_user_ids'] === ['1', '2', '3']
                && $body['channel_for_external_user_ids'] === 'push';
        });
    }

    /** @test */
    public function it_can_send_to_players()
    {
        OneSignal::sendTo(SignalType::Players, ['9d1f3d06-2ada-47b2-a1d7-a32a22628782']);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);

            return $body['include_player_ids'][0] === '9d1f3d06-2ada-47b2-a1d7-a32a22628782';
        });
    }

    /** @test */
    public function it_can_send_to_all()
    {
        OneSignal::sendTo(SignalType::All);

        Http::assertSentCount(1);
    }

    /** @test */
    public function it_can_send_to_all_with_additional_parameters()
    {
        OneSignal::parameters([
            'include_player_ids' => ['9d1f3d06-2ada-47b2-a1d7-a32a22628782'],
        ])->sendTo(SignalType::All);

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);

            return $body['include_player_ids'][0] === '9d1f3d06-2ada-47b2-a1d7-a32a22628782';
        });
    }

    /** @test */
    public function it_can_cancel_a_notification()
    {
        OneSignal::cancel('test-notification-id');

        Http::assertSentCount(1);
    }

    /** @test */
    public function it_can_get_all_notifications()
    {
        OneSignal::getNotifications(Kind::Api);

        Http::assertSent(function (Request $request) {
            $data = $request->data();

            return $data === ['app_id' => 'app-id', 'limit' => 50, 'offset' => 0, 'kind' => 1];
        });
    }

    /** @test */
    public function it_can_get_notification()
    {
        OneSignal::getNotification('test-id', ['outcome_names' => 'os__click.count']);

        Http::assertSent(function (Request $request) {
            $data = $request->data();

            return $data === ['app_id' => 'app-id', 'outcome_names' => 'os__click.count'];
        });
    }

    /** @test */
    public function it_can_add_player()
    {
        OneSignal::addPlayer(DeviceType::Android, timezone: 'Australia/Sydney');

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);

            return $body === ['app_id' => 'app-id', 'device_type' => 1, 'timezone' => 36000];
        });
    }

    /** @test */
    public function it_can_edit_player()
    {
        OneSignal::editPlayer('test-player-android-id', timezone: 'America/New_York');

        Http::assertSent(function (Request $request) {
            $body = json_decode($request->body(), true);

            return $body === ['app_id' => 'app-id', 'timezone' => -14400];
        });
    }
}
