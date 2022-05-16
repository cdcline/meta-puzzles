<?php

class TableSeating {
   private $numSeats;
   private $alreadySat;
   private $seatSpace;

   public function __construct($numSeats, $seatSpace, $alreadySat) {
      $this->numSeats = $numSeats;
      sort($alreadySat, SORT_NUMERIC);
      $this->alreadySat = $alreadySat;

      $this->seatSpace = $seatSpace;
   }

   private function getMostPossibleSeats(): int {
         return $this->numSeats / ($this->seatSpace + 1);
   }

   private function getMostSeatsInSpace(int $spaceToFill): int {
      return ($spaceToFill - $this->seatSpace) / ($this->seatSpace + 1);
   }

   public function getMostNewSeats(): int {
      if ($this->seatSpace >= ($this->numSeats / 2)) {
         return 0;
      }
      if (!$this->alreadySat) {
         return $this->getMostPossibleSeats();
      } else {
         $totalNewSeats = 0;
         for ($i = 0; $i < count($this->alreadySat); $i++) {
            $iNextSeat = $i + 1;
            $distanceToFill = 0;
            if(isset($this->alreadySat[$iNextSeat])) {
               $distanceToFill = $this->alreadySat[$iNextSeat] - $this->alreadySat[$i] - 1;
            } else {
               $distanceToFill = ($this->numSeats - $this->alreadySat[$i]) + $this->alreadySat[0] - 1;
            }
            $totalNewSeats += $this->getMostSeatsInSpace($distanceToFill);
         }
      }
      return $totalNewSeats;
   }
}

function getMaxAdditionalDinersCount(int $N, int $K, int $M, array $S): int {
   $tableSeating = new TableSeating($N, $K, $S);
   $count = $tableSeating->getMostNewSeats();
   return $count;
}

$testCases = [
['data' => ['N' => 10, 'K' => 1, 'M' => 0, 'S' => []],
 'Val' => 5
],
['data' => ['N' => 10, 'K' => 4, 'M' => 0, 'S' => []],
 'Val' => 2
],
['data' => ['N' => 10, 'K' => 4, 'M' => 0, 'S' => [4]],
 'Val' => 1
],
['data' => ['N' => 10, 'K' => 5, 'M' => 0, 'S' => []],
 'Val' => 0
],
['data' => ['N' => 10, 'K' => 1, 'M' => 1, 'S' => [1]],
 'Val' => 4
],
['data' => ['N' => 10, 'K' => 1, 'M' => 2, 'S' => [1, 4]],
 'Val' => 2
],
['data' => ['N' => 10, 'K' => 1, 'M' => 2, 'S' => [2, 6]],
 'Val' => 3
],
['data' => ['N' => 15, 'K' => 2, 'M' => 3, 'S' => [11, 6, 14]],
 'Val' => 1
]
];

$testFunc = function($tData): ?string {
   try {
      $maxCount = getMaxAdditionalDinersCount($tData->N, $tData->K, $tData->M, $tData->S);
      $error = $tData->checkTestResult($maxCount);
   } catch (Exception $e) {
      $error = $e->getMessage();
   }

   $error = isset($error) ? $error . "\n" . $tData->getDataString() : null;
   return $error;
};

require_once __DIR__ . '/vendor/autoload.php';
Utils\TestRunner::runTestsfromArray($testCases, $testFunc);