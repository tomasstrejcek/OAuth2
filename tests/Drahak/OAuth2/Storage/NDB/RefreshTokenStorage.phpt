<?php
namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';

use Drahak\OAuth2\Storage\NDB\RefreshTokenStorage;
use Drahak\OAuth2\Storage\RefreshTokens\IRefreshToken;
use Mockista\MockInterface;
use Nette;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\NDB\RefreshTokenStorage.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\NDB\RefreshTokenStorageTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\NDB
 */
class RefreshTokenStorageTest extends TestCase
{

	/** @var MockInterface */
	private $selectionFactory;

	/** @var RefreshTokenStorage */
	private $storage;

	protected function setUp()
	{
		parent::setUp();
		$this->selectionFactory = $this->mockista->create('Nette\Database\SelectionFactory');
		$this->storage = new RefreshTokenStorage($this->selectionFactory);
	}

	public function testStoreNewRefreshToken()
	{
		$data = array(
			'refresh_token' => 'adfs54s8br48nfd8h7t4m8',
			'client_id' => 1,
			'user_id' => 5,
			'expires' => new \DateTime
		);

		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$entity = $this->mockista->create('Drahak\OAuth2\Storage\RefreshTokens\IRefreshToken');

		$this->selectionFactory->expects('table')->once()->with('oauth_refresh_token')->andReturn($selection);

		$entity->expects('getRefreshToken')->once()->andReturn($data['refresh_token']);
		$entity->expects('getClientId')->once()->andReturn($data['client_id']);
		$entity->expects('getUserId')->once()->andReturn($data['user_id']);
		$entity->expects('getExpires')->once()->andReturn($data['expires']);

		$selection->expects('insert')->once()->with($data);

		$this->storage->store($entity);
	}

	public function testRemoveRefreshToken()
	{
		$token = '86af28s2d8g4re84fmh9gy6s8';
		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$selection->expects('where')->once()->with(array('refresh_token' => $token))->andReturn($selection);
		$selection->expects('delete')->once()->andReturn($selection);

		$this->selectionFactory->expects('table')->once()->with('oauth_refresh_token')->andReturn($selection);

		$this->storage->remove($token);
	}

	public function testValidateRefreshToken()
	{
		$token = '86af28s2d8g4re84fmh9gy6s8';
		$row = array('refresh_token' => $token, 'expires' => '1.1.1996', 'client_id' => 1, 'user_id' => 5);
		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$selection->expects('where')->once()->with(array('refresh_token' => $token))->andReturn($selection);
		$selection->expects('where')->once()->andReturn($selection);
		$selection->expects('fetch')->once()->andReturn($row);

		$this->selectionFactory->expects('table')->once()->with('oauth_refresh_token')->andReturn($selection);

		$result = $this->storage->getValidRefreshToken($token);
		Assert::true($result instanceof IRefreshToken);
		Assert::equal($result->getRefreshToken(), $token);
		Assert::equal($result->getClientId(), 1);
		Assert::equal($result->getUserId(), 5);
	}

}
\run(new RefreshTokenStorageTest());