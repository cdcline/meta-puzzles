<?php

function getUniformIntegerCountInIntervalRegex(int $A, int $B): int {
   $count = 0;
   for ($i=$A; $i<=$B; $i++) {
      $digit = $i % 10;
      $regex = "/^[{$digit}]+$/";
      if (preg_match($regex, $i)) {
         $count++;
      }
   }
   return $count;
}

function getUniformIntegerCountInIntervalLogs(int $A, int $B): int {
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
   ['data' => ['A' => 75, 'B' => 300],
    'Val' => 5
   ],
   ['data' => ['A' => 1, 'B' => 9],
    'Val' => 9
   ],
   ['data' => ['A' => 999999999999, 'B' => 999999999999],
    'Val' => 1
   ],
];


$testFunc = function($tData): ?string {
   $errors = null;
   try {
      if ($error = $tData->checkTestResult(
                     getUniformIntegerCountInIntervalStrSplit($tData->A, $tData->B))) {
         $errors[] = $error;
      }
      if ($error = $tData->checkTestResult(
                     getUniformIntegerCountInIntervalLogs($tData->A, $tData->B))) {
         $errors[] = $error;
      }
      if ($error = $tData->checkTestResult(
                     getUniformIntegerCountInIntervalRegex($tData->A, $tData->B))) {
         $errors[] = $error;
      }
      $error = $errors ? print_r($errors, true) : null;
   } catch (Exception $e) {
      $error = $e->getMessage() ?: 'unknown error';
   }

   $error = $error ? $tData->getDataString() . "\n" . $error : null;
   return $error;
};

require_once __DIR__ . '/vendor/autoload.php';
Utils\TestRunner::runTestsfromArray($testCases, $testFunc);