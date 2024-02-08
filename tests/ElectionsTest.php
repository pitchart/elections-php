<?php

namespace Tests\Elections\ElectionKata;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertTrue;

class ElectionsTest extends TestCase
{
    private $list = [
        "District 1" => ["Bob", "Anna", "Jess", "July"],
        "District 2" => ["Jerry", "Simon"],
        "District 3" => ["Johnny", "Matt", "Carole"],
    ];

    public function test_elections_without_district(): void
    {
        assertTrue(true);
    }

    public function test_elections_with_district(): void
    {
        assertTrue(true);
    }

}
