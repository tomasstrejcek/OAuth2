<?php
namespace Drahak\OAuth2\Grant;

use Drahak\OAuth2\Token;
use Drahak\OAuth2\Token\AccessToken;
use Drahak\OAuth2\Token\RefreshToken;
use Drahak\OAuth2\Token\IToken;

/**
 * AuthorizationCode
 * @package Drahak\OAuth2\Grant
 * @author Drahomír Hanák
 */
class AuthorizationCode extends GrantType
{

	/** @var array */
	private $scope = array();

	/**
	 * @return array
	 */
	protected function getScope()
	{
		return $this->scope;
	}

	/**
	 * Get authorization code identifier
	 * @return string
	 */
	public function getIdentifier()
	{
		return self::AUTHORIZATION_CODE;
	}

	/**
	 * Verify request
	 * @throws Token\InvalidAuthorizationCodeException
	 */
	protected function verifyRequest()
	{
		$code = $this->input->getParameter('code');

		$entity = $this->token->getToken(IToken::AUTHORIZATION_CODE)->getEntity($code);
		$this->scope = $entity->getScope();

		$this->token->getToken(IToken::AUTHORIZATION_CODE)->getStorage()->remove($code);
	}

	/**
	 * Generate access token
	 * @return string
	 */
	protected function generateAccessToken()
	{
		$client = $this->getClient();
		$accessTokenStorage = $this->token->getToken(IToken::ACCESS_TOKEN);
		$refreshTokenStorage = $this->token->getToken(IToken::REFRESH_TOKEN);

		$accessToken = $accessTokenStorage->create($client, $this->user->getId(), $this->getScope());
		$refreshToken = $refreshTokenStorage->create($client, $this->user->getId(), $this->getScope());

		return array(
			'access_token' => $accessToken->getAccessToken(),
			'token_type' => 'bearer',
			'expires_in' => $accessTokenStorage->getLifetime(),
			'refresh_token' => $refreshToken->getRefreshToken()
		);
	}

}