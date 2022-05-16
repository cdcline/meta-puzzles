<?php

class Utils {
   public static function getLengthOfNum($i): int {
      return ((int)log10($i)) + 1;
   }

   public static function getLastDigitOfNum($i): int {
      return $i % 10;
   }

   public static function getPowMultiplier($i): int {
      return ((int)pow(10, $i) - 1) / (10 - 1);
   }

   public static function throwToBig($A, $B) {
      $diff = $B - $A;
      if (self::getLengthOfNum($diff) > 5) {
         throw new Exception('Gonna take too long');
      }
   }
}

function getUniformIntegerCountInIntervalByCalc(int $A, int $B): int {
   // Handle if it's just a single number
   if ($A === $B) {
      // If we start at 10 and end at 10, we get 0.
      if ($A === 10) {
         return 0;
      }
      // If it's < 10 it's uniform.
      if ($A < 10) {
         return 1;
      }
      // Otherwise calculate what value we expect it to be and test (lol)
      $aL = Utils::getLengthOfNum($A);
      $aLastDigit = Utils::getLastDigitOfNum($A);
      $d = Utils::getPowMultiplier($aL);
      $calVal = $d * $aLastDigit;
      if ($A === $calVal) {
         return 1;
      }
      return 0;
   }

   // Handle the B <= 10 case (also will make A <= 10)
   if ($B <= 10) {
      if ($B < 9) {
         return $B - $A + 1;
      }
      return 10 - $A;
   }

   // Gotta do more calculations
   // Get the length of A & B
   $aL = Utils::getLengthOfNum($A);
   $bL = Utils::getLengthOfNum($B);

   // Handle same length (B > 10)
   if ($aL === $bL) {
      $count = 0;
      $powMuliplier = Utils::getPowMultiplier($aL);
      // Calc all possible numbers in the range & compare
      for ($i = 1; $i < 10; $i++) {
         $iNum = $i * $powMuliplier;
         if ($iNum >= $A && $iNum <= $B) {
            $count++;
         }
      }
      return $count;
   }

   // Handle full lengths
   $fullPows = $bL - $aL - 1;
   $fullCounts = $fullPows * 9;

   // Handle A and up.
   $aPowMuliplier = Utils::getPowMultiplier($aL);
   $aCount = 0;
   // Calc all possible numbers in the range & compare
   for ($i = 1; $i < 10; $i++) {
      $iNum = $i * $aPowMuliplier;
      if ($iNum >= $A) {
         $aCount++;
      }
   }

   // Handle B and down
   $bPowMuliplier = Utils::getPowMultiplier($bL);
   $bCount = 0;
   // Calc all possible numbers in the range & compare
   for ($i = 1; $i < 10; $i++) {
      $iNum = $i * $bPowMuliplier;
      if ($iNum <= $B) {
         $bCount++;
      }
   }

   return $aCount + $fullCounts + $bCount;
}

function getUniformIntegerCountInIntervalRegex(int $A, int $B): int {
   Utils::throwToBig($A, $B);
   $count = 0;
   for ($i=$A; $i<=$B; $i++) {
      $digit = Utils::getLastDigitOfNum($i);
      $regex = "/^[{$digit}]+$/";
      if (preg_match($regex, $i)) {
         $count++;
      }
   }
   return $count;
}

function getUniformIntegerCountInIntervalLogs(int $A, int $B): int {
   Utils::throwToBig($A, $B);
   $count = 0;
   for ($i=$A; $i<=$B; $i++) {
      // Get the length of N
      $l = ((int)log10($i)) + 1;
      // Form the number M of the type
      // K*111... where K is the
      // rightmost digit of N
      $m = ((int)pow(10, $l) - 1) / (10 - 1);
      $m *= $i % 10;
      if ($m === $i) {
         $count++;
      }
   }
   return $count;
}

