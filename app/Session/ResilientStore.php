<?php

namespace App\Session;

use App\Session\Concerns\RecoversCorruptSessionPayloads;
use Illuminate\Session\Store;

class ResilientStore extends Store
{
    use RecoversCorruptSessionPayloads;
}
