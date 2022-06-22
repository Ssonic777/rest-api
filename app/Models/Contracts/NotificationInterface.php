<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOne;

interface NotificationInterface
{
    public function notification(): HasOne;
}
