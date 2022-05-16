<?php

namespace Utils;

use Exception;

class TestRunner {
   private $testCases;
   private $testFunction;

   public static function runTestsfromArray($testCaseValues, $testFunction): void {
      $testCases = [];
      foreach ($testCaseValues as $tValues) {
         $testCases[] = TestCase::fromArray($tValues);
      }
      (new self($testCases, $testFunction))->runAllTests();
   }

   private function __construct($testCases, $testFunction) {
      $this->testCases = $testCases;
      $this->testFunction = $testFunction;
   }

   private function runAllTests() {
      $failures = [];
      $tFunction = $this->testFunction;
      $passed = 0;
      foreach ($this->testCases as $i => $tCase) {
         try {
            if ($errors = $tFunction($tCase)) {
               $iStr = "testCase{$i}";
               $failures[$iStr] = $errors;
            } else {
               $passed++;
            }
         } catch (Exception $e) {
            $failures[$iStr] = $e->getMessage();
         }
      }
      $failures ? $this->printFail($passed, $failures) : $this->printPass();
   }

   private function printPass(): void {
      echo "=== PASS ===\n";
   }

   private function printFail(int $passed, array $failures): void {
      $total = count($failures) + $passed;
      $passStr = "({$passed}/{$total} passed)";
      echo "=== FAIL {$passStr} ===\n";
      print_r($failures);
   }
}
