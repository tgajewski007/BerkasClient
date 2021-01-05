<?php
namespace braga\berkascli\api\types;
/**
 * Created on 2 kwi 2018 23:35:14
 * error prefix
 * @author Tomasz Gajewski
 * @package
 *
 */
class ApiResource extends ApiResourceSimple
{
	// -----------------------------------------------------------------------------------------------------------------
	public $content;
	// -----------------------------------------------------------------------------------------------------------------
	function __construct(ApiResourceComm $tmp)
	{
		parent::__construct($tmp);
		$this->content = base64_decode($tmp->base64Content);
	}
	// -----------------------------------------------------------------------------------------------------------------
}