<?php

namespace Ale\Ratchet;

use Nette\ComponentModel\Container;
use React\EventLoop\LoopInterface;

/**
 * Ratchet server for Nette - run instead of Nette application.
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
	 * @var LoopInterface
	 */
	private $loop;


	/**
	 * @param Application $application
	 * @param LoopInterface $loop
	 * @param string $server The address to receive sockets on (0.0.0.0 means receive connections from any)
	 * @param int $port The port to server sockets on
	 */
	public function __construct(Application $application, LoopInterface $loop, $server, $port)
	{
		$this->application = $application;
		$this->server = $server;
		$this->port = $port;
		$this->loop = $loop;
	}


	/**
	 * Run IO server
	 */
	public function run()
	{
		$wsServer = new \Ratchet\WebSocket\WsServer($this->application);
		$httpServer = new \Ratchet\Http\HttpServer($wsServer);

		$socket = new \React\Socket\Server($this->loop);
		$socket->listen($this->port, $this->server);

		$server = new \Ratchet\Server\IoServer($httpServer, $socket, $this->loop);
		$server->run();
	}


}