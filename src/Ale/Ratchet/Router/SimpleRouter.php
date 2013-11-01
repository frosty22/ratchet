<?php

namespace Ale\Ratchet\Router;

use Ale\Ratchet\Request;

/**
 *
 * Simple convert messages, from JSON format:
 *
 * {
 *   "path" : "Module:Control:handle"
 *   "data" : <params>
 * }
 *
 * - <params> string|object - can be string for ordinary params, or JSON association params
 * like name:value
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class SimpleRouter extends \Nette\Object implements IRouter {


	/**
	 * @var string
	 */
	private $defaultNamespace;


	/**
	 * @var string
	 */
	private $defaultControl;


	/**
	 * @var string
	 */
	private $defaultHandle;


	/**
	 * @param string $defaultNamespace
	 * @param string $defaultControl
	 * @param string $defaultHandle
	 */
	public function __construct($defaultNamespace, $defaultControl, $defaultHandle)
	{
		$this->defaultNamespace = $defaultNamespace;
		$this->defaultControl = $defaultControl;
		$this->defaultHandle = $defaultHandle;
	}


	/**
	 * Convert incoming message to the request, if not match return NULL
	 * @param string $message
	 * @return Request|NULL
	 */
	public function match($message)
	{
		$obj = json_decode($message, TRUE);

		if ($obj === NULL || !isset($obj["path"]))
			return NULL;

		$parts = array_reverse(explode(':', $obj["path"]));

		$handle = 'handle' . ucfirst(empty($parts[0]) ? $this->defaultHandle : $parts[0]);
		$className = ucfirst(empty($parts[1]) ? $this->defaultControl : $parts[1]) . 'Control';

		unset($parts[0], $parts[1]);
		$parts = array_reverse($parts);

		$namespace = $this->defaultNamespace ? $this->defaultNamespace . '\\' : '';
		foreach ($parts as $part) {
			$namespace .= ucfirst($part . 'Module') . '\\';
		}

		$params = array();
		if (isset($obj["data"])) {
			if (is_array($obj["data"])) $params = $obj["data"];
			else $params = array(0 => $obj["data"]);
		}

		return new Request($namespace . $className, $handle, $params);
	}

}