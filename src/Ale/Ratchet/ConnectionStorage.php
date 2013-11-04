<?php

namespace Ale\Ratchet;

use Ale\Ratchet\Response\IResponse;
use Ratchet\ConnectionInterface;

/**
 *
 * Storage for manage all connections.
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 * @method onOpen(ConnectionInterface $client)
 * @method onClose(ConnectionInterface $client)
 *
 */
class ConnectionStorage extends \Nette\Object implements \IteratorAggregate {


	/**
	 * @var array
	 */
	public $onOpen = array();


	/**
	 * @var array
	 */
	public $onClose = array();


	/**
	 * @var Connection[]
	 */
	protected $clients = array();


	/**
	 * Create connection management
	 */
	public function __construct()
	{
		$this->clients = array();
	}


	/**
	 * @param ConnectionInterface $client
	 * @return $this
	 */
	public function addClient(ConnectionInterface $client)
	{
		$hash = spl_object_hash($client);

		if (!isset($this->clients[$hash])) {
			$this->clients[$hash] = new Connection($client);
			$this->onOpen($client);
		}

		return $this;
	}


	/**
	 * @param ConnectionInterface $client
	 * @return $this
	 */
	public function removeClient(ConnectionInterface $client)
	{
		$hash = spl_object_hash($client);

		if (isset($this->clients[$hash])) {
			$this->onClose($client);
			unset($this->clients[$hash]);
		}

		return $this;
	}


	/**
	 * @return Connection[]
	 */
	public function getIterator()
	{
		return $this->clients;
	}


	/**
	 * Send response to the all connections
	 * @param IResponse $response
	 */
	public function sendAll(IResponse $response)
	{
		foreach ($this->clients as $client) {
			$client->send($response);
		}
	}

}