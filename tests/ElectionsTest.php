<?php

namespace Tests\Elections\ElectionKata;

use ApprovalTests\Approvals;
use Elections\ElectionKata\ElectionsWithDistrict;
use Elections\ElectionKata\ElectionsWithoutDistrict;
use PHPUnit\Framework\TestCase;

class ElectionsTest extends TestCase
{
    const JOHNNY = 'Johnny';
    const BOB = 'Bob';
    const JERRY = 'Jerry';
    const ANNA = 'Anna';
    const MICHEL = 'Michel';
    const JESS = "Jess";
    const JULY = "July";
    const SIMON = "Simon";
    const MATT = "Matt";
    const CAROLE = "Carole";
    const DISTRICT_1 = 'District 1';
    const DISTRICT_2 = 'District 2';
    const DISTRICT_3 = "District 3";
    const DONALD = "Donald";
    const JOE = "Joe";
    private $list = [
        self::DISTRICT_1 => [self::BOB, self::ANNA, self::JESS, self::JULY],
        self::DISTRICT_2 => [self::JERRY, self::SIMON],
        self::DISTRICT_3 => [self::JOHNNY, self::MATT, self::CAROLE],
    ];

    public function test_elections_without_district(): void
    {
        $election = new ElectionsWithoutDistrict($this->list);
        $election->addCandidate(self::MICHEL);
        $election->addCandidate(self::JERRY);
        $election->addCandidate(self::JOHNNY);
        
        $election->voteFor(self::BOB, self::JERRY, self::DISTRICT_1);
        $election->voteFor(self::JERRY, self::JERRY, self::DISTRICT_2);
        $election->voteFor(self::ANNA, self::JOHNNY, self::DISTRICT_1);
        $election->voteFor(self::JOHNNY, self::JOHNNY, self::DISTRICT_3);
        $election->voteFor(self::MATT, self::DONALD, self::DISTRICT_3);
        $election->voteFor(self::JESS, self::JOE, self::DISTRICT_1);
        $election->voteFor(self::SIMON, "", self::DISTRICT_2);
        $election->voteFor(self::CAROLE, "", self::DISTRICT_3);

        Approvals::verifyList($election->results());
    }

    public function test_elections_with_district(): void
    {
        $election = new ElectionsWithDistrict($this->list);
        $election->addCandidate(self::MICHEL);
        $election->addCandidate(self::JERRY);
        $election->addCandidate(self::JOHNNY);

        $election->voteFor(self::BOB, self::JERRY, self::DISTRICT_1);
        $election->voteFor(self::JERRY, self::JERRY, self::DISTRICT_2);
        $election->voteFor(self::ANNA, self::JOHNNY, self::DISTRICT_1);
        $election->voteFor(self::JOHNNY, self::JOHNNY, self::DISTRICT_3);
        $election->voteFor(self::MATT, self::DONALD, self::DISTRICT_3);
        $election->voteFor(self::JESS, self::JOE, self::DISTRICT_1);
        $election->voteFor(self::JULY, self::JERRY, self::DISTRICT_1);
        $election->voteFor(self::SIMON, "", self::DISTRICT_2);
        $election->voteFor(self::CAROLE, "", self::DISTRICT_3);

        Approvals::verifyList($election->results());    }

}
