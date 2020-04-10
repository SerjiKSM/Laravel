<?php
/**
 * Created by PhpStorm.
 * User: sadmin
 * Date: 10.04.20
 * Time: 16:50
 */

namespace App\Contracts;


interface OrderContract
{
    public function storeOrderDetails($params);
}
