<?php
class Seat {
   private $position;
   private $isSeated;
   private $nextSeat;
   private $prevSeat;
   private $startSeat= false;
   private $numSpaces;

   public function __construct($positon, $isSeated, $numSpaces) {
      $this->position = $positon;
      $this->isSeated = $isSeated;
      $this->numSpaces = $numSpaces;
   }

   public function setNextSeat(&$nextSeat) {
      $this->nextSeat = $nextSeat;
   }

   public function setPrevSeat(&$prevSeat) {
      $this->prevSeat = $prevSeat;
   }

   public function getPosition() {
      return $this->position;
   }

   public function getNextSeat() {
      return $this->nextSeat;
   }

   public function getPrevSeat() {
      return $this->prevSeat;
   }

   public function isSeated() {
      return $this->isSeated;
   }

   public function setStart() {
      $this->startSeat = true;
   }

   public function isStart() {
      return $this->startSeat;
   }

   private function getCloseSeat(bool $goingUp) {
      return $goingUp ? $this->getNextSeat() : $this->getPrevSeat();
   }

   public function getFreeSeat(int $numSpaces, bool $goingUp = true) {
      $spaceToNext = 0;
      $seat = $this->getCloseSeat($goingUp);
      if ($seat->isSeated()) {
         return false;
      }

      while ($spaceToNext++ < $numSpaces) {
         $seat = $seat->getCloseSeat($goingUp);
         if ($seat->isSeated()) {
            return false;
         }
      }
      return $seat;
   }

   public function getNextSpaceToSit(): ?self {
      $spaceToNext = 0;
      $seat = $this;
      while ($seat = $seat->getNextSeat()) {
         if ($seat->isStart()) {
            return null;
         }
         if ($seat->isSeated()) {
            $spaceToNext = 0;
            continue;
         }
         if (++$spaceToNext > $this->numSpaces) {
            return $seat;
         }
      }
   }

   public function hasEmptySeats(int $numSpaces, bool $goingUp = true) {
      return $this->getFreeSeat(($numSpaces - 1), $goingUp);
   }

   public function hasEnoughSpace(int $numSpaces) {
      return $this->hasEmptySeats($numSpaces, true);
   }

   public function sitDown() {
      $this->isSeated = true;
   }

   public function print() {
      echo "P:{$this->getPosition()}, Se:{$this->isSeated()}, St:{$this->isStart()}\n";
   }
}

class TableSeating {
   private $iSeats;
   private $seatSpace;

   public function __construct($numSeats, $seatSpace, $alreadySat) {
      $this->alreadySat = $alreadySat;
      $this->seatSpace = $seatSpace;
      $i = 0;
      $aSat = array_flip($alreadySat);

      while (++$i <= $numSeats) {
         $isSat = isset($aSat[$i]);
         $this->iSeats[$i] = new Seat($i, $isSat, $this->seatSpace);
      }

      foreach ($this->iSeats as $i => $seat) {
         $nextSeat = $i + 1;
         $prevSeat = $i - 1;
         if ($prevSeat < 1) {
            $prevSeat = $numSeats;
         }
         if ($nextSeat > $numSeats) {
            $nextSeat = 1;
         }
         $seat->setNextSeat($this->iSeats[$nextSeat]);
         $seat->setPrevSeat($this->iSeats[$prevSeat]);
      }
   }

   private function startSittingAroundSeat($seatNumber) {
      $startSeat = $this->iSeats[$seatNumber];
      $startSeat->setStart();
      $iSeat = $startSeat;
      while ($iSeat = $iSeat->getNextSpaceToSit()) {
         if ($iSeat->hasEnoughSpace($this->seatSpace)) {
            $iSeat->sitDown();
         }
      }
   }

   public function getMostNewSeats(): int {
      $minSpaceToSeatMore = count($this->iSeats) / 2;
      if ($this->seatSpace > $minSpaceToSeatMore) {
         return 0;
      }

      $this->startSittingAroundSeat($this->alreadySat[0]);

      $totalSat = 0;
      foreach ($this->iSeats as $seat) {
         if ($seat->isSeated()) {
            $totalSat++;
         }
      }

      return $totalSat - count($this->alreadySat);
   }

   public function printSeats() {
      echo "Seats ({$this->seatSpace}):\n";
      foreach ($this->iSeats as $i) {
         echo "\t{$i->getPosition()}: {$i->isSeated()}\n";
      }
   }
}

function getMaxAdditionalDinersCount(int $N, int $K, int $M, array $S): int {
   $tableSeating = new TableSeating($N, $K, $S);
   $count = $tableSeating->getMostNewSeats();
   return $count;
}

$testCases = [
['data' => ['N' => 10, 'K' => 1, 'M' => 2, 'S' => [2, 6]],
 'Val' => 3
],
['data' => ['N' => 15, 'K' => 2, 'M' => 3, 'S' => [11, 6, 14]],
 'Val' => 1
]];

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