<?php
namespace braga\berkascli\client;
use braga\berkascli\api\request\RegisterDownloadAliasRequest;
use braga\berkascli\api\types\ApiResource;
use braga\berkascli\api\types\ApiResourceSimple;
use braga\tools\tools\UploadFileManager;

/**
 * Created on 2 kwi 2018 22:15:36
 * error prefix
 * @author Tomasz Gajewski
 * @package
 *
 */
class BerkasClient
{
	// -----------------------------------------------------------------------------------------------------------------
	protected $baseUrl = "https://berkas.pl/";
	/**
	 *
	 * @var BerkasClientAuth
	 */
	protected $auth;
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 *
	 * @return \braga\berkascli\client\BerkasClientAuth
	 */
	public function getAuth()
	{
		return $this->auth;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 *
	 * @param \braga\berkascli\client\BerkasClientAuth $auth
	 */
	public function setAuth(BerkasClientAuth $auth)
	{
		$this->auth = $auth;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 *
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 *
	 * @param double $idBerkasResource
	 * @return ApiResource
	 */
	public function get($idBerkasResource)
	{
		$tmp = $this->curlGet("resource/" . $idBerkasResource);
		/** @var ApiResourceComm $tmp  */
		$retval = new ApiResource();
		$retval->contentType = $tmp->contentType;
		$retval->createDate = $tmp->createDate;
		$retval->idResource = $tmp->idResource;
		$retval->name = $tmp->name;
		$retval->content = base64_decode($tmp->base64Content);
		return $retval;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 *
	 * @param double $idBerkasResource
	 * @return ApiResourceSimple
	 */
	public function meta($idBerkasResource)
	{
		$tmp = $this->curlGet("metaResource/" . $idBerkasResource);
		/** @var ApiResourceSimple $tmp  */
		$retval = new ApiResourceSimple();
		$retval->contentType = $tmp->contentType;
		$retval->createDate = $tmp->createDate;
		$retval->idResource = $tmp->idResource;
		$retval->name = $tmp->name;
		return $retval;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 *
	 * @param double $idBerkasResource
	 */
	public function sendDownloadHeaderToBrowser($idBerkasResource)
	{
		$url = $this->baseUrl . "registerDownloadAlias";
		$postData = array();
		$r = new RegisterDownloadAliasRequest();
		$r->idResource = $idBerkasResource;
		$r->ipAddress = getRemoteIp();
		$postData["param"] = json_encode($r);

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_HTTPHEADER, [
						"Authorization: bearer " . $this->getAuth()->getJWT()->__toString() ]);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($c, CURLOPT_POSTFIELDS, $postData);

		$output = curl_exec($c);

		curl_close($c);
		$tmp = json_decode($output);
		/** @var \braga\berkascli\api\types\ApiOneTimeResourceUrl $tmp */
		if(isset($tmp->oneTimeUrl))
		{
			header("Location: " . $tmp->oneTimeUrl);
			exit();
		}
		else
		{
			throw new \Exception($tmp->description);
		}
	}
	// -----------------------------------------------------------------------------------------------------------------
	public function save(UploadFileManager $file)
	{
		$tmp = $this->curlUploadFile($file);
		if(isset($tmp->idResource))
		{
			$retval = new ApiResourceSimple();
			$retval->contentType = $tmp->contentType;
			$retval->createDate = $tmp->createDate;
			$retval->idResource = $tmp->idResource;
			$retval->name = $tmp->name;
			return $retval;
		}
		else
		{
			throw new \Exception("BR:10001 Błąd zapisania załącznika", 10001);
		}
	}
	// -----------------------------------------------------------------------------------------------------------------
	private function curlGet($url)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->getBaseUrl() . $url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($c);
		curl_close($c);
		return json_decode($output);
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 *
	 * @param UploadFileManager $file
	 * @return ApiResourceSimple
	 */
	private function curlUploadFile(UploadFileManager $file)
	{
		$url = $this->baseUrl . "resource";
		$postData = array();
		$postData["file"] = curl_file_create($file->getTemporaryFilename(), $file->getMimeType(), $file->getOrginalFilename());

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_HTTPHEADER, array(
						'Content-Type: multipart/form-data' ));
		curl_setopt($c, CURLOPT_HTTPHEADER, [
						"Authorization: bearer " . $this->getAuth()->getJWT()->__toString() ]);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($c, CURLOPT_POSTFIELDS, $postData);

		$output = curl_exec($c);

		curl_close($c);
		return json_decode($output);
	}
	// -----------------------------------------------------------------------------------------------------------------
}