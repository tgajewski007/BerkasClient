<?php
namespace braga\berkascli\client;
use braga\tools\api\RestClient;
use braga\tools\benchmark\Benchmark;
use GuzzleHttp\Exception\BadResponseException;
use Monolog\Level;
class ApiClient extends RestClient
{
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @var BerkasClientAuth
	 */
	protected ?BerkasClientAuth $auth = null;
	// ----------------------------------------------------------------------------------------------------------------
	/**
	 * @param \braga\berkascli\client\BerkasClientAuth $auth
	 */
	public function setAuth(BerkasClientAuth $auth)
	{
		$this->auth = $auth;
	}
	// -----------------------------------------------------------------------------------------------------------------
	protected function getHeaders()
	{
		$retval = array();
		$retval["Authorization"] = "bearer " . $this->auth->getJWT()->toString();
		return $retval;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param string $url
	 * @param \stdClass $multipart
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	protected function postMultipart($url, array $query, array $multipart)
	{
		Benchmark::add(__METHOD__);
		$options = array();
		$options["headers"] = $this->getHeaders();
		$options["query"] = $query;
		$options["multipart"] = $multipart;
		$this->logRequest($this->baseUrl . $url, $options["query"]);
		try
		{
			$this->response = $this->client->post($this->baseUrl . $url, $options);
			$this->logResponse($this->baseUrl . $url, $this->response);
		}
		catch(BadResponseException $e)
		{
			$this->response = $e->getResponse();
			$this->logResponse($this->baseUrl . $url, $this->response, Level::Error);
		}
		return $this->response;
	}
	// -----------------------------------------------------------------------------------------------------------------
}
