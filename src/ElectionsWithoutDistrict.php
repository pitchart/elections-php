<?php

namespace Elections\ElectionKata;

use function array_map;
use function array_search;
use function array_search as array_search1;
use function array_sum;
use function array_values;
use function count;
use function in_array;
use function in_array as in_array1;
use function number_format;

class ElectionsWithoutDistrict implements ElectionsInterface
{

    /** @var string[] */
    private array $candidates = [];

    /** @var string[] */
    private array $officialCandidates = [];

    /** @var int[] */
    private array $votesWithoutDistricts = [];
    /**
     * @var \string[][]
     */
    private array $list;

    /**
     * @param \string[][] $list
     */
    public function __construct(array $list)
    {

        $this->list = $list;
    }

    public function addCandidate(string $candidate): void
    {
        $this->officialCandidates[] = $candidate;
        $this->candidates[] = $candidate;
        $this->votesWithoutDistricts[] = 0;
    }

    public function voteFor(string $elector, string $candidate, string $electorDistrict): void
    {
        if (in_array($candidate, $this->candidates)) {
            $index = array_search($candidate, $this->candidates);
            $this->votesWithoutDistricts[$index] = $this->votesWithoutDistricts[$index] + 1;
        } else {
            $this->candidates[] = $candidate;
            $this->votesWithoutDistricts[] = 1;
        }
    }

    public function results(): array
    {
        $results = [];
        $nullVotes = 0;
        $blankVotes = 0;
        $nbValidVotes = 0;
        $nbVotes = array_sum($this->votesWithoutDistricts);
        for ($i = 0; $i < count($this->officialCandidates); $i++) {
            $index = array_search1($this->officialCandidates[$i], $this->candidates);
            $nbValidVotes += $this->votesWithoutDistricts[$index];
        }
        for ($i = 0; $i < count($this->votesWithoutDistricts); $i++) {
            $candidateResult = ($this->votesWithoutDistricts[$i] * 100) / $nbValidVotes;
            $candidate = $this->candidates[$i];
            if (in_array1($candidate, $this->officialCandidates)) {
                $results[$candidate] = number_format($candidateResult, 2, ',', '') . "%";
            } elseif (empty($this->candidates[$i])) {
                $blankVotes += $this->votesWithoutDistricts[$i];
            } else {
                $nullVotes += $this->votesWithoutDistricts[$i];
            }
        }

        $blankResult = ($blankVotes * 100) / $nbVotes;
        $results["Blank"] = number_format($blankResult, 2, ',', '') . "%";

        $nullResult = ($nullVotes * 100) / $nbVotes;
        $results["Null"] = number_format($nullResult, 2, ',', '') . "%";

        $nbElectors = array_sum(array_map(fn(array $districtList) => count($districtList), array_values($this->list)));
        $abstentionResult = 100 - ($nbVotes * 100 / $nbElectors);
        $results["Abstention"] = number_format($abstentionResult, 2, ',', '') . "%";
        return $results;
    }

}