function getUniformIntegerCountInIntervalStrSplit(int $A, int $B): int {
   Utils::throwToBig($A, $B);
   $count = 0;
   for ($i=$A; $i<=$B; $i++) {
      $str = str_split((string)$i);
      $uniform = true;
      foreach($str as $char) {
         if ($char != $str[0]) {
            $uniform = false;
            break;
         }
      }
      if ($uniform) {
         $count++;
      }
   }
   return $count;
}

$testCases = [
   /* Matching Cases */
   ['data' => ['A' => 1, 'B' => 1],
   'Val' => 1
   ],
   ['data' => ['A' => 9, 'B' => 9],
   'Val' => 1
   ],
   ['data' => ['A' => 10, 'B' => 10],
   'Val' => 0
   ],
   ['data' => ['A' => 11, 'B' => 11],
   'Val' => 1
   ],
   ['data' => ['A' => 777, 'B' => 777],
   'Val' => 1
   ],
   ['data' => ['A' => 999999999999, 'B' => 999999999999],
   'Val' => 1
   ],
   /* Matching Cases End  */

   /* <= B 10 cases */
   ['data' => ['A' => 1, 'B' => 9],
    'Val' => 9
   ],
   ['data' => ['A' => 1, 'B' => 10],
    'Val' => 9
   ],
   ['data' => ['A' => 9, 'B' => 9],
    'Val' => 1
   ],
   ['data' => ['A' => 3, 'B' => 10],
    'Val' => 7
   ],
   ['data' => ['A' => 3, 'B' => 4],
    'Val' => 2
   ],
   ['data' => ['A' => 3, 'B' => 5],
    'Val' => 3
   ],
   ['data' => ['A' => 10, 'B' => 10],
    'Val' => 0
   ],
   /*  <= B 10 cases end */

   /* Same Length > 10*/
   ['data' => ['A' => 10, 'B' => 20],
    'Val' => 1
   ],

   ['data' => ['A' => 11, 'B' => 22],
    'Val' => 2
   ],
   ['data' => ['A' => 999999999997, 'B' => 999999999999],
    'Val' => 1
   ],
   ['data' => ['A' => 199999999997, 'B' => 999999999999],
    'Val' => 8
   ],
   ['data' => ['A' => 10, 'B' => 35],
    'Val' => 3
   ],
   /*   Same Length > 10 end */

   /* Full Counts */
   ['data' => ['A' => 11, 'B' => 999],
   'Val' => 18
   ],
   ['data' => ['A' => 111, 'B' => 99999],
   'Val' => 27
   ],
   /* Full Counts end */

   ['data' => ['A' => 75, 'B' => 300],
    'Val' => 5
   ],

   ['data' => ['A' => 10, 'B' => 20],
    'Val' => 1
   ],
   ['data' => ['A' => 999999999999, 'B' => 999999999999],
    'Val' => 1
   ],
   ['data' => ['A' => 10, 'B' => 222],
    'Val' => 11
   ],
];


$testFunc = function($tData): ?string {
   $errors = [];
   try {
      $testFunctions = ['StrSplit', 'Logs', 'Regex', 'ByCalc'];
      foreach ($testFunctions as $funcStr) {
         $func = "getUniformIntegerCountInInterval{$funcStr}";
         try {
            if ($error = $tData->checkTestResult($func($tData->A, $tData->B))) {
               $errors[$funcStr] = $error;
            }
         } catch (Exception $e) {
            $errors[$funcStr] = $e->getMessage();
         }
      }
      if ($errors) {
         $total = count($testFunctions);
         $failed = count($errors);
         $passed = $total - $failed;
         $errorStr = "Passed {$passed} / {$total} test functions\n";
         $error = $errorStr . print_r($errors, true);
      }
   } catch (Exception $e) {
      $error = $e->getMessage() ?: 'unknown error';
   }

   $error = $error ? $tData->getDataString() . "\n" . $error : null;
   return $error;
};

require_once __DIR__ . '/vendor/autoload.php';
Utils\TestRunner::runTestsfromArray($testCases, $testFunc);