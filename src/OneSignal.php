<?php

namespace Shokme\OneSignal;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Shokme\OneSignal\Enums\Channel;
use Shokme\OneSignal\Enums\Delay;
use Shokme\OneSignal\Enums\SignalType;

class OneSignal
{
    const API_URL = 'https://onesignal.com/api/v1';

    const ENDPOINT_NOTIFICATIONS = '/notifications';

    protected array|Channel $channels = Channel::All;

    protected PendingRequest $http;

    protected array $body;

    private array $segments = ['All'];

    private array $additionalParameters = [];

    public function __construct(protected string $appId, protected string $restApiKey)
    {
        $this->body['app_id'] = $appId;
        $this->http = Http::acceptJson()
            ->withHeaders(['Authorization' => "Basic $this->restApiKey"])
            ->baseUrl(self::API_URL)
            ->timeout(config('onesignal.http.timeout'))
            ->retry(config('onesignal.http.retry'));
    }

    public function title(array $headings): self
    {
        $this->body['headings'] = $headings;

        return $this;
    }

    public function subtitle(array $subtitle): self
    {
        $this->body['subtitle'] = $subtitle;

        return $this;
    }

    public function contents(array $contents): self
    {
        $this->body['contents'] = $contents;

        return $this;
    }

    public function url(string $url): self
    {
        $this->body['url'] = $url;

        return $this;
    }

    public function schedule(Carbon $date): self
    {
        $this->body['send_after'] = $date->toString();

        return $this;
    }

    /**
     * @param string $time to be used with $delay: timezone
     */
    public function delay(Delay $delay, string $time = null): self
    {
        $this->body['delayed_option'] = $delay;
        
        if ($time) {
            $this->body['delivery_time_of_day'] = $time;
        }

        return $this;
    }

    public function segments(array $segments): self
    {
        $this->segments = $segments;

        return $this;
    }

    /**
     * @param  array<Channel>  $channels
     */
    public function channels(array $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    public function channel(Channel $channel): self
    {
        $this->channels = $channel;

        return $this;
    }

    public function filters($filters): self
    {
        $this->body['filters'] = $filters;

        return $this;
    }

    public function filter(string $field, string $key, string $operator = null, string $value = null): self
    {
        if (func_num_args() === 3) {
            $value = $operator;
            $operator = '=';
        }

        $this->body['filters'][] = [
            'field' => $field,
            'key' => $key,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    public function parameters(array $parameters = []): self
    {
        $this->additionalParameters = $parameters;

        return $this;
    }

    public function sendTo(SignalType $type, array $data = []): Response
    {
        $this->body['included_segments'] = $this->segments;

        if ($type === SignalType::Users) {
            $this->body['include_external_user_ids'] = $this->toStringArray($data);
            $this->body['channel_for_external_user_ids'] = $this->channels;
        } elseif ($type === SignalType::Players) {
            $this->body['include_player_ids'] = $this->toStringArray($data);
        } elseif ($type === SignalType::Segments) {
            $this->body['included_segments'] = $data;
        } elseif ($type === SignalType::Filters) {
            $this->body['filters'] = $data;
        }

        $body = [...$this->body, ...$this->additionalParameters];

        return $this->http->post(self::ENDPOINT_NOTIFICATIONS, $body);
    }

    private function toStringArray(array|Collection $userIds): array
    {
        if (is_array($userIds)) {
            $userIds = collect($userIds);
        }

        return $userIds->map(fn ($id) => (string) $id)->toArray();
    }

    // TODO: buttons
    // TODO: delete scheduled notifications
    // TODO: get notifications
    // TODO: get notification by id
}
