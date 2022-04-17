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

    /**
     * @var array<Channel>|Channel
     */
    protected array|Channel $channels = Channel::All;

    protected PendingRequest $http;

    protected array $body;

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

    public function make(): self
    {
        return $this;
    }

    public function subject(string|array $subject): self
    {
        if (is_string($subject)) {
            $subject =  [config('app.locale') => $subject];
        }

        $this->body['headings'] = $subject;

        return $this;
    }

    public function subtitle(string|array $subtitle): self
    {
        if (is_string($subtitle)) {
            $subtitle =  [config('app.locale') => $subtitle];
        }

        $this->body['subtitle'] = $subtitle;

        return $this;
    }

    public function contents(string|array $contents): self
    {
        if (is_string($contents)) {
            $contents =  [config('app.locale') => $contents];
        }

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

    /**
     * @param  array<Channel>  $channels
     */
    public function channels(array $channels): self
    {
        foreach ($channels as $channel) {
            if (! in_array($channel, Channel::cases())) {
                throw new \InvalidArgumentException("Invalid channel[$channel] must be an enum of ".Channel::class);
            }
        }

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
            'relation' => $operator,
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
