<?php
namespace Drahak\OAuth2\Token;

use Drahak\OAuth2\NotLoggedInException;
use Drahak\OAuth2\Storage;
use Drahak\OAuth2\IKeyGenerator;
use Drahak\OAuth2\Storage\AccessTokens\IAccessTokenStorage;
use Drahak\OAuth2\Storage\Clients\IClient;
use Nette\Object;
use Nette\Security\User;

/**
 * AccessToken
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 *
 * @property-read int $lifetime
 * @property-read IAccessTokenStorage $storage
 */
class AccessToken extends Object implements IToken
{

	/** @var IAccessTokenStorage */
	private $storage;

	/** @var IKeyGenerator */
	private $keyGenerator;

	/** @var int */
	private $lifetime;

	public function __construct($lifetime, IKeyGenerator $keyGenerator, IAccessTokenStorage $accessToken)
	{
		$this->lifetime = $lifetime;
		$this->keyGenerator = $keyGenerator;
		$this->storage = $accessToken;
	}

	/**
	 * Create access token
	 * @param Storage\Clients\IClient $client
	 * @param string|int $userId
	 * @param array $scope
	 * @return Storage\AccessTokens\AccessToken
	 */
	public function create(IClient $client, $userId, array $scope = array())
	{
		$accessExpires = new \DateTime;
		$accessExpires->modify('+' . $this->lifetime . ' seconds');

		$accessToken = new Storage\AccessTokens\AccessToken(
			$this->keyGenerator->generate(),
			$accessExpires,
			$client->getId(),
			$userId,
			$scope
		);
		$this->storage->store($accessToken);

		return $accessToken;
	}

	/**
	 * Check access token
	 * @param string $accessToken
	 * @return Storage\AccessTokens\IAccessToken|NULL
	 * @throws InvalidAccessTokenException
	 */
	public function getEntity($accessToken)
	{
		$entity = $this->storage->getValidAccessToken($accessToken);
		if (!$entity) {
			$this->storage->remove($accessToken);
			throw new InvalidAccessTokenException;
		}
		return $entity;
	}

	/**
	 * Get token identifier name
	 * @return string
	 */
	public function getIdentifier()
	{
		return self::ACCESS_TOKEN;
	}


	/******************** Getters & setters ********************/

	/**
	 * Returns access token lifetime
	 * @return int
	 */
	public function getLifetime()
	{
		return $this->lifetime;
	}

	/**
	 * Get access token storage
	 * @return IAccessTokenStorage
	 */
	public function getStorage()
	{
		return $this->storage;
	}

}