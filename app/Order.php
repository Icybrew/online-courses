<?php

namespace App;

use App\Core\Database\Model;


/**
 * Class Order
 * @package App
 */
class Order extends Model
{
    protected $table = "orders";

    protected $primary_key = "id";
}
