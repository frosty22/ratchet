<?php

namespace Ale\Ratchet\DI;

use Nette\Config\CompilerExtension;

/**
 *
 * Install ratchet server and other components to container.
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class RatchetExtension extends CompilerExtension {


	/**
	 * @var array
	 */
	private $defaults = array(
		"server" 	=> "0.0.0.0",
		"port"		=> 8080,
		"router"	=> array(
			"namespace"	=> "App",
			"control"	=> "Default",
			"handle"	=> "default"
		)
	);


	/**
	 * Load configuration
	 */
	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('router'))
			->setClass('Ale\Ratchet\Router\SimpleRouter', array($config["router"]["namespace"],
				$config["router"]["control"], $config["router"]["handle"]));

		$builder->addDefinition($this->prefix('connectionStorage'))
			->setClass('Ale\Ratchet\ConnectionStorage');

		$loop = $builder->addDefinition($this->prefix('loop'))
			->setClass('React\EventLoop\LoopInterface')
			->setFactory('React\EventLoop\Factory::create');

		$application = $builder->addDefinition($this->prefix('application'))
			->setClass('Ale\Ratchet\Application');

		$builder->addDefinition($this->prefix('server'))
			->setClass('Ale\Ratchet\Server', array($application, $loop, $config['server'], $config['port']));


	}

}