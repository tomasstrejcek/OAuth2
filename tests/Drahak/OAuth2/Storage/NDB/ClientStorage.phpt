<?php
namespace Tests\Drahak\OAuth2\Storage\NDB;

require_once __DIR__ . '/../../../bootstrap.php';

use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage\NDB\ClientStorage;
use Mockista\MockInterface;
use Nette;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\NDB\ClientStorage.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\NDB\ClientStorageTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\NDB
 */
class ClientStorageTest extends TestCase
{

	/** @var MockInterface */
	private $selectionFactory;

	/** @var ClientStorage */
	private $storage;
    
    protected function setUp()
    {
		parent::setUp();
		$this->selectionFactory = $this->mockista->create('Nette\Database\SelectionFactory');
		$this->storage = new ClientStorage($this->selectionFactory);
    }
    
    public function testGetClientByIdAndSecret()
    {
		$params = array('id' => 1, 'secret' => '54da65adad9');
		$rowData = $params + array('redirect_url' => 'httl://localhost/');

		$selection = $this->mockista->create('Nette\Database\Table\Selection');
		$selection->expects('where')->once()->with(array('id' => $params['id']))->andReturn($selection);
		$selection->expects('where')->once()->with(array('secret' => $params['secret']))->andReturn($selection);
		$selection->expects('fetch')->once()->andReturn($rowData);

		$this->selectionFactory->expects('table')->once()->with('oauth_client')->andReturn($selection);

		$client = $this->storage->getClient($params['id'], $params['secret']);
		Assert::true($client instanceof IClient);
		Assert::equal($client->getId(), $rowData['id']);
		Assert::equal($client->getSecret(), $rowData['secret']);
		Assert::equal($client->getRedirectUrl(), $rowData['redirect_url']);
    }

}
\run(new ClientStorageTest());