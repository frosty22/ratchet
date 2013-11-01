<?php

namespace Ale\Ratchet;

use Ale\Ratchet\Router\IRouter;

use Nette\DI\Container;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 * @method onException(ConnectionInterface $conn, \Exception $e)
 *
 */
class Application extends \Nette\Object implements MessageComponentInterface {


	/**
	 * @var array
	 */
	public $onException = array();


	/**
	 * @var Container
	 */
	private $container;


	/**
	 * @var ConnectionStorage
	 */
	private $connectionStorage;


	/**
	 * @var IRouter
	 */
	private $router;


	/**
	 * @param Container $container Service locator for inject services to the control
	 * @param ConnectionStorage $connection
	 * @param IRouter $router
	 */
	public function __construct(Container $container, ConnectionStorage $connection, IRouter $router)
	{
		$this->container = $container;
		$this->connectionStorage = $connection;
		$this->router = $router;
	}


	/**
	 * @param ConnectionInterface $conn
	 */
	public function onOpen(ConnectionInterface $conn)
	{
		$this->connectionStorage->addClient($conn);
	}


	/**
	 * @param ConnectionInterface $conn
	 */
	public function onClose(ConnectionInterface $conn)
	{
		$this->connectionStorage->removeClient($conn);
	}


	/**
	 * @param ConnectionInterface $conn
	 * @param \Exception $e
	 */
	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		$this->onException($conn, $e);
		$conn->close();
	}


	public function onMessage(ConnectionInterface $from, $msg)
	{
		$request = $this->router->match($msg);

		if (is_null($request))
			throw new BadRequestException('Invalid message - router cant create request.');

		if (!$request instanceof Request)
			throw new InvalidArgumentException('Route must return Ale\Ratchet\Request.');

		$className = $request->getControl();
		if (!$className || !class_exists($className))
			throw new BadRequestException('Control class "' . $className . '" not found.');

		if (!is_subclass_of($className, 'Ale\Ratchet\UI\Control'))
			throw new BadRequestException($className . ' must be subclass of Ale\Ratchet\UI\Control.');

		$handleName = $request->getHandle();
		if (!$handleName)
			throw new BadRequestException('Handle cannot be empty.');

		if (!method_exists($className, $handleName))
			throw new BadRequestException('Method "' . $handleName . '" not found in class "' . $className . '".');

		$control = new $className($this->connectionStorage);
		/** @var \Ale\Ratchet\UI\Control $control */

		foreach (array_reverse(get_class_methods($control)) as $method) {
			if (substr($method, 0, 6) === 'inject') {
				$this->container->callMethod(array($control, $method));
			}
		}

		$control->startup();

		if (!$this->tryCall($control, $handleName, $request->getParams()))
			throw new InvalidArgumentException('Handle method "' . $handleName . '" of "' . $className . '" must be callable.');

		$control->shutdown();
	}


	/**
	 * Call method of object
	 * @param \Nette\Object $obj
	 * @param string $method
	 * @param array $params
	 * @return bool
	 */
	protected function tryCall(\Nette\Object $obj, $method, array $params)
	{
		$rc = $obj->getReflection();

		$rm = $rc->getMethod($method);
		if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
			$rm->invokeArgs($obj, $this->combineArgs($rm, $params));
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * @param \ReflectionFunctionAbstract $method
	 * @param array $args
	 * @return array
	 * @throws BadRequestException
	 */
	protected function combineArgs(\ReflectionFunctionAbstract $method, array $args)
	{
		$res = array();
		$i = 0;

		foreach ($method->getParameters() as $param) {
			$name = $param->getName();

			if (isset($args[$name])) {
				$res[$i++] = $args[$name];
			} elseif (isset($args[$i])) {
				$value = $args[$i];
				$res[$i++] = $value;
			} else {
				$res[$i++] = $param->isDefaultValueAvailable() && $param->isOptional() ?
					$param->getDefaultValue() : ($param->isArray() ? array() : NULL);
			}

			$type = $param->isArray() ? 'array' :
				($param->isDefaultValueAvailable() && $param->isOptional() ? gettype($param->getDefaultValue()) : 'NULL');

			if (!$this->convertType($res[$i-1], $type)) {
				$mName = $method instanceof \ReflectionMethod ?
					$method->getDeclaringClass()->getName() . '::' . $method->getName() : $method->getName();

				throw new BadRequestException("Invalid value for parameter '$name' in method $mName(), expected " .
				($type === 'NULL' ? 'scalar' : $type) . ".");
			}

		}

		return $res;
	}


	/**
	 * Non data-loss type conversion.
	 * @param  mixed
	 * @param  string
	 * @return bool
	 */
	public function convertType(& $val, $type)
	{
		if ($val === NULL || is_object($val)) {
			// ignore
		} elseif ($type === 'array') {
			if (!is_array($val)) {
				return FALSE;
			}
		} elseif (!is_scalar($val)) {
			return FALSE;

		} elseif ($type !== 'NULL') {
			$old = $val = ($val === FALSE ? '0' : (string) $val);
			settype($val, $type);
			if ($old !== ($val === FALSE ? '0' : (string) $val)) {
				return FALSE; // data-loss occurs
			}
		}
		return TRUE;
	}



}