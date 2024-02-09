<?php

namespace Elections\ElectionKata;

class ElectionsWithoutDistrict implements ElectionsInterface
{

    /**
     * @param \string[][] $list
     */
    public function __construct(array $list)
    {
    }

    public function addCandidate(string $candidate): void
    {
        // TODO: Implement addCandidate() method.
    }

    public function voteFor(string $elector, string $candidate, string $electorDistrict): void
    {
        // TODO: Implement voteFor() method.
    }

    public function results(): array
    {
        return [];
    }
}