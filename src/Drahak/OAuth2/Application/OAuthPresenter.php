<?php
namespace Drahak\OAuth2\Application;

use Drahak\OAuth2\Grant\GrantContext;
use Drahak\OAuth2\Grant\IGrant;
use Drahak\OAuth2\Grant\InvalidGrantTypeException;
use Drahak\OAuth2\InvalidScopeException;
use Drahak\OAuth2\InvalidStateException;
use Drahak\OAuth2\OAuthException;
use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage;
use Drahak\OAuth2\Storage\InvalidAuthorizationCodeException;
use Drahak\OAuth2\Grant\GrantType;
use Drahak\OAuth2\Storage\Clients\IClientStorage;
use Drahak\OAuth2\UnauthorizedClientException;
use Drahak\OAuth2\UnsupportedResponseTypeException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Http\Url;

/**
 * OauthPresenter
 * @package Drahak\OAuth2\Application
 * @author Drahomír Hanák
 *
 * @property-read IGrant $grantType
 */
class OAuthPresenter extends Presenter implements IOAuthPresenter
{

	/** @var GrantContext */
	private $grantContext;

	/** @var Storage\AuthorizationCodeFacade */
	protected $authorizationCode;

	/** @var IClientStorage */
	protected $clientStorage;

	/** @var IClient */
	protected $client;

	/**
	 * Inject grant strategy context
	 * @param GrantContext $grantContext
	 */
	public function injectGrant(GrantContext $grantContext)
	{
		$this->grantContext = $grantContext;
	}

	/**
	 * Inject token manager - authorization code
	 * @param Storage\AuthorizationCodeFacade $authorizationCode
	 */
	public function injectAuthorizationCode(Storage\AuthorizationCodeFacade $authorizationCode)
	{
		$this->authorizationCode = $authorizationCode;
	}

	/**
	 * Injet client storage
	 * @param IClientStorage $clientStorage
	 */
	public function injectClientStorage(IClientStorage $clientStorage)
	{
		$this->clientStorage = $clientStorage;
	}

	/**
	 * On presenter startup
	 */
	protected function startup()
	{
		parent::startup();
		$this->client = $this->clientStorage->getClient(
			$this->getParameter(GrantType::CLIENT_ID_KEY),
			$this->getParameter(GrantType::CLIENT_SECRET_KEY)
		);
	}

	/**
	 * Get grant type
	 * @return IGrant
	 * @throws UnsupportedResponseTypeException
	 */
	public function getGrantType()
	{
		$request = $this->getHttpRequest();
		$grantType = $request->getPost(GrantType::GRANT_TYPE_KEY);
		try {
			return $this->grantContext->getGrantType($grantType);
		} catch (InvalidStateException $e) {
			throw new UnsupportedResponseTypeException('Trying to use unknown grant type ' . $grantType, $e);
		}
	}

	/**
	 * Provide OAuth2 error response (redirect or at least JSON)
	 * @param OAuthException $exception
	 */
	public function oauthError(OAuthException $exception)
	{
		$error = array(
			'error' => $exception->getKey(),
			'error_description' => $exception->getMessage()
		);
		$this->oauthResponse($error, $this->getParameter('redirect_uri'));
	}

	/**
	 * Send OAuth response
	 * @param array|\Traversable $data
	 * @param string|null $redirectUrl
	 * @param int $code
	 */
	public function oauthResponse($data, $redirectUrl = NULL, $code = 200)
	{
		if ($data instanceof \Traversable) {
			$data = iterator_to_array($data);
		}
		$data = (array)$data;

		// Redirect, if there is URL
		if ($redirectUrl !== NULL) {
			$url = new Url($redirectUrl);
			if ($this->getParameter('response_type') == 'token') {
				$url->setFragment(http_build_query($data));
			} else {
				$url->appendQuery($data);
			}
			$this->redirectUrl($url);
		}

		// else send JSON response
		foreach ($data as $key => $value) {
			$this->payload->$key = $value;
		}
		$this->getHttpResponse()->setCode($code);
		$this->sendResponse(new JsonResponse($this->payload));
	}

	/**
	 * @param string $responseType
	 * @param string $redirectUrl
	 * @param string|null $scope
	 *
	 * @throws InvalidScopeException
	 * @throws UnauthorizedClientException
	 * @throws UnsupportedResponseTypeException
	 */
	public function issueAuthorizationCode($responseType, $redirectUrl, $scope = NULL)
	{
		if ($responseType !== 'code') {
			throw new UnsupportedResponseTypeException;
		}
		if (!$this->client->getId()) {
			throw new UnauthorizedClientException;
		}

		try {
			$scope = array_filter(explode(',', str_replace(' ', ',', $scope)));
			$code = $this->authorizationCode->create($this->client, $this->user->getId(), $scope);
			$data = array(
				'code' => $code->getAuthorizationCode()
			);
			$this->oauthResponse($data, $redirectUrl);
		} catch (OAuthException $e) {
			$this->oauthError($e);
		}
	}

	/**
	 * Issue access token to client
	 * @param string|null $grantType
	 * @param string|null $redirectUrl
	 *
	 * @throws InvalidAuthorizationCodeException
	 * @throws InvalidGrantTypeException
	 * @throws InvalidStateException
	 */
	public function issueAccessToken($grantType = NULL, $redirectUrl = NULL)
	{
		try {
			if ($grantType !== NULL) {
				$grantType = $this->grantContext->getGrantType($grantType);
			} else {
				$grantType = $this->getGrantType();
			}

			$response = $grantType->getAccessToken($this->getHttpRequest());
			$this->oauthResponse($response, $redirectUrl);
		} catch (OAuthException $e) {
			$this->oauthError($e);
		}
	}

}