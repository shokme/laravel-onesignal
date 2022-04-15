<?php

namespace Shokme\OneSignal\Enums;

enum Channel: string
{
    case All = 'all';
    case Push = 'push';
    case Email = 'email';
    case Sms = 'sms';
}
