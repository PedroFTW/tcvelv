<?php

use App\Objects\Server;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ServerTest extends TestCase
{
    #[DataProvider('getValidData')]
    public function testJsonSerializable($data, $expectedJson): void
    {
        $server = new Server($data);
        $this->assertEquals($expectedJson, json_encode($server, JSON_UNESCAPED_UNICODE));
    }


    public function testEmptyFieldInEntryData(): void
    {
        //TODO: A DataProvider could be created to ensure failure in different positions of the entry array
        $data = [
            0 => "Dell R730XD2x Intel Xeon E5-2670v3",
            1 => "128GBDDR4",
            2 => "",
            3 => "AmsterdamAMS-01",
            4 => "€364.99"
        ];

        $this->expectException(Exception::class);
        new Server($data);
    }

    public static function getValidData(): array
    {
        return [
            [
                [
                    0 => "Dell R730XD2x Intel Xeon E5-2670v3",
                    1 => "128GBDDR4",
                    2 => "2x120GBSSD",
                    3 => "AmsterdamAMS-01",
                    4 => "€364.99"
                ],
                '{"model":"Dell R730XD2x Intel Xeon E5-2670v3","ramType":"DDR4","ramSize":128,"hddType":"SSD","hddStorageDistribution":"2x120GB","hddStorage":240,"location":"AmsterdamAMS-01","currency":"€","price":364.99}'
            ],
            [
                [
                    0 => "Dell R730XD2x Intel Xeon E5-2670v3",
                    1 => "64GBDDR3",
                    2 => "1x1TBSATA2",
                    3 => "AmsterdamAMS-02",
                    4 => "€99.99"
            ],
            '{"model":"Dell R730XD2x Intel Xeon E5-2670v3","ramType":"DDR3","ramSize":64,"hddType":"SATA2","hddStorageDistribution":"1x1TB","hddStorage":1000,"location":"AmsterdamAMS-02","currency":"€","price":99.99}'
            ]
        ];
    }
}