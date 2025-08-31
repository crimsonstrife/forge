<?php
namespace App\Enums;

enum MetricUnit: string
{
    case Number='number';
    case Percent='percent';
    case Currency='currency';
    case Duration='duration';
}
