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
	 * @var \SplObjectStorage
	 */
	protected $clients;


	/**
	 * Create connection management
	 */
	public function __construct()
	{
		$this->clients = new \SplObjectStorage;
	}


	/**
	 * @param ConnectionInterface $client
	 * @return $this
	 */
	public function addClient(ConnectionInterface $client)
	{
		$this->onOpen($client);
		$this->clients->attach($client);
		return $this;
	}


	/**
	 * @param ConnectionInterface $client
	 * @return $this
	 */
	public function removeClient(ConnectionInterface $client)
	{
		$this->onClose($client);
		$this->clients->detach($client);
		return $this;
	}


	/**
	 * @return \SplObjectStorage|\Traversable
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
		$res = $response->create();

		foreach ($this->clients as $client) {
			/** @var ConnectionInterface $client */
			$client->send($res);
		}
	}

}