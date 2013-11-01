<?php

namespace Ale\Ratchet {

	class Exception extends \Exception { }

	class RuntimeException extends Exception { }
	class LogicException extends Exception { }

	class BadRequestException extends RuntimeException { }

	class InvalidArgumentException extends LogicException { }

}