<?php

namespace Ale\Ratchet\Response;

use Ale\Ratchet\InvalidArgumentException;
use Nette\Utils\Json;

/**
 *
 * Common response for call method in client side
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class CallResponse extends \Nette\Object implements IResponse {


	/**
	 * Name of method
	 * @var string
	 */
	private $name;


	/**
	 * @var array
	 */
	private $data = array();


	/**
	 * @param string $name
	 * @param array $data
	 * @throws InvalidArgumentException
	 */
	public function __construct($name, array $data = array())
	{
		if (!is_string($name) || empty($name))
			throw new InvalidArgumentException('Name of call must be non-empty string.');

		$this->name = $name;
		$this->data = $data;
	}


	/**
	 * @return string
	 */
	public function create()
	{
		return Json::encode((object)array('type' => 'call', 'name' => $this->name, 'data' => $this->data));
	}

}