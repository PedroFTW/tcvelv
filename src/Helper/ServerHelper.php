<?php

namespace App\Helper;

use App\Objects\Server;
use Exception;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ServerHelper
{
    public const HDD_TYPES_PATTERN = "/(SAS|SATA2|SSD)/";
    public const HDD_TYPES = ['SAS', 'SATA2', 'SSD'];

    private ValidatorInterface $validator;

    public function validateServerObj(Server $server): void
    {
        $errors = $this->getValidator()->validate($server);

        if ($errors->count() != 0) {
            throw new Exception($errors->get(0)->getMessage());
        }
    }

    public function validateServerData(array $data): void
    {
        if (empty($data)) {
            throw new Exception("Cannot receive and empty array.");
        }

        foreach ($data as $field) {
            $errors = $this->getValidator()->validate($field, new NotBlank());

            if ($errors->count() != 0) {
                throw new Exception($errors->get(0)->getMessage());
            }
        }
    }

    public static function getRamData(string $rawRamData): array
    {
        $ramData = [];
        $ram = explode("GB", $rawRamData);
        $ramData['size'] = $ram[0];
        $ramData['type'] = $ram[1];

        return $ramData;
    }

    public static function getPriceData(string $rawPriceData): array
    {
        $priceData = [];
        $pricePos = strcspn($rawPriceData, '0123456789');
        $priceData['currency'] = substr($rawPriceData, 0, $pricePos);
        $priceData['price'] = substr($rawPriceData, $pricePos);

        return $priceData;
    }

    public static function getHddData(string $rawHddData): array
    {
        $hddData = [];
        $hdd = preg_split(self::HDD_TYPES_PATTERN, $rawHddData, 0, PREG_SPLIT_DELIM_CAPTURE);
        $hddData['storageDistribution'] = $hdd[0];
        $hddData['type'] = $hdd[1];
        $hddData['storage'] = self::getHddRealStorage($hddData['storageDistribution']);

        return $hddData;
    }

    public static function getHddRealStorage($distribution): int
    {
        $distribution = explode('x', $distribution);
        $hddQuantity = $distribution[0];
        $hddInfo = preg_split("/(TB|GB)/", $distribution[1], 0, PREG_SPLIT_DELIM_CAPTURE);
        $hddSize = $hddInfo[0];

        if ($hddInfo[1] === "TB") {
            $hddSize *= 1000;
        }

        return $hddQuantity * $hddSize;
    }

    private function getValidator(): ValidatorInterface
    {
        if (isset($this->validator)) {
            return $this->validator;
        }

        return Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
    }
}
