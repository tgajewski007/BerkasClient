<?php
namespace braga\berkascli\client;
use braga\tools\benchmark\Benchmark;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;
use braga\graylogger\BaseLogger;
use braga\graylogger\LoggerService;
use braga\tools\api\types\response\ErrorResponseType;
use braga\tools\exception\BusinesException;
class ApiClient
{
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @var Client
	 */
	protected $client;
	/**
	 * @var ResponseInterface
	 */
	protected $response;
	/**
	 * @var BerkasClientAuth
	 */
	protected $auth;
	protected $logClassName;
	protected $baseUrl;
	// -----------------------------------------------------------------------------------------------------------------
	function __construct($baseUrl, $logClassName = BaseLogger::class)
	{
		$this->baseUrl = $baseUrl;
		$this->logClassName = $logClassName;
		$this->client = new Client();
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param string $logClassName
	 */
	public function setLogClassName($logClassName)
	{
		$this->logClassName = $logClassName;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}
	// ----------------------------------------------------------------------------------------------------------------
	/**
	 * @return \braga\berkascli\client\BerkasClientAuth
	 */
	public function getAuth()
	{
		return $this->auth;
	}
	// ----------------------------------------------------------------------------------------------------------------
	/**
	 * @param \braga\berkascli\client\BerkasClientAuth $auth
	 */
	public function setAuth(BerkasClientAuth $auth)
	{
		$this->auth = $auth;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	protected function getResponse()
	{
		return $this->response;
	}
	// -----------------------------------------------------------------------------------------------------------------
	protected function getAuthHeaders()
	{
		$retval = array();

		$retval["Authorization"] = "bearer " . $this->getAuth()->getJWT()->toString();
		return $retval;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param string $url
	 * @param $body
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	protected function post($url, $body)
	{
		Benchmark::add(__METHOD__);
		$options = array();
		$options["headers"] = $this->getAuthHeaders();
		$options["body"] = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
		$this->logRequest($url, $options["body"]);
		try
		{
			$this->response = $this->client->post($this->baseUrl . $url, $options);
			$this->logResponse($url, $this->response);
		}
		catch(BadResponseException $e)
		{
			$this->response = $e->getResponse();
			$this->logResponse($url, $this->response, LoggerService::ERROR);
		}
		return $this->response;
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
		$options["headers"] = $this->getAuthHeaders();
		$options["query"] = $query;
		$options["multipart"] = $multipart;
		$this->logRequest($url, "");
		try
		{
			$this->response = $this->client->post($this->baseUrl . $url, $options);
			$this->logResponse($url, $this->response);
		}
		catch(BadResponseException $e)
		{
			$this->response = $e->getResponse();
			$this->logResponse($url, $this->response, LoggerService::ERROR);
		}
		return $this->response;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param string $url
	 * @param \stdClass $body
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	protected function put($url, $body)
	{
		Benchmark::add(__METHOD__);
		$options = array();
		$options["headers"] = $this->getAuthHeaders();
		$options["body"] = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
		$this->logRequest($url, $options["body"]);
		try
		{
			$this->response = $this->client->put($this->baseUrl . $url, $options);
			$this->logResponse($url, $this->response);
		}
		catch(BadResponseException $e)
		{
			$this->response = $e->getResponse();
			$this->logResponse($url, $this->response, LoggerService::ERROR);
		}
		return $this->response;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param string $url
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	protected function get($url)
	{
		Benchmark::add(__METHOD__);
		$options = array();
		$options["headers"] = $this->getAuthHeaders();
		$this->logRequest($url, null);
		try
		{
			$this->response = $this->client->get($this->baseUrl . $url, $options);
			$this->logResponse($url, $this->response);
		}
		catch(BadResponseException $e)
		{
			$this->response = $e->getResponse();
			$this->logResponse($url, $this->response, LoggerService::ERROR);
		}
		return $this->response;
	}
	// -----------------------------------------------------------------------------------------------------------------
	/**
	 * @param string $url
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	protected function delete($url)
	{
		Benchmark::add(__METHOD__);
		$options = array();
		$options["headers"] = $this->getAuthHeaders();
		$this->logRequest($url, null);
		try
		{
			$this->response = $this->client->delete($this->baseUrl . $url, $options);
			$this->logResponse($url, $this->response);
		}
		catch(BadResponseException $e)
		{
			$this->response = $e->getResponse();
			$this->logResponse($url, $this->response, LoggerService::ERROR);
		}
		return $this->response;
	}
	// -----------------------------------------------------------------------------------------------------------------
	protected function logRequest($url, $body)
	{
		Benchmark::add(__METHOD__);
		$context = array();
		$context["body"] = $body;
		$context["class"] = static::class;
		$this->logClassName::info($url, $context);
	}
	// -----------------------------------------------------------------------------------------------------------------
	protected function logResponse($url, ResponseInterface $res, $level = LoggerService::INFO)
	{
		Benchmark::add(__METHOD__);
		$context = array();
		$context["body"] = $res->getBody()->getContents();
		$context["class"] = static::class;
		$context["status"] = $res->getStatusCode();
		$this->logClassName::log($level, $url . " Response: " . $res->getStatusCode(), $context);
		$res->getBody()->rewind();
	}
	// -----------------------------------------------------------------------------------------------------------------
	protected function inteprete(ResponseInterface $res, $class, $successCode = 200)
	{
		Benchmark::add(__METHOD__);
		$mapper = new \JsonMapper();
		$mapper->bStrictNullTypes = false;
		if($res->getStatusCode() == $successCode)
		{
			$retval = $mapper->map(json_decode($res->getBody()->getContents()), new $class());
			return $retval;
		}
		else
		{
			$resError = $mapper->map(json_decode($res->getBody()), new ErrorResponseType());
			/**  @var \braga\tools\api\types\response\ErrorResponseType $resError */
			$err = reset($resError->error);
			/**  @var \braga\tools\api\types\type\ErrorType $err */
			throw new BusinesException($err->number . " " . $err->description);
		}
	}
	// -----------------------------------------------------------------------------------------------------------------
	protected function intepreteArray(ResponseInterface $res, $class, $successCode = 200)
	{
		Benchmark::add(__METHOD__);
		$mapper = new \JsonMapper();
		$mapper->bStrictNullTypes = false;
		if($res->getStatusCode() == $successCode)
		{
			$json = $res->getBody()->getContents();
			$obj = json_decode($json);
			$retval = $mapper->mapArray($obj, array(), $class);
			return $retval;
		}
		else
		{
			$resError = $mapper->map(json_decode($res->getBody()), new ErrorResponseType());
			/**  @var \braga\tools\api\types\response\ErrorResponseType $resError */
			$err = reset($resError->error);
			/**  @var \braga\tools\api\types\type\ErrorType $err */
			throw new BusinesException($err->number . " " . $err->description);
		}
	}
	// -----------------------------------------------------------------------------------------------------------------
}
