<?php

class Utf8StrlenTest extends TestLibTestCase
{
	protected $name = 'utf8_strlen()';

    public function test_utf8()
    {
		$str = 'Iñtërnâtiônàlizætiøn';
		$this->is_equal(utf8_strlen($str), 20);
    }

	protected function test_utf8_invalid()
	{
		$str = "Iñtërnâtiôn\xe9àlizætiøn";
		$this->is_equal(utf8_strlen($str), 20);
	}

	protected function test_ascii()
	{
		$str = 'ABC 123';
		$this->is_equal(utf8_strlen($str), 7);
	}

	protected function test_empty_str()
	{
		$str = '';
		$this->is_equal(utf8_strlen($str), 0);
	}
}
