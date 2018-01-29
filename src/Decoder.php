<?php namespace dehydr8\Protobuf;

class Decoder {
  private $data;
  private $idx;

  public function __construct($data) {
    $this->data = $data;
    $this->idx = 0;
  }

  private function hasMoreContent() {
    return $this->idx < strlen($this->data);
  }

  private function unpack($format, $increment = 0) {
    $ret = unpack($format, substr($this->data, $this->idx));
    $this->idx += $increment;
    return $ret[1];
  }

  private function readByte() {
    $b = $this->unpack("C", 1);
    $this->log("readByte() => " . dechex($b));
    return $b;
  }

  /**
   * https://developers.google.com/protocol-buffers/docs/encoding
   *
   * @return void
   */
  private function readVarint() {
    $this->log("reading next varint");
    $value = 0;
    $shift = 0;
    do {
      $b = $this->readByte();
      $this->log("read byte = " . dechex($b));
      // strip the msb
      $s = $b & (0xFF >> 1);
      $shifted = bcmul($s, bcpow('2', $shift));
      $value = bcadd($value, $shifted);
      $shift += 7;
    } while ($b >> 7 & 1);

    $this->log("varint read = $value");

    return $value;
  }

  private function read64bit() {
    $this->log("reading 64bit");
    $length = 4;
    $value = substr($this->data, $this->idx, $length);
    $this->idx += $length;

    return bin2hex($value);
  }

  private function readString() {
    // intval for sane lengths
    $length = intval($this->readVarint());

    if ($length <= 0)
      return "";

    $this->log("reading string of length: $length");

    $value = substr($this->data, $this->idx, $length);
    $this->idx += $length;

    if (!ctype_print($value)) {
      try {
        $this->log("trying to parse as object: $value");
        $d = new Decoder($value);
        return $d->decode();
      } catch (\Exception $e) {

      }
    }

    $this->log("!!!! strval = " . strlen($value));

    return $value;
  }

  private function getFieldNumber($field) {
    return bcdiv($field, bcpow('2', 3));
  }

  private function getWireType($field) {
    return intval($field) & 3;
  }

  private function log($message) {
    //echo "[-] $message\r\n";
  }

  public function decode() {
    $fields = array();
    $finished = false;

    while (!$finished && $this->hasMoreContent()) {
      $enc = $this->readVarint();
      $field = $this->getFieldNumber($enc);
      $type = $this->getWireType($enc);
  
      $this->log("vint($enc) -> field($field) - wt($type)");
  
      $value = null;
  
      switch ($type) {
        case 0: $value = $this->readVarint(); break;
        case 1: $value = $this->read64bit(); break;
        case 2: $value = $this->readString(); break;
        default:
          throw new \Exception("Invalid wiretype received: $type");
      }

      $fields[] = array(
        "field" => $field,
        "value" => $value,
      );
    }

    return $fields;
  }
}