<?php

namespace App\Actions;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 *
 */
class Purify
{
	private $config;
	private $purifier;

	public function __construct()
	{
		$this->config = HTMLPurifier_Config::createDefault();
		$this->purifier = new HTMLPurifier($this->config);
	}

	public function cleanHTML(string $text): string
	{
		return $this->purifier->purify($text);
	}
}