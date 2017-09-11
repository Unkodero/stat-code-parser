<?php
/**
 * Created by PhpStorm.
 * User: Unkodero
 * Date: 08.09.2017
 * Time: 17:10
 */

namespace App\Model;


class Page extends BaseModel
{
    /**
     * @var int Page ID
     */
    public $id;

    /**
     * @var string Page URL
     */
    public $url;

    /**
     * Page constructor.
     *
     * @param int $id
     * @param string $url
     */
    public function __construct(int $id, string $url)
    {
        $this->id = $id;
        $this->url = $url;
    }
}