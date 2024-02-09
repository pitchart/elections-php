<?php

namespace Elections\ElectionKata;

use function array_key_exists;
use function array_map;
use function array_search;
use function array_search as array_search1;
use function array_sum;
use function array_values;
use function count;
use function count as count1;
use function in_array;
use function in_array as in_array1;
use function number_format;

class ElectionsWithDistrict implements ElectionsInterface
{
    /** @var string[] */
    private array $candidates = [];
    /** @var string[] */
    private array $officialCandidates = [];
    /** @var array<string, int[]> */
    private array $votesWithDistricts = [];
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
        $this->votesWithDistricts["District 1"][] = 0;
        $this->votesWithDistricts["District 2"][] = 0;
        $this->votesWithDistricts["District 3"][] = 0;
    }

    public function voteFor(string $elector, string $candidate, string $electorDistrict): void
    {
        if (array_key_exists($electorDistrict, $this->votesWithDistricts)) {
            $districtVotes = $this->votesWithDistricts[$electorDistrict];
            if (in_array($candidate, $this->candidates)) {
                $index = array_search($candidate, $this->candidates);
                $districtVotes[$index] = $districtVotes[$index] + 1;
            } else {
                $this->candidates[] = $candidate;
                $districtVotes[] = 0;
                foreach ($this->votesWithDistricts as $district => $votes) {
                    $this->votesWithDistricts[$district][] = 0;
                }
                $districtVotes[count($this->candidates) - 1] = $districtVotes[count($this->candidates) - 1] + 1;
            }
            $this->votesWithDistricts[$electorDistrict] = $districtVotes;
        }
    }

    public function results(): array
    {
        $results = [];
        $nbVotes = 0;
        $nullVotes = 0;
        $blankVotes = 0;
        $nbValidVotes = 0;
        foreach ($this->votesWithDistricts as $districtVotes) {
            $nbVotes += array_sum($districtVotes);
        }
        for ($i = 0; $i < count1($this->officialCandidates); $i++) {
            $index = array_search1($this->officialCandidates[$i], $this->candidates);
            foreach ($this->votesWithDistricts as $districtVotes) {
                $nbValidVotes += $districtVotes[$index];
            }
        }
        $officialCandidatesResult = [];
        for ($i = 0; $i < count1($this->officialCandidates); $i++) {
            $officialCandidatesResult[$this->candidates[$i]] = 0;
        }
        foreach ($this->votesWithDistricts as $districtVotes) {
            $districtResult = [];
            for ($i = 0; $i < count1($districtVotes); $i++) {
                $candidateResult = 0;
                if ($nbValidVotes != 0) {
                    $candidateResult = ($districtVotes[$i] * 100) / $nbValidVotes;
                }
                $candidate = $this->candidates[$i];
                if (in_array1($candidate, $this->officialCandidates)) {
                    $districtResult[] = $candidateResult;
                } elseif (empty($this->candidates[$i])) {
                    $blankVotes += $districtVotes[$i];
                } else {
                    $nullVotes += $districtVotes[$i];
                }
            }
            $districtWinnerIndex = 0;
            for ($i = 1; $i < count1($districtResult); $i++) {
                if ($districtResult[$districtWinnerIndex] < $districtResult[$i])
                    $districtWinnerIndex = $i;
            }
            $officialCandidatesResult[$this->candidates[$districtWinnerIndex]] = $officialCandidatesResult[$this->candidates[$districtWinnerIndex]] + 1;
        }

        for ($i = 0; $i < count1($officialCandidatesResult); $i++) {
            $ratioCandidate = ($officialCandidatesResult[$this->candidates[$i]]) / count1($officialCandidatesResult) * 100;
            $results[$this->candidates[$i]] = number_format($ratioCandidate, 2, ',', '') . "%";
        }

        $blankResult = ($blankVotes * 100) / $nbVotes;
        $results["Blank"] = number_format($blankResult, 2, ',', '') . "%";

        $nullResult = ($nullVotes * 100) / $nbVotes;
        $results["Null"] = number_format($nullResult, 2, ',', '') . "%";

        $nbElectors = array_sum(array_map(fn(array $districtList) => count1($districtList), array_values($this->list)));
        $abstentionResult = 100 - ($nbVotes * 100 / $nbElectors);
        $results["Abstention"] = number_format($abstentionResult, 2, ',', '') . "%";
        return $results;
    }

}