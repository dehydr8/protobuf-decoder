<?php 

class DecoderTest extends PHPUnit_Framework_TestCase {

  public function testIsThereAnySyntaxError() {
    $var = new dehydr8\Protobuf\Decoder("");
    $this->assertTrue(is_object($var));
    unset($var);
  }
}