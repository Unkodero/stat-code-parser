<?php

namespace App;

use App\Model\Country;
use App\Model\Page;
use App\Store\Countries;
use App\Store\Pages;
use Requests;
use IjorTengab\ParseHtml;

class App
{
    /**
     * @return Countries
     */
    public static function getAvailableCountries()
    {
        $response = Requests::get(SITE_URI . 'post.html');

        $html = new parseHTML($response->body);
        $countriesHtml = $html->find('.postbody .content .post h3 a');

        $countriesStore = new Countries();

        foreach ($countriesHtml->getElements() as $countryHref) {
            $country = new ParseHtml($countryHref);

            $name = mb_convert_encoding($country->text(), 'utf-8', 'cp1251');
            $href = $country->attr('href');

            $countriesStore->push(new Country($name, $href));
        }

        unset($response, $html, $countriesHtml);

        return $countriesStore;
    }

    /**
     * @param Country $country
     * @return Country
     */
    public static function getCountryPages(Country $country)
    {
        $store = new Pages();
        $store->push(new Page(1, $country->url));

        $response = Requests::get(SITE_URI . $country->url);
        $html = new parseHTML($response->body);
        $pages = $html->find('.postbody .content a');

        foreach ($pages->getElements() as $pageHref) {
            $page = new ParseHtml($pageHref);

            $id = $page->text();

            if (preg_match('/^\d+/', $id)) {
                $store->push(new Page($id, $page->attr('href')));
            }
        }

        $country->setPagesStore($store);

        unset($response, $html, $pages, $store);

        return $country;
    }

    /**
     * @param Page $page
     * @return array
     */
    public static function parsePage(Page $page)
    {
        $csv = [];

        $response = Requests::get(SITE_URI . $page->url);
        $html = new parseHTML($response->body);

        $rows = $html->find('.postbody .content table.yes tr');

        foreach ($rows->getElements() as $rowHtml) {
            $csvRow = [];

            $row = new ParseHtml($rowHtml);
            $cells = $row->find('td');

            foreach ($cells->getElements() as $cellHtml) {
                $cell = new ParseHtml($cellHtml);
                $csvRow[] = mb_convert_encoding($cell->text(), 'utf-8', 'cp1251');
            }

            $csv[] = $csvRow;
        }

        unset($response, $html, $rows);

        return $csv;
    }
}
