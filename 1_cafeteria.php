<?php

class Seat {
   private $position;
   private $isSeated;
   private $nextSeat;
   private $prevSeat;

   public function __construct($posiiton, $isSeated) {
      $this->position = $posiiton;
      $this->isSeated = $isSeated;
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

   public function hasEmptySeats(int $numSpaces, bool $goingUp = true) {
      return $this->getFreeSeat(($numSpaces - 1), $goingUp);
   }

   public function hasEnoughSpace(int $numSpaces) {
      return $this->hasEmptySeats($numSpaces, true) && $this->hasEmptySeats($numSpaces, false);
   }

   public function sitDown() {
      $this->isSeated = true;
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
         $this->iSeats[$i] = new Seat($i, $isSat);
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

   private function sitAroundSeat($seatNumber) {
      $startSeat = $this->iSeats[$seatNumber];
      $iSeat = $startSeat;
      while ($iSeat = $iSeat->getFreeSeat($this->seatSpace)) {
         if ($iSeat->hasEnoughSpace($this->seatSpace)) {
            $iSeat->sitDown();
         } else {
            break;
         }
      }
      $iSeat = $startSeat;
      while ($iSeat = $iSeat->getFreeSeat($this->seatSpace, false)) {
         if ($iSeat->hasEnoughSpace($this->seatSpace, false)) {
            $iSeat->sitDown();
         } else {
            break;
         }
      }
   }

   public function getMostNewSeats(): int {
      foreach ($this->alreadySat as $iStartSeat) {
         $this->sitAroundSeat($iStartSeat);
      }

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