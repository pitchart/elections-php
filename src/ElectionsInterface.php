<?php

namespace Elections\ElectionKata;

interface ElectionsInterface
{
    public function addCandidate(string $candidate): void;

    public function voteFor(string $elector, string $candidate, string $electorDistrict): void;

    public function results(): array;
}