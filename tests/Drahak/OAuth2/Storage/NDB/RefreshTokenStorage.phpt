<?php
namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../DatabaseTestCase.php';

use Drahak\OAuth2\Storage\NDB\RefreshTokenStorage;
use Drahak\OAuth2\Storage\RefreshTokens\IRefreshToken;
use Drahak\OAuth2\Storage\RefreshTokens\RefreshToken;
use Nette;
use Tester;
use Tester\Assert;
use Tests\DatabaseTestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\NDB\RefreshTokenStorage.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\NDB\RefreshTokenStorageTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\NDB
 */
class RefreshTokenStorageTest extends DatabaseTestCase
{

	/** @var RefreshTokenStorage */
	private $storage;

	protected function setUp()
	{
		parent::setUp();
		$this->storage = new RefreshTokenStorage($this->selectionFactory);
	}

	public function testCreateRefreshToken()
	{
		$entity = $this->createEntity();
		$this->storage->store($entity);

		$stored = $this->storage->getValidRefreshToken($entity->getRefreshToken());
		Assert::true($stored instanceof IRefreshToken);
	}

	public function testRemoveRefreshToken()
	{
		$entity = $this->createEntity();
		$this->storage->store($entity);
		$this->storage->remove($entity->getRefreshToken());
		$stored = $this->storage->getValidRefreshToken($entity->getRefreshToken());
		Assert::null($stored);
	}

	public function testGetValidRefreshToken()
	{
		$entity = $this->createEntity('5fcb1af9-d5cd-11');
		$this->storage->store($entity);
		$stored = $this->storage->getValidRefreshToken($entity->getRefreshToken());
		Assert::true($stored instanceof IRefreshToken);
		Assert::equal($stored->getRefreshToken(), $entity->getRefreshToken());
		Assert::equal($stored->getClientId(), $entity->getClientId());
		Assert::equal($stored->getUserId(), $entity->getUserId());
	}

	/**
	 * Create test entity
	 * @param string|null $userId
	 * @return RefreshToken
	 */
	protected function createEntity($userId = NULL)
	{
		return new RefreshToken(
			hash('sha256', Nette\Utils\Strings::random()),
			new \DateTime('20.1.2050'),
			'd3a213ad-d142-11',
			$userId
		);
	}

}
\run(new RefreshTokenStorageTest());