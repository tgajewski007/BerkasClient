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
	// -----------------------------------------------------------------------------------------------------------------
	function __construct(ApiResourceSimple $tmp)
	{
		$this->contentType = $tmp->contentType;
		$this->createDate = $tmp->createDate;
		$this->idResource = $tmp->idResource;
		$this->name = $tmp->name;
	}
	// -----------------------------------------------------------------------------------------------------------------
}