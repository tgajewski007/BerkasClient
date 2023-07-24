<?php
namespace braga\berkascli\client;
use braga\berkascli\api\request\RegisterDownloadAliasRequest;
use braga\berkascli\api\types\ApiResource;
use braga\berkascli\api\types\ApiResourceSimple;
use braga\tools\benchmark\Benchmark;
use braga\tools\exception\BragaException;
use braga\tools\tools\UploadFileManager;
use braga\berkascli\api\types\ApiResourceComm;
use braga\berkascli\api\types\ApiOneTimeResourceUrl;

/**
 * Created on 2 kwi 2018 22:15:36
 * error prefix
 * @author Tomasz Gajewski
 * @package
 *
 */
class BerkasClient extends ApiClient
{
	// ------------------------------------------------------------------------------------------------------------------
	/**
	 * @param double $idBerkasResource
	 * @return ApiResource
	 */
	public function resource($idBerkasResource)
	{
		Benchmark::add(__METHOD__);
		$url = "/api.v1/resource/" . $idBerkasResource;
		$res = $this->get($url);
		$tmp = $this->inteprete($res, ApiResourceComm::class, 200);
		$retval = ApiResource::convert($tmp);
		return $retval;
	}
	// ------------------------------------------------------------------------------------------------------------------
	/**
	 * @param double $idBerkasResource
	 * @return ApiResourceSimple
	 */
	public function metaResource($idBerkasResource)
	{
		Benchmark::add(__METHOD__);
		$url = "/api.v1/metaResource/" . $idBerkasResource;
		$res = $this->get($url);
		return $this->inteprete($res, ApiResourceSimple::class, 200);
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param double $idBerkasResource
	 * @return ApiOneTimeResourceUrl
	 */
	public function registerOneTimeUrl($idBerkasResource)
	{
		Benchmark::add(__METHOD__);
		$url = "/api.v1/registerDownloadAlias";
		$body = new RegisterDownloadAliasRequest();
		$body->idResource = $idBerkasResource;
		$body->ipAddress = getRemoteIp();
		$res = $this->post($url, $body);
		return $this->inteprete($res, ApiOneTimeResourceUrl::class);
	}
	// ------------------------------------------------------------------------------------------------------------------
	/**
	 * @param double $idBerkasResource
	 */
	public function sendDownloadHeaderToBrowser($idBerkasResource)
	{
		Benchmark::add(__METHOD__);
		$i = $this->registerOneTimeUrl($idBerkasResource);
		header("Location: " . $i->oneTimeUrl);
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param UploadFileManager $file
	 * @return ApiResourceSimple
	 */
	public function save(UploadFileManager $file)
	{
		Benchmark::add(__METHOD__);
		$url = "/api.v1/resource";
		$multipart = array();

		$tmp = array();
		$tmp["name"] = "file";
		$tmp["filename"] = $file->getOrginalFilename();
		if(empty($file->getContent()))
		{
			$tmp["contents"] = file_get_contents($file->getTemporaryFilename());
		}
		else
		{
			$tmp["contents"] = $file->getContent();
		}
		$multipart[] = $tmp;
		if(empty($tmp["contents"]))
		{
			throw new BragaException("BT:10101 Brak zawartości", 10101);
		}
		if(empty($tmp["filename"]))
		{
			throw new BragaException("BT:10102 Brak nazwy pliku", 10102);
		}
		$md5 = md5($tmp["contents"]);

		$query = array();
		$query["md5"] = $md5;
		$res = $this->postMultipart($url, $query, $multipart);
		return $this->inteprete($res, ApiResourceSimple::class);
	}
	// -----------------------------------------------------------------------------------------------------------------
}