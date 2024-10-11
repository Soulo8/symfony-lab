<?php

declare(strict_types=1);

namespace App\Enum;

enum Image: string
{
    case Bird = '/src/DataFixtures/images/bird.jpg';
    case Car = '/src/DataFixtures/images/car.jpg';
    case Cat = '/src/DataFixtures/images/cat.jpg';
    case Landscape = '/src/DataFixtures/images/landscape.jpg';
    case Ship = '/src/DataFixtures/images/ship.jpg';
}
