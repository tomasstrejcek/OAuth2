<?php
namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../../../DatabaseTestCase.php';

use Drahak\OAuth2\Grant\IGrant;
use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage\NDB\ClientStorage;
use Nette;
use Tester;
use Tester\Assert;
use Tests\DatabaseTestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\NDB\ClientStorage.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\NDB\ClientStorageTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\NDB
 */
class ClientStorageTest extends DatabaseTestCase
{
	/** @var ClientStorage */
	private $storage;
    
    protected function setUp()
    {
		parent::setUp();
		$this->storage = new ClientStorage($this->selectionFactory);
	}

	public function testGetCientByIdAndSecret()
	{
		$id = 'd3a213ad-d142-11';
		$secret = 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12';

		$client = $this->storage->getClient($id, $secret);
		Assert::true($client instanceof IClient);
		Assert::equal($client->getId(), $id);
		Assert::equal($client->getSecret(), $secret);
	}

	public function testWheneverIsUserAllowedToUseGrantType()
	{
		$id = 'd3a213ad-d142-11';

		$canUseGrant = $this->storage->canUseGrantType($id, IGrant::CLIENT_CREDENTIALS);
		Assert::true($canUseGrant);
	}

	public function testUserIsNotAllowedToUseGrantType()
	{
		$id = 'd3a213ad-d142-11';

		$canUseGrant = $this->storage->canUseGrantType($id, 'test_credentials');
		Assert::false($canUseGrant);
	}

}
\run(new ClientStorageTest());