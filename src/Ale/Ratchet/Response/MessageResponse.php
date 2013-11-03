<?php

namespace Ale\Ratchet\Response;

use Ale\Ratchet\InvalidArgumentException;

/**
 *
 * Simple data response only for own handled message.
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class MessageResponse extends \Nette\Object implements IResponse {


	/**
	 * @var string
	 */
	private $data;


	/**
	 * @param string $data
	 * @throws InvalidArgumentException
	 */
	public function __construct($data)
	{
		if (is_object($data) || is_array($data))
			throw new InvalidArgumentException('Message in DataResponse doesnt support array of object.');

		$this->data = $data;
	}


	/**
	 * @return string
	 */
	public function create()
	{
		return $this->data;
	}

}