<?php
namespace Tests\Drahak\OAuth2\Storage\RefreshTokens;

require_once __DIR__ . '/../../../bootstrap.php';

use Drahak\OAuth2\Storage\RefreshTokens\RefreshTokenFacade;
use Nette;
use Tester;
use Tester\Assert;
use Tests\TestCase;
use Mockista\MockInterface;

/**
 * Test: Tests\Drahak\OAuth2\Storage\RefreshTokens\RefreshToken.
 *
 * @testCase Tests\Drahak\OAuth2\Storage\RefreshTokens\RefreshTokenFacadeTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Storage\RefreshTokens
 */
class RefreshTokenFacadeTest extends TestCase
{

	/** @var MockInterface */
	private $storage;

	/** @var MockInterface */
	private $keyGenerator;

	/** @var RefreshTokenFacade */
	private $token;

	protected function setUp()
	{
		parent::setUp();
		$this->storage = $this->mockista->create('Drahak\OAuth2\Storage\RefreshTokens\IRefreshTokenStorage');
		$this->keyGenerator = $this->mockista->create('Drahak\OAuth2\IKeyGenerator');
		$this->token = new RefreshTokenFacade(3600, $this->keyGenerator, $this->storage);
	}

	public function testCheckInvalidToken()
	{
		$token = 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12';
		$this->storage->expects('getValidRefreshToken')->once()->with($token)->andReturn(FALSE);
		$this->storage->expects('remove')->once()->with($token);

		Assert::throws(function() use($token) {
			$this->token->getEntity($token);
		}, 'Drahak\OAuth2\Storage\InvalidRefreshTokenException');
	}

	public function testValidToken()
	{
		$entity = TRUE;
		$token = 'a2a2f11ece9c35f117936fc44529a174e85ca68005b7b0d1d0d2b5842d907f12';
		$this->storage->expects('getValidRefreshToken')->once()->with($token)->andReturn($entity);
		$result = $this->token->getEntity($token);
		Assert::same($result, $entity);
	}

	public function testCreateToken()
	{
		$key = '117936fc44529a174e85ca68005b';

		$client = $this->mockista->create('Drahak\OAuth2\Storage\Clients\IClient');
		$client->expects('getId')->once()->andReturn(1);

		$this->keyGenerator->expects('generate')->once()->andReturn($key);

		$this->storage->expects('store')->once();

		$entity = $this->token->create($client, 54, array('don\'t_affect_anything'));
		Assert::equal($entity->getRefreshToken(), $key);
		Assert::equal($entity->getExpires()->getTimestamp()-time(), 3600);
		Assert::equal($entity->getClientId(), 1);
		Assert::equal($entity->getUserId(), 54);
	}

}
\run(new RefreshTokenFacadeTest());