<?php

namespace App\Session;

use App\Session\Concerns\RecoversCorruptSessionPayloads;
use Illuminate\Session\EncryptedStore;

class ResilientEncryptedStore extends EncryptedStore
{
    use RecoversCorruptSessionPayloads;
}
