<?php

namespace Ale\Ratchet;

use Nette\ComponentModel\Container;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

/**
 * Ratchet server for Nette
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Server extends \Nette\Object {


	/**
	 * @var string
	 */
	private $server;


	/**
	 * @var int
	 */
	private $port;


	/**
	 * @var Application
	 */
	private $application;


	/**
	 * @param Application $application
	 * @param string $server
	 * @param int $port
	 */
	public function __construct(Application $application, $server, $port)
	{
		$this->application = $application;
		$this->server = $server;
		$this->port = $port;
	}


	/**
	 * Run IO server
	 */
	public function run()
	{
		$wsServer = new WsServer($this->application);
		$httpServer = new HttpServer($wsServer);

		$server = IoServer::factory($httpServer, $this->port, $this->server);
		$server->run();
	}


}