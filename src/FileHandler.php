<?php

namespace App;

use App\Model\Country;

class FileHandler
{
    /**
     * @var int App launch timestamp
     */
    private $timestamp;

    /**
     * @var bool|resource File Resource
     */
    private $fileStream;

    /**
     * FileHandler constructor.
     *
     * @param Country $country
     * @param int $timestamp
     */
    public function __construct(Country $country, int $timestamp)
    {
        $this->timestamp = $timestamp;

        if (!is_dir($timestamp)) {
            mkdir($timestamp, 0777);
        }

        return $this->fileStream = fopen("./{$this->timestamp}/{$country->name}.csv", 'w');
    }

    /**
     * Save array as CSV data
     *
     * @param array $data
     */
    public function csv(array $data)
    {
        if (is_array($data[0])) {
            // Array of rows
            foreach ($data as $row) {
                fwrite($this->fileStream, implode(';', $row) . PHP_EOL);
            }
        } else {
            // Single row
            fwrite($this->fileStream, implode(';', $data) . PHP_EOL);
        }
    }

    /**
     * Save file
     */
    public function save()
    {
        fclose($this->fileStream);
    }
}