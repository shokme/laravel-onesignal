<?php

namespace Shokme\OneSignal\Enums;

enum SignalType: string {
    case All = 'all';
    case Users = 'users';
    case Segments = 'segments';
    case Filters = 'filters';
    case Players = 'players';
}
