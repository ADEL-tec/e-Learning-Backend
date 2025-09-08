<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Traits\DefaultDatetimeFormat;

class Order extends Model
{
    use DefaultDatetimeFormat;
    use HasFactory;
}
