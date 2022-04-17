# Laravel OneSignal
The `shokme/laravel-onesignal` package provides easy to use facade to send notification to your users via onesignal.
It uses the latest functionality of `PHP8.1`.

Here's a demo of how you can use it:
```php
$response = OneSignal::make()
    ->subject('Notification Title')
    ->contents(['en' => 'Notification Body', 'es' => 'Translated Notification Body'])
    ->url('en.myapp.service://')
    ->sendTo(SignalType::Users, [1, 2, 3]);
```
Find below the documentation of all available methods.

## Installation
You can install the package via composer:
```shell
composer require shokme/laravel-onesignal
```

define environment variables:
```dotenv
ONESIGNAL_APP_ID=your-app-id
ONESIGNAL_REST_API_KEY=your-api-key
```

You may want to publish the config file to update http timeout and retry:
```shell
php artisan vendor:publish --provider="Shokme\OneSignal\OneSignalServiceProvider" --tag="config"
```

## Documentation
### Message
You can use a string or array of language to set the message(title,subtitle,contents).
If you set a `string`, it will be set to 'en' for default language.
```php
OneSignal::subject('Notification Title'); // ['en' => 'Notification Title']
OneSignal::subject(['en' => 'Notification Title', 'nl' => 'Translated Notification Title']);
```

### Url
You can set the url to open when user click the notification.
```php
OneSignal::url('https://example.com');
```
### Buttons
#### Mobile
```php
OneSignal::buttons(ButtonType::Mobile, [
    ['id' => 'action-1', 'text' => 'Action To Trigger'],
    ...
]);
```
#### Web
```php
OneSignal::buttons(ButtonType::Web, [
    ['id' => 'action-1', 'text' => 'Action To Trigger', 'url' => 'https://app.com'],
    ...
]);
```

### Additional Parameters
If you have to use some parameters that are not supported by the package, you can set them using this method:
```php
OneSignal::parameters(['template_id' => 'be4a8044-bbd6-11e4-a581-000c2940e62c'])
```

### Schedule and Delay
You can read the [OneSignal](https://documentation.onesignal.com/reference/create-notification#delivery) documentation for more information.
#### Schedule
is equal to `send_after` parameter.
```php
OneSignal::schedule(Carbon::parse('17 april 2022')->timezone('GMT+3'));
```
#### Delay
is equal to `delayed_option` parameter, if `timezone` is set you need to set the second arguments(`$time`). 
```php
$lastActive = OneSignal::delay(Delay::LastActive);
$timezone = OneSignal::delay(Delay::Timezone, '9:00AM');
```

### Channels
When using only one channel.
```php
OneSignal::channel(Channel::Sms);
```
When using multiple channel.
```php
OneSignal::channels([Channel::Sms, Channel::Push]);
```

### Filters
You can see [Available Filters](https://documentation.onesignal.com/reference/create-notification#send-to-users-based-on-filters).\
If you want to know how to [Format Filters](https://documentation.onesignal.com/reference/create-notification#formatting-filters).
```php
OneSignal::filters([
    ['field' => 'tag', 'key' => 'test', 'relation' => '=', 'value' => 'test'],
    ['field' => 'tag', 'key' => 'test2', 'relation' => '>=', 'value' => 'test2'],
])
```
or you can chain filter, watch out in some case you may need to combine with `filters()` because some fields aren't supported at the moment.
```php
OneSignal::filter('tag', 'level', 10)
    ->filter('amount_spent', '>', 0)
    ->filter('session_count', 5);

//[
//    ['field' => 'tag', 'key' => 'test', 'relation' => '=', 'value' => 10],
//    ['field' => 'amount_spent', 'relation' => '>=', 'value' => '0'],
//    ['field' => 'session_count', 'relation' => '=', 'value' => '5'],
//]
```

### Sending Notification
You can send multiple kind of notification.
By default, the notification use the `push` channel.
```php
$response = OneSignal::sendTo(SignalType::All);
$response = OneSignal::sendTo(SignalType::Users, [1,2,3]);
$response = OneSignal::channel(Channel::Sms)->sendTo(SignalType::Players, ['player_id-1', 'player_id-2']);
$response = OneSignal::sendTo(SignalType::Segments, ['Active Users', 'Inactive Users']);
$response = OneSignal::filters([...])->sendTo(SignalType::Filters);
```

### Notifications
#### Retrieve all
You can retrieve all notifications using:
```php
$response = OneSignal::getNotifications();
// Dashboard, Api or Automated
$response = OneSignal::getNotifications(Kind::Dashboard);
```
#### Retrieve one
You can add a second argument to specify your `$outcomes`.
Check the OneSignal outcomes [documentation](https://documentation.onesignal.com/reference/view-notification).
```php
$response = OneSignal::getNotification('notification_id');
```
#### Cancel
```php
$response = OneSignal::cancel('notification_id');
```

### Player
OneSignal Device [documentation](https://documentation.onesignal.com/reference/add-a-device)
#### Add
All device type can be found in Shokme\OneSignal\Enum\DeviceType.php.
```php
$response = OneSignal::addPlayer(DeviceType::Android, 'push-token-from-google', 'Australia/Sydney', [
    'device_model' => 'Nexus 5X',
]);
```
#### Edit
```php
$response = OneSignal::editPlayer('push-token-from-google', timezone: 'Europe/Brussels');
```

## Contribution
This package might not be complete as it is made for personal use.
If you want to contribute, please feel free to open an issue or pull request.
