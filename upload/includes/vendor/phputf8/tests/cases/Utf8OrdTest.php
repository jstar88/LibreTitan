<?php

require_once UTF8.'/functions/ord.php';


class Utf8OrdTest extends TestLibTestCase
{
	protected $name = 'utf8_ord()';

	protected function test_empty_str()
	{
		$str = '';
		$this->is_equal(utf8_ord($str), 0);
	}

	protected function test_ascii_char()
	{
		$str = 'a';
		$this->is_equal(utf8_ord($str), 97);
	}

	protected function test_2_byte_char()
	{
		$str = 'ñ';
		$this->is_equal(utf8_ord($str), 241);
	}

	protected function test_3_byte_char()
	{
		$str = '₧';
		$this->is_equal(utf8_ord($str), 8359);
	}

	protected function test_4_byte_char()
	{
		$str = "\xf0\x90\x8c\xbc";
		$this->is_equal(utf8_ord($str), 66364);
	}
}
