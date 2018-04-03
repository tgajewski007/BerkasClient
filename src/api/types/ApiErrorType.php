<?php
namespace braga\berkascli\api\types;
/**
 * Created on 26 lut 2018 15:38:26
 * error prefix
 * @author Tomasz Gajewski
 * @package
 *
 */
class ApiErrorType
{
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * Numer błędu
	 * @var string
	 */
	public $number;
	/**
	 * Opis błędu
	 * @var string
	 */
	public $description;
	// -----------------------------------------------------------------------------------------------------------------
	public static function convertFromException(\Exception $e)
	{
		$retval = new self();
		$retval->number = $e->getCode();
		$retval->description = $e->getMessage();
		return $retval;
	}
	// -----------------------------------------------------------------------------------------------------------------
}