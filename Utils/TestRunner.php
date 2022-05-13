<?php

namespace Utils;

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
      foreach ($this->testCases as $i => $tCase) {
         if ($errors = $tFunction($tCase)) {
            $iStr = "testCase{$i}";
            $failures[$iStr] = $errors;
         }
      }
      $failures ? $this->printFail($failures) : $this->printPass();
   }

   private function printPass(): void {
      echo "=== PASS ===\n";
   }

   private function printFail(array $failures): void {
      echo "=== FAIL ===\n";
      print_r($failures);
   }
}
