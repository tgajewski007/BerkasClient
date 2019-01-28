<?php
namespace braga\berkascli\client;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha512;

/**
 * Created on 2 kwi 2018 22:56:42
 * error prefix
 * @author Tomasz Gajewski
 * @package
 *
 */
class BerkasClientAuth
{
	// -----------------------------------------------------------------------------------------------------------------
	protected $userName;
	protected $privateKey;
	protected $issuer;
	protected $audiance;
	protected $tokenSerial;
	protected $validateMinues;
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return mixed
	 */
	public function getIssuer()
	{
		return $this->issuer;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return mixed
	 */
	public function getAudiance()
	{
		return $this->audiance;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return mixed
	 */
	public function getTokenSerial()
	{
		return $this->tokenSerial;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return mixed
	 */
	public function getValidateMinues()
	{
		return $this->validateMinues;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param mixed $issuer
	 */
	public function setIssuer($issuer)
	{
		$this->issuer = $issuer;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param mixed $audiance
	 */
	public function setAudiance($audiance)
	{
		$this->audiance = $audiance;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param mixed $tokenSerial
	 */
	public function setTokenSerial($tokenSerial)
	{
		$this->tokenSerial = $tokenSerial;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param mixed $validateMinues
	 */
	public function setValidateMinues($validateMinues)
	{
		$this->validateMinues = $validateMinues;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return mixed
	 */
	public function getUserName()
	{
		return $this->userName;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return mixed
	 */
	public function getPrivateKey()
	{
		return $this->privateKey;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param mixed $userName
	 */
	public function setUserName($userName)
	{
		$this->userName = $userName;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param mixed $privateKey
	 */
	public function setPrivateKey($privateKey)
	{
		$this->privateKey = $privateKey;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return \Lcobucci\JWT\Token
	 */
	public function getJWT()
	{
		$signer = new Sha512();
		$key = new Key($this->getPrivateKey());
		$token = new Builder();
		$token->setIssuer($this->getIssuer());
		$token->setAudience($this->getAudiance());
		$token->setId($this->getTokenSerial());
		$token->setIssuedAt(time() - 60);
		$token->setNotBefore(time() - 60);
		$token->setExpiration(time() + $this->getValidateMinues() * 60);
		$token->set('uid', $this->getUserName());
		$token->sign($signer, $key);
		return $token->getToken();
	}
	// -----------------------------------------------------------------------------------------------------------------
}