<?php
namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';

use Drahak\OAuth2\Storage\AccessTokens\IAccessToken;
use Drahak\OAuth2\Storage\NDB\AccessTokenStorage;
use Mockista\MockInterface;
use Nette;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\NDB\AccessTokenStorage.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\NDB\AccessTokenStorageTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\NDB
 */
class AccessTokenStorageTest extends TestCase
{

	/** @var MockInterface */
	private $selectionFactory;

	/** @var AccessTokenStorage */
	private $storage;

    protected function setUp()
    {
		parent::setUp();
		$this->selectionFactory = $this->mockista->create('Nette\Database\SelectionFactory');
		$this->storage = new AccessTokenStorage($this->selectionFactory);
    }
    
    public function testStoreNewAccessToken()
    {
		$data = array(
			'access_token' => 'adfs54s8br48nfd8h7t4m8',
			'client_id' => 1,
			'user_id' => 5,
			'expires' => new \DateTime
		);

		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$scopeSelection = $this->mockista->create('Nette\Database\Table\Selection');
		$connection = $this->mockista->create('Nette\Database\Connection');
		$entity = $this->mockista->create('Drahak\OAuth2\Storage\AccessTokens\IAccessToken');

		$this->selectionFactory->expects('table')->once()->with('oauth_access_token')->andReturn($selection);
		$this->selectionFactory->expects('table')->once()->with('oauth_access_token_scope')->andReturn($scopeSelection);

		$entity->expects('getAccessToken')->once()->andReturn($data['access_token']);
		$entity->expects('getClientId')->once()->andReturn($data['client_id']);
		$entity->expects('getUserId')->once()->andReturn($data['user_id']);
		$entity->expects('getExpires')->once()->andReturn($data['expires']);
		$entity->expects('getScope')->once()->andReturn(array('profile'));

		$selection->expects('insert')->once()->with($data);
		$selection->expects('getConnection')->once()->andReturn($connection);

		$connection->expects('beginTransaction')->once();
		$connection->expects('commit')->once();

		$scopeSelection->expects('insert')
			->once()
			->with(array(
				'access_token' => $data['access_token'],
				'scope_name' => 'profile'
			));

		$this->storage->store($entity);
    }

	public function testThrowsExceptionWhenInvalidScopeIsGiven()
	{
		$data = array(
			'access_token' => 'adfs54s8br48nfd8h7t4m8',
			'client_id' => 1,
			'user_id' => 5,
			'expires' => new \DateTime
		);

		$exception = new \PDOException();
		$exception->errorInfo = array(1452);

		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$scopeSelection = $this->mockista->create('Nette\Database\Table\Selection');
		$connection = $this->mockista->create('Nette\Database\Connection');
		$entity = $this->mockista->create('Drahak\OAuth2\Storage\AccessTokens\IAccessToken');

		$this->selectionFactory->expects('table')->once()->with('oauth_access_token')->andReturn($selection);
		$this->selectionFactory->expects('table')->once()->with('oauth_access_token_scope')->andReturn($scopeSelection);

		$entity->expects('getAccessToken')->once()->andReturn($data['access_token']);
		$entity->expects('getClientId')->once()->andReturn($data['client_id']);
		$entity->expects('getUserId')->once()->andReturn($data['user_id']);
		$entity->expects('getExpires')->once()->andReturn($data['expires']);
		$entity->expects('getScope')->once()->andReturn(array('profile'));

		$selection->expects('insert')->once()->with($data);
		$selection->expects('getConnection')->once()->andReturn($connection);

		$scopeSelection->expects('insert')
			->once()
			->with(array(
				'access_token' => $data['access_token'],
				'scope_name' => 'profile'
			))
			->andThrow($exception);

		$connection->expects('beginTransaction')->once();

		Assert::throws(function() use($entity) {
			$this->storage->store($entity);
		}, 'Drahak\OAuth2\InvalidScopeException');
	}

	public function testRemoveAccessToken()
	{
		$token = '86af28s2d8g4re84fmh9gy6s8';
		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$selection->expects('where')->once()->with(array('access_token' => $token))->andReturn($selection);
		$selection->expects('delete')->once()->andReturn($selection);

		$this->selectionFactory->expects('table')->once()->with('oauth_access_token')->andReturn($selection);

		$this->storage->remove($token);
	}

	public function testValidateAccessToken()
	{
		$token = '86af28s2d8g4re84fmh9gy6s8';
		$row = array('profile' => 'asd5asd5a6as');
		$accessToken = array(
			'access_token' => $token,
			'expires' => '26.2.2002 15:23',
			'client_id' => 1,
			'user_id' => 2
		);

		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$selection->expects('where')->once()->with(array('access_token' => $token))->andReturn($selection);
		$selection->expects('where')->once()->andReturn($selection);
		$selection->expects('fetch')->once()->andReturn($accessToken);

		$selection->expects('select')->once()->with('scope_name')->andReturn($selection);
		$selection->expects('fetchPairs')->once()->with('scope_name')->andReturn($row);

		$this->selectionFactory->expects('table')->once()->with('oauth_access_token')->andReturn($selection);
		$this->selectionFactory->expects('table')->once()->with('oauth_access_token_scope')->andReturn($selection);

		$result = $this->storage->getValidAccessToken($token);
		Assert::true($result instanceof IAccessToken);
		Assert::equal($result->getAccessToken(), $accessToken['access_token']);
		Assert::equal($result->getClientId(), $accessToken['client_id']);
		Assert::equal($result->getUserId(), $accessToken['user_id']);
		Assert::equal($result->getScope(), array_keys($row));
	}

}
\run(new AccessTokenStorageTest());