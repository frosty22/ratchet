<?php

namespace Ale\Ratchet\UI;

use Ale\Ratchet\ConnectionStorage;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
abstract class Control extends \Nette\Object {


	/**
	 * @var ConnectionStorage
	 */
	private $connectionStorage;


	/**
	 * @param ConnectionStorage $connection
	 */
	final public function __construct(ConnectionStorage $connection)
	{
		$this->connectionStorage = $connection;
	}


	/**
	 * @return ConnectionStorage
	 */
	public function getConnectionStorage()
	{
		return $this->connectionStorage;
	}


	/**
	 * Startup, is call before call handle
	 */
	public function startup()
	{
	}


	/**
	 * Shutdown, is call after call handle
	 */
	public function shutdown()
	{
	}


}