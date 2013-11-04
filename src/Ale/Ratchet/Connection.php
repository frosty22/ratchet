<?php

namespace Ale\Ratchet;

use Ale\Ratchet\Response\IResponse;
use Ratchet\ConnectionInterface;

/**
 *
 * Single connection (proxy class of ConnectionInterface)
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Connection extends \Nette\Object {


	/**
	 * @var ConnectionInterface
	 */
	private $connection;


	/**
	 * @param ConnectionInterface $connection
	 */
	public function __construct(ConnectionInterface $connection)
	{
		$this->connection = $connection;
	}


	/**
	 * Close connection
	 */
	public function close()
	{
		$this->connection->close();
	}


	/**
	 * @param IResponse $response
	 */
	public function send(IResponse $response)
	{
		$this->connection->send($response->create());
	}


}