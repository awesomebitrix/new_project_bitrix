<?php
/**
 * Created by OOO 1C-SOFT.
 * User: dremin_s
 * Date: 22.11.2016
 */

namespace Online1c\Iblock\Property;


use Online1c\Iblock\IblockMainException;

class PropertyException extends IblockMainException
{
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 *
	 * @param string $message [optional] The Exception message to throw.
	 * @param int $code [optional] The Exception code.
	 * @param \Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
	 *
	 * @since 5.1.0
	 */
	public function __construct($message, $code, \Exception $previous = null)
	{
		if($code == 0){
			$code = 2000;
		}
		parent::__construct($message, $code, $previous);
	}


}