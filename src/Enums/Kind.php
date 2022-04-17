<?php

namespace Shokme\OneSignal\Enums;

enum Kind: int
{
    case Dashboard = 0;
    case Api = 1;
    case Automated = 3;
}
