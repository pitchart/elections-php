<?php

namespace Elections\ElectionKata;

class Elections implements ElectionsInterface
{
    /** @var string[] */
    private array $candidates = [];
    /** @var string[] */
    private array $officialCandidates = [];
    /** @var int[] */
    private array $votesWithoutDistricts = [];
    /** @var array<string, int[]> */
    private array $votesWithDistricts = [];
    /** @var array<string, string[]> */
    private array $list;
    private bool $withDistrict;

    /**
     * @param array<string, string[]> $list
     * @param bool $withDistrict
     */
    public function __construct(array $list, bool $withDistrict)
    {
        $this->list = $list;
        $this->withDistrict = $withDistrict;

        $this->votesWithDistricts["District 1"] = [];
        $this->votesWithDistricts["District 2"] = [];
        $this->votesWithDistricts["District 3"] = [];
    }

    public function addCandidate(string $candidate): void
    {
        if ($this->withDistrict) {
            $this->addCandidateWithDistrict($candidate);
        } else {
            $this->addCandidateWithoutDistrict($candidate);
        }
    }

    public function voteFor(string $elector, string $candidate, string $electorDistrict): void
    {
        if ($this->withDistrict) {
            $this->voteForWithDistrict($electorDistrict, $candidate);
        } else {
            $this->voteForWithoutDistrict($candidate);
        }
    }

    public function results(): array
    {
        if ($this->withDistrict) {
            return $this->resultsWithDistrict();
        }
        return $this->resultsWithoutDistrict();
    }

    /**
     * @param string $candidate
     * @return void
     */
    public function voteForWithoutDistrict(string $candidate): void
    {
        if (in_array($candidate, $this->candidates)) {
            $index = array_search($candidate, $this->candidates);
            $this->votesWithoutDistricts[$index] = $this->votesWithoutDistricts[$index] + 1;
        } else {
            $this->candidates[] = $candidate;
            $this->votesWithoutDistricts[] = 1;
        }
    }

    /**
     * @param string $electorDistrict
     * @param string $candidate
     * @return void
     */
    public function voteForWithDistrict(string $electorDistrict, string $candidate): void
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

    /**
     * @return array
     */
    public function resultsWithoutDistrict(): array
    {
        $results = [];
        $nullVotes = 0;
        $blankVotes = 0;
        $nbValidVotes = 0;
        $nbVotes = array_sum($this->votesWithoutDistricts);
        for ($i = 0; $i < count($this->officialCandidates); $i++) {
            $index = array_search($this->officialCandidates[$i], $this->candidates);
            $nbValidVotes += $this->votesWithoutDistricts[$index];
        }
        for ($i = 0; $i < count($this->votesWithoutDistricts); $i++) {
            $candidateResult = ($this->votesWithoutDistricts[$i] * 100) / $nbValidVotes;
            $candidate = $this->candidates[$i];
            if (in_array($candidate, $this->officialCandidates)) {
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

    /**
     * @return array
     */
    public function resultsWithDistrict(): array
    {
        $results = [];
        $nbVotes = 0;
        $nullVotes = 0;
        $blankVotes = 0;
        $nbValidVotes = 0;
        foreach ($this->votesWithDistricts as $districtVotes) {
            $nbVotes += array_sum($districtVotes);
        }
        for ($i = 0; $i < count($this->officialCandidates); $i++) {
            $index = array_search($this->officialCandidates[$i], $this->candidates);
            foreach ($this->votesWithDistricts as $districtVotes) {
                $nbValidVotes += $districtVotes[$index];
            }
        }
        $officialCandidatesResult = [];
        for ($i = 0; $i < count($this->officialCandidates); $i++) {
            $officialCandidatesResult[$this->candidates[$i]] = 0;
        }
        foreach ($this->votesWithDistricts as $districtVotes) {
            $districtResult = [];
            for ($i = 0; $i < count($districtVotes); $i++) {
                $candidateResult = 0;
                if ($nbValidVotes != 0) {
                    $candidateResult = ($districtVotes[$i] * 100) / $nbValidVotes;
                }
                $candidate = $this->candidates[$i];
                if (in_array($candidate, $this->officialCandidates)) {
                    $districtResult[] = $candidateResult;
                } elseif (empty($this->candidates[$i])) {
                    $blankVotes += $districtVotes[$i];
                } else {
                    $nullVotes += $districtVotes[$i];
                }
            }
            $districtWinnerIndex = 0;
            for ($i = 1; $i < count($districtResult); $i++) {
                if ($districtResult[$districtWinnerIndex] < $districtResult[$i])
                    $districtWinnerIndex = $i;
            }
            $officialCandidatesResult[$this->candidates[$districtWinnerIndex]] = $officialCandidatesResult[$this->candidates[$districtWinnerIndex]] + 1;
        }

        for ($i = 0; $i < count($officialCandidatesResult); $i++) {
            $ratioCandidate = ($officialCandidatesResult[$this->candidates[$i]]) / count($officialCandidatesResult) * 100;
            $results[$this->candidates[$i]] = number_format($ratioCandidate, 2, ',', '') . "%";
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

    /**
     * @param string $candidate
     * @return void
     */
    public function addCandidateWithDistrict(string $candidate): void
    {
        $this->officialCandidates[] = $candidate;
        $this->candidates[] = $candidate;
        $this->votesWithDistricts["District 1"][] = 0;
        $this->votesWithDistricts["District 2"][] = 0;
        $this->votesWithDistricts["District 3"][] = 0;
    }

    /**
     * @param string $candidate
     * @return void
     */
    public function addCandidateWithoutDistrict(string $candidate): void
    {
        $this->officialCandidates[] = $candidate;
        $this->candidates[] = $candidate;
        $this->votesWithoutDistricts[] = 0;
    }
}