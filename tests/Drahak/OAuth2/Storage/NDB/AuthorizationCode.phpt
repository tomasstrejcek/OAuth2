<?php
namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';

use Drahak\OAuth2\Storage\AuthorizationCodes\IAuthorizationCode;
use Drahak\OAuth2\Storage\NDB\AuthorizationCodeStorage;
use Mockista\MockInterface;
use Nette;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\NDB\AuthorizationCode.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\NDB\AuthorizationCodeTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\NDB
 */
class AuthorizationCodeTest extends TestCase
{

	/** @var MockInterface */
	private $selectionFactory;

	/** @var AuthorizationCodeStorage */
	private $storage;

	protected function setUp()
	{
		parent::setUp();
		$this->selectionFactory = $this->mockista->create('Nette\Database\SelectionFactory');
		$this->storage = new AuthorizationCodeStorage($this->selectionFactory);
	}

	public function testStoreNewAuthorizationCode()
	{
		$data = array(
			'authorization_code' => 'adfs54s8br48nfd8h7t4m8',
			'client_id' => 1,
			'user_id' => 5,
			'expires' => new \DateTime
		);

		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$scopeSelection = $this->mockista->create('Nette\Database\Table\Selection');
		$connection = $this->mockista->create('Nette\Database\Connection');
		$entity = $this->mockista->create('Drahak\OAuth2\Storage\AuthorizationCodes\IAuthorizationCode');

		$this->selectionFactory->expects('table')->once()->with('oauth_authorization_code')->andReturn($selection);
		$this->selectionFactory->expects('table')->once()->with('oauth_authorization_code_scope')->andReturn($scopeSelection);

		$entity->expects('getAuthorizationCode')->once()->andReturn($data['authorization_code']);
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
				'authorization_code' => $data['authorization_code'],
				'scope_name' => 'profile'
			));

		$this->storage->store($entity);
	}

	public function testThrowsExceptionWhenInvalidScopeIsGiven()
	{
		$data = array(
			'authorization_code' => 'adfs54s8br48nfd8h7t4m8',
			'client_id' => 1,
			'user_id' => 5,
			'expires' => new \DateTime
		);

		$exception = new \PDOException();
		$exception->errorInfo = array(1452);

		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$scopeSelection = $this->mockista->create('Nette\Database\Table\Selection');
		$connection = $this->mockista->create('Nette\Database\Connection');
		$entity = $this->mockista->create('Drahak\OAuth2\Storage\AuthorizationCodes\IAuthorizationCode');

		$this->selectionFactory->expects('table')->once()->with('oauth_authorization_code')->andReturn($selection);
		$this->selectionFactory->expects('table')->once()->with('oauth_authorization_code_scope')->andReturn($scopeSelection);

		$entity->expects('getAuthorizationCode')->once()->andReturn($data['authorization_code']);
		$entity->expects('getClientId')->once()->andReturn($data['client_id']);
		$entity->expects('getUserId')->once()->andReturn($data['user_id']);
		$entity->expects('getExpires')->once()->andReturn($data['expires']);
		$entity->expects('getScope')->once()->andReturn(array('profile'));

		$selection->expects('insert')->once()->with($data);
		$selection->expects('getConnection')->once()->andReturn($connection);

		$connection->expects('beginTransaction')->once();

		$scopeSelection->expects('insert')
			->once()
			->with(array(
				'authorization_code' => $data['authorization_code'],
				'scope_name' => 'profile'
			))
			->andThrow($exception);

		$connection->expects('beginTransaction')->once();

		Assert::throws(function() use($entity) {
			$this->storage->store($entity);
		}, 'Drahak\OAuth2\InvalidScopeException');
	}

	public function testRemoveAuthorizationCode()
	{
		$token = '86af28s2d8g4re84fmh9gy6s8';
		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$selection->expects('where')->once()->with(array('authorization_code' => $token))->andReturn($selection);
		$selection->expects('delete')->once()->andReturn($selection);

		$this->selectionFactory->expects('table')->once()->with('oauth_authorization_code')->andReturn($selection);

		$this->storage->remove($token);
	}

	public function testValidateAuthorizationCode()
	{
		$token = '86af28s2d8g4re84fmh9gy6s8';
		$row = array('profile' => 'asd5asd5a6as');
		$authorizationCode = array(
			'authorization_code' => $token,
			'expires' => '26.2.2002 15:23',
			'client_id' => 1,
			'user_id' => 2
		);

		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$selection->expects('where')->twice()->with(array('authorization_code' => $token))->andReturn($selection);
		$selection->expects('where')->once()->andReturn($selection);
		$selection->expects('fetch')->once()->andReturn($authorizationCode);

		$selection->expects('select')->once()->with('scope_name')->andReturn($selection);
		$selection->expects('fetchPairs')->once()->with('scope_name')->andReturn($row);

		$this->selectionFactory->expects('table')->once()->with('oauth_authorization_code')->andReturn($selection);
		$this->selectionFactory->expects('table')->once()->with('oauth_authorization_code_scope')->andReturn($selection);

		$result = $this->storage->getValidAuthorizationCode($token);

		Assert::true($result instanceof IAuthorizationCode);
		Assert::equal($result->getAuthorizationCode(), $authorizationCode['authorization_code']);
		Assert::equal($result->getClientId(), $authorizationCode['client_id']);
		Assert::equal($result->getUserId(), $authorizationCode['user_id']);
		Assert::equal($result->getScope(), array_keys($row));
	}

}
\run(new AuthorizationCodeTest());