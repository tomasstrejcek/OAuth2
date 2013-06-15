<?php
namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../DatabaseTestCase.php';

use Drahak\OAuth2\Storage\AccessTokens\AccessToken;
use Drahak\OAuth2\Storage\AccessTokens\IAccessToken;
use Drahak\OAuth2\Storage\NDB\AccessTokenStorage;
use Nette;
use Tester;
use Tester\Assert;
use Tests\DatabaseTestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\NDB\AccessTokenStorage.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\NDB\AccessTokenStorageTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\NDB
 */
class AccessTokenStorageTest extends DatabaseTestCase
{

	/** @var AccessTokenStorage */
	private $storage;

    protected function setUp()
    {
		parent::setUp();
		$this->storage = new AccessTokenStorage($this->selectionFactory);
    }
    
    public function testCreateAccessToken()
    {
		$entity = $this->createEntity();
		$this->storage->store($entity);

		$stored = $this->storage->getValidAccessToken($entity->getAccessToken());
		Assert::true($stored instanceof IAccessToken);
    }

	public function testRemoveAccessToken()
	{
		$entity = $this->createEntity();
		$this->storage->store($entity);
		$this->storage->remove($entity->getAccessToken());
		$stored = $this->storage->getValidAccessToken($entity->getAccessToken());
		Assert::null($stored);
	}

	public function testThrowsInvalidScopeExceptionWhenInvalidScopeGiven()
	{
		$entity = $this->createEntity('5fcb1af9-d5cd-11', array('invalid_scope_access'));

		Assert::throws(function() use($entity) {
			$this->storage->store($entity);
		}, 'Drahak\OAuth2\InvalidScopeException');
	}

	public function testGetValidAccessTokenEntityWithScope()
	{
		$entity = $this->createEntity('5fcb1af9-d5cd-11', array('profile'));
		$this->storage->store($entity);
		$stored = $this->storage->getValidAccessToken($entity->getAccessToken());
		Assert::true($stored instanceof IAccessToken);
		Assert::equal($stored->getAccessToken(), $entity->getAccessToken());
		Assert::equal($stored->getClientId(), $entity->getClientId());
		Assert::equal($stored->getUserId(), $entity->getUserId());
		Assert::equal($stored->getScope(), $entity->getScope());
	}

	/**
	 * Create test entity
	 * @param string|null $userId
	 * @param array $scope
	 * @return AccessToken
	 */
	protected function createEntity($userId = NULL, $scope = array())
	{
		return new AccessToken(
			hash('sha256', Nette\Utils\Strings::random()),
			new \DateTime('20.1.2050'),
			'd3a213ad-d142-11',
			$userId,
			$scope
		);
	}

}
\run(new AccessTokenStorageTest());