<?php

namespace App\Model;

use App\Store\Pages;

class Country extends BaseModel
{
    /**
     * @var string Country Name
     */
    public $name;

    /**
     * @var string Country URL
     */
    public $url;

    /**
     * @var Pages
     */
    private $pagesStore;

    /**
     * Country constructor.
     *
     * @param string $name
     * @param string $url
     */
    public function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @return Pages
     */
    public function getPagesStore()
    {
        return $this->pagesStore;
    }

    /**
     * @param Pages $pagesStore
     */
    public function setPagesStore(Pages $pagesStore)
    {
        $this->pagesStore = $pagesStore;
    }

}