<?php

namespace App\Enums;

enum MeasurementUnits: string
{
    case PIECE = "piece";
    case KG = "kg";
    case GR = "gr";

    public function name(): string
    {
        return match($this) {
            MeasurementUnits::PIECE => 'шт',
            MeasurementUnits::KG => 'кг',
            MeasurementUnits::GR => 'гр',
        };
    }
}
