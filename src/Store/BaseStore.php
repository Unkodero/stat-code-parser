<?php
/**
 * Created by PhpStorm.
 * User: Unkodero
 * Date: 08.09.2017
 * Time: 17:18
 */

namespace App\Store;


use App\Model\BaseModel;

class BaseStore
{
    /**
     * @var array Store collection
     */
    public $store = [];

    /**
     * @param BaseModel $model
     */
    public function push(BaseModel $model)
    {
        $this->store[] = $model;
    }

}