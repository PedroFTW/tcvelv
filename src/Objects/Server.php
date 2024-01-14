<?php

namespace App\Objects;

use App\Helper\ServerHelper;
use JsonSerializable;

class Server implements JsonSerializable
{
    private string $model;
    private string $ramType;
    private int $ramSize;
    private string $hddType;
    private int $hddStorage;
    private string $hddStorageDistribution;
    private string $location;
    private string $currency;
    private float $price;

    /*
            array:5 [
                0 => "Dell R730XD2x Intel Xeon E5-2670v3"
                1 => "128GBDDR4"
                2 => "2x120GBSSD"
                3 => "AmsterdamAMS-01"
                4 => "€364.99"
            ]
            */
    public function __construct(array $data)
    {
        $serverHelper = new ServerHelper();

        $serverHelper->validateServerData($data);

        $hddData = ServerHelper::getHddData($data[2]);
        $ramData = ServerHelper::getRamData($data[1]);
        $priceData = ServerHelper::getPriceData($data[4]);

        $this->model = $data[0];
        $this->ramType = $ramData['type'];
        $this->ramSize = $ramData['size'];
        $this->hddType = $hddData['type'];
        $this->hddStorageDistribution = $hddData['storageDistribution'];
        $this->hddStorage = $hddData['storage'];
        $this->location = $data[3];
        $this->currency = $priceData['currency'];
        $this->price = $priceData['price'];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'model' => $this->model,
            'ramType' => $this->ramType,
            'ramSize' => $this->ramSize,
            'hddType' => $this->hddType,
            'hddStorageDistribution' => $this->hddStorageDistribution,
            'hddStorage' => $this->hddStorage,
            'location' => $this->location,
            'currency' => $this->currency,
            'price' => $this->price,
        ];
    }
}
