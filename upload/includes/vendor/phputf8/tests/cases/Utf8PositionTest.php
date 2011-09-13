<?php

require_once UTF8.'/utils/position.php';


class Utf8PositionTest extends TestLibTestCase
{
	protected $name = 'utf8_byte_position(), utf8_locate_current_chr()';

	protected function test_ascii_char_to_byte()
	{
		$str = 'testing';
		$this->is_identical(utf8_byte_position($str, 3), 3);
		$this->is_identical(utf8_byte_position($str, 3, 4), array(3, 4));
		$this->is_identical(utf8_byte_position($str, -1), 0);
		$this->is_identical(utf8_byte_position($str, 8), 7);
	}

	protected function test_multibyte_char_to_byte()
	{
		$str = 'Iñtërnâtiônàlizætiøn';
		$this->is_identical(utf8_byte_position($str, 3), 4);
		$this->is_identical(utf8_byte_position($str, 3, 5), array(4, 7));
		$this->is_identical(utf8_byte_position($str, -1), 0);
		$this->is_identical(utf8_byte_position($str, 28), 27);
	}

	// Tests for utf8_locate_current_chr & utf8_locate_next_chr
	protected function test_singlebyte()
	{
		$tests   = array();

		// Single byte, should return current index
		$tests[] = array('aaживπά우리をあöä', 0, 0);
		$tests[] = array('aaживπά우리をあöä', 1, 1);

		foreach($tests as $test)
			$this->is_identical(utf8_locate_current_chr($test[0], $test[1]), $test[2]);

		$tests   = array();
		$tests[] = array('aaживπά우리をあöä', 1, 1);

		foreach($tests as $test)
			$this->is_identical(utf8_locate_next_chr($test[0], $test[1]), $test[2]);
	}

	protected function test_two_byte()
	{
		// Two byte, should move to boundary, expect even number
		$tests   = array();
		$tests[] = array('aaживπά우리をあöä', 2, 2);
		$tests[] = array('aaживπά우리をあöä', 3, 2);
		$tests[] = array('aaживπά우리をあöä', 4, 4);

		foreach($tests as $test)
			$this->is_identical(utf8_locate_current_chr($test[0], $test[1]), $test[2]);

		$tests   = array();
		$tests[] = array('aaживπά우리をあöä', 2, 2);
		$tests[] = array('aaживπά우리をあöä', 3, 4);
		$tests[] = array('aaживπά우리をあöä', 4, 4);

		foreach($tests as $test)
			$this->is_identical(utf8_locate_next_chr($test[0], $test[1]), $test[2]);
	}

	protected function test_threebyte()
	{
		// Three byte, should move to boundary 10 or 13
		$tests   = array();
		$tests[] = array('aaживπά우리をあöä', 10, 10);
		$tests[] = array('aaживπά우리をあöä', 11, 10);
		$tests[] = array('aaживπά우리をあöä', 12, 10);
		$tests[] = array('aaживπά우리をあöä', 13, 13);

		foreach($tests as $test)
			$this->is_identical(utf8_locate_current_chr($test[0], $test[1]), $test[2]);

		$tests   = array();
		$tests[] = array('aaживπά우리をあöä', 10, 10);
		$tests[] = array('aaживπά우리をあöä', 11, 13);
		$tests[] = array('aaживπά우리をあöä', 12, 13);
		$tests[] = array('aaживπά우리をあöä', 13, 13);

		foreach($tests as $test)
			$this->is_identical(utf8_locate_next_chr($test[0], $test[1]), $test[2]);
	}

	protected function test_bounds()
	{
		// Bounds checking
		$tests   = array();
		$tests[] = array('aaживπά우리をあöä', -2, 0);
		$tests[] = array('aaживπά우리をあöä', 128, 29);

		foreach($tests as $test)
			$this->is_identical(utf8_locate_current_chr($test[0], $test[1]), $test[2]);

		$tests[] = array('aaживπά우리をあöä', -2, 0);
		$tests[] = array('aaживπά우리をあöä', 128, 29);

		foreach($tests as $test)
			$this->is_identical(utf8_locate_next_chr($test[0], $test[1]), $test[2]);
	}
}
