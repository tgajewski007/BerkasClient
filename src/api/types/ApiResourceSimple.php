<?php
namespace braga\berkascli\api\types;

/**
 * Created on 2 kwi 2018 18:38:38
 * error prefix
 * @author Tomasz Gajewski
 * @package
 *
 */
class ApiResourceSimple
{
	// -----------------------------------------------------------------------------------------------------------------
	public $idResource;
	public $createDate;
	public $name;
	public $contentType;
	public $sha256;
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param ApiResourceSimple $tmp
	 * @return \braga\berkascli\api\types\ApiResourceSimple|\braga\berkascli\api\types\ApiResource
	 */
	public static function convert(ApiResourceComm $tmp)
	{
		$retval = new static();
		$retval->contentType = $tmp->contentType;
		$retval->createDate = $tmp->createDate;
		$retval->idResource = $tmp->idResource;
		$retval->name = $tmp->name;
		$retval->sha256 = $tmp->sha256;
		return $retval;
	}
	// -----------------------------------------------------------------------------------------------------------------
}