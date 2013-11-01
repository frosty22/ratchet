<?php

namespace Ale\Ratchet;

/**
 *
 * @copyright Copyright (c) 2013 Ledvinka Vít
 * @author Ledvinka Vít, frosty22 <ledvinka.vit@gmail.com>
 *
 */
class Request extends \Nette\Object {


	/**
	 * Name of control
	 * @var string
	 */
	private $control;


	/**
	 * Name of target handle
	 * @var string
	 */
	private $handle;


	/**
	 * Array of parameters
	 * @var array
	 */
	private $params = array();


	/**
	 * @param string $control
	 * @param string $handle
	 * @param array $params
	 */
	public function __construct($control, $handle, array $params = array())
	{
		$this->control = $control;
		$this->handle = $handle;
		$this->params = $params;
	}


	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}


	/**
	 * @return string
	 */
	public function getHandle()
	{
		return $this->handle;
	}


	/**
	 * @return string
	 */
	public function getControl()
	{
		return $this->control;
	}


}