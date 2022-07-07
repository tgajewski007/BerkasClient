<?php
namespace braga\berkascli\client;
use braga\tools\security\OAuthToken;
/**
 * Created on 2 kwi 2018 22:56:42
 * error prefix
 * @author Tomasz Gajewski
 * @package
 *
 */
class BerkasClientAuth
{
	use OAuthToken;

	// -----------------------------------------------------------------------------------------------------------------
	protected $isseRealms;
	protected $clientId;
	protected $clientSecret;
	// -----------------------------------------------------------------------------------------------------------------
	function __construct($isseRealms, $clientId, $clientSecret)
	{
		$this->isseRealms = $isseRealms;
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return \Lcobucci\JWT\Token\Plain
	 */
	public function getJWT()
	{
		return $this->createToken($this->isseRealms, $this->clientId, $this->clientSecret);
	}
	// -----------------------------------------------------------------------------------------------------------------
}