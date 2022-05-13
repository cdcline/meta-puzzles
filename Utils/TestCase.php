<?php

namespace Utils;

class TestCase {
   private $data;
   private $expectedResult;

   public static function fromArray($vArray) {
      return new self($vArray['data'], $vArray['Val']);
   }

   private function __construct(array $data, $expectedResult) {
      $this->data = $data;
      $this->expectedResult = $expectedResult;
   }

   public function __get($name) {
      return $this->data[$name];
   }

   public function checkTestResult($testResult): ?string {
      if ($this->matchesResult($testResult)) {
         return null;
      }

      return  "Invalid result, got: {$testResult}, expected: {$this->expectedResult}";
   }

   public function getDataString(): string {
      return print_r($this->data, true);
   }

   private function matchesResult($testResult): bool {
      return $this->expectedResult === $testResult;
   }
}
