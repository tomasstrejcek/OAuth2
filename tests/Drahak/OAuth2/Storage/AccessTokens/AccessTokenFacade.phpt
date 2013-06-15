<?php
namespace Tests\Drahak\OAuth2\Storage\AccessTokens;

require_once __DIR__ . '/../../../bootstrap.php';

use Drahak\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Mockista\MockInterface;
use Nette;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Drahak\OAuth2\Storage\AccessTokens\AccessToken.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\AccessTokens\AccessTokenFacadeTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\AccessTokens
 */
class AccessTokenFacadeTest extends TestCase
{

	/** @var MockInterface */
	private $storage;

	/** @var MockInterface */
	private $keyGenerator;

	/** @var AccessTokenFacade */
	private $token;

    protected function setUp()
    {
		parent::setUp();
		$this->storage = $this->mockista->create('Drahak\OAuth2\Storage\AccessTokens\IAccessTokenStorage');
		$this->keyGenerator = $this->mockista->create('Drahak\OAuth2\IKeyGenerator');
		$this->token = new AccessTokenFacade(3600, $this->keyGenerator, $this->storage);
    }
    
    public function testCheckInvalidToken()
    {
		$token = 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12';
		$this->storage->expects('getValidAccessToken')->once()->with($token)->andReturn(FALSE);
		$this->storage->expects('remove')->once()->with($token);

		Assert::throws(function() use($token) {
			$this->token->getEntity($token);
		}, 'Drahak\OAuth2\Storage\InvalidAccessTokenException');
	}

	public function testValidToken()
	{
		$entity = TRUE;
		$token = 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12';
		$this->storage->expects('getValidAccessToken')->once()->with($token)->andReturn($entity);
		$result = $this->token->getEntity($token);
		Assert::same($result, $entity);
	}

	public function testCreateToken()
	{
		$key = '117936fc44529a174e85ca68005b';
		$scope = array('profile', 'oauth_spec');

		$client = $this->mockista->create('Drahak\OAuth2\Storage\Clients\IClient');
		$client->expects('getId')->once()->andReturn(1);

		$this->keyGenerator->expects('generate')->once()->andReturn($key);

		$this->storage->expects('store')->once();

		$entity = $this->token->create($client, 54, $scope);
		Assert::equal($entity->getAccessToken(), $key);
		Assert::equal($entity->getExpires()->getTimestamp()-time(), 3600);
		Assert::equal($entity->getClientId(), 1);
		Assert::equal($entity->getUserId(), 54);
		Assert::same($entity->getScope(), $scope);
	}

}
\run(new AccessTokenFacadeTest());