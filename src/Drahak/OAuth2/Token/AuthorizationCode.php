<?php
namespace Drahak\OAuth2\Token;

use Drahak\OAuth2\Storage;
use Drahak\OAuth2\IKeyGenerator;
use Drahak\OAuth2\Storage\AuthorizationCodes\IAuthorizationCodeStorage;
use Drahak\OAuth2\Storage\Clients\IClient;
use Nette\Object;

/**
 * AuthorizationCode
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
class AuthorizationCode extends Object implements IToken
{

	/** @var int */
	private $lifetime;

	/** @var IAuthorizationCodeStorage */
	private $storage;

	/** @var IKeyGenerator */
	private $keyGenerator;

	public function __construct($lifetime, IKeyGenerator $keyGenerator, IAuthorizationCodeStorage $storage)
	{
		$this->lifetime = $lifetime;
		$this->keyGenerator = $keyGenerator;
		$this->storage = $storage;
	}

	/**
	 * Create authorization code
	 * @param IClient $client
	 * @param string|int $userId
	 * @param array $scope
	 * @return Storage\AuthorizationCodes\AuthorizationCode
	 */
	public function create(IClient $client, $userId, array $scope = array())
	{
		$accessExpires = new \DateTime;
		$accessExpires->modify('+' . $this->lifetime . ' seconds');

		$authorizationCode = new Storage\AuthorizationCodes\AuthorizationCode(
			$this->keyGenerator->generate(),
			$accessExpires,
			$client->getId(),
			$userId,
			$scope
		);
		$this->storage->store($authorizationCode);

		return $authorizationCode;
	}

	/**
	 * Check if token's valid
	 * @param string $token
	 * @throws Storage\AuthorizationCodes\IAuthorizationCode
	 * @throws InvalidAuthorizationCodeException
	 */
	public function getEntity($token)
	{
		$entity = $this->storage->getValidAuthorizationCode($token);
		if (!$entity) {
			$this->storage->remove($token);
			throw new InvalidAuthorizationCodeException;
		}
		return $entity;
	}

	/**
	 * Get token identifier name
	 * @return string
	 */
	public function getIdentifier()
	{
		return self::AUTHORIZATION_CODE;
	}


	/****************** Getters & setters ******************/

	/**
	 * Get token lifetime
	 * @return int
	 */
	public function getLifetime()
	{
		return $this->lifetime;
	}

	/**
	 * Get storage
	 * @return IAuthorizationCodeStorage
	 */
	public function getStorage()
	{
		return $this->storage;
	}

}