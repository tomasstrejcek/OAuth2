<?php
namespace Drahak\OAuth2\Http;

use Nette\Http\IRequest;
use Nette\Object;

/**
 * Input parser
 * @package Drahak\OAuth2\Http
 * @author Drahomír Hanák
 */
class Input extends Object implements IInput
{

	/** @var IRequest */
	private $request;

	/** @var array */
	private $data;

	public function __construct(IRequest $request)
	{
		$this->request = $request;
	}

	/**
	 * Get all parameters
	 * @return array
	 */
	public function getParameters()
	{
		if (!$this->data) {
			if ($this->request->getQuery()) {
				$this->data = $this->request->getQuery();
			} else if ($this->request->getPost()) {
				$this->data = $this->request->getPost();
			} else {
				$this->data = $this->parseRequest(file_get_contents('php://input'));
			}
		}
		return $this->data;
	}

	/**
	 * Get single parameter by key
	 * @param string $key
	 * @return string|int
	 */
	public function getParameter($key)
	{
		$parameters = $this->getParameters();
		return isset($parameters[$key]) ? $parameters[$key] : NULL;
	}

	/**
	 * Convert client request data to array or traversable
	 * @param string $data
	 * @return array
	 */
	private function parseRequest($data)
	{
		$result = array();
		parse_str($data, $result);
		return $result;
	}


}