<?php

namespace App;

use App\Core\Database\Model;


/**
 * Class Order
 * @package App
 */
class Order extends Model
{
    protected static $table = "orders";

    protected static $primary_key = "id";
}
