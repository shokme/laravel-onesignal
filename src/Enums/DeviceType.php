<?php

namespace Shokme\OneSignal\Enums;

enum DeviceType: int
{
    case Ios = 0;
    case Android = 1;
    case Amazon = 2;
    case WindowsPhone = 3;
    case ChromeExtension = 4;
    case ChromeWebPush = 5;
    case Windows = 6;
    case Safari = 7;
    case Firefox = 8;
    case Macos = 9;
    case Alexa = 10;
    case Email = 11;
    case Huawei = 13;
    case Sms = 14;
}
