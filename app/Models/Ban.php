<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Mchev\Banhammer\Models\Ban as BanhammerBan;

class Ban extends BanhammerBan
{
    use HasUuids;
}
