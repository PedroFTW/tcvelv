<?php

namespace App\DTO;

use App\Helper\ServerHelper;
use Symfony\Component\Validator\Constraints as Assert;

class ServerFiltersDTO
{
    public const HDD_STORAGE_MIN_MAX_DEFAULTS = [
        'min' => 0,
        'max' => 7200
    ];

    public const PRICE_STORAGE_MIN_MAX_DEFAULTS = [
        'min' => 0,
        'max' => 9999.99
    ];

    public function __construct(
        #[Assert\Collection(
            fields: [
                'min' => new Assert\Optional([new Assert\Type('numeric')]),
                'max' => new Assert\Optional([new Assert\Type('numeric')])
            ]
        )] public ?array $storage = self::PRICE_STORAGE_MIN_MAX_DEFAULTS,
        #[Assert\Type(type: 'array')] public ?array $ramSize = [],
        #[Assert\Type(type: 'array')] public ?array $ramType = [],
        #[Assert\Choice(
            options: ServerHelper::HDD_TYPES,
            multiple: true
        )] public ?array $hddType = [],
        #[Assert\Type(type: 'array')] public ?array $location = [],
        #[Assert\Collection(
            fields: [
                'min' => new Assert\Optional([new Assert\Type('numeric')]),
                'max' => new Assert\Optional([new Assert\Type('numeric')])
            ]
        )] public ?array $price = self::PRICE_STORAGE_MIN_MAX_DEFAULTS,
    ) {
    }
}
