<?php

namespace App\DTO;

use App\Helper\ServerHelper;
use Symfony\Component\Validator\Constraints as Assert;

class ServerFiltersDTO
{
    public function __construct(
        #[Assert\Collection(
            fields: [
                'min' => new Assert\Type('numeric'),
                'max' => new Assert\Type('numeric')
            ]
        )] public ?array $storage = ['min' => 0, 'max' => 7200],
        #[Assert\Type(type: 'array')] public ?array $ramSize = [],
        #[Assert\Type(type: 'array')] public ?array $ramType = [],
        #[Assert\Choice(
            options: ServerHelper::HDD_TYPES,
            multiple: true
        )] public ?array $hddType = [],
        #[Assert\Type(type: 'array')] public ?array $location = [],
        #[Assert\Collection(
            fields: [
                'min' => new Assert\Type('numeric'),
                'max' => new Assert\Type('numeric')
            ]
        )] public ?array $price = ['min' => 0, 'max' => 9999.99],
    ) {
    }
}
