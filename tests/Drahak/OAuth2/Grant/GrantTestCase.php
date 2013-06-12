<?php
namespace Tests\Drahak\OAuth2\Grant;

use Mockista\MockInterface;
use Tests\TestCase;

/**
 * GrantTestCase
 * @package Tests\Drahak\OAuth2\Grant
 * @author Drahomír Hanák
 */
abstract class GrantTestCase extends TestCase
{

	/** @var MockInterface */
	protected $client;

	/** @var MockInterface */
	protected $clientEntity;

	/** @var MockInterface */
	protected $accessTokenEntity;

	/** @var MockInterface */
	protected $refreshTokenEntity;

	/** @var MockInterface */
	protected $accessToken;

	/** @var MockInterface */
	protected $refreshToken;

	/** @var MockInterface */
	protected $authorizationCode;

	/** @var MockInterface */
	protected $user;

	/** @var MockInterface */
	protected $token;

	/** @var MockInterface */
	protected $input;

	protected function setUp()
	{
		parent::setUp();
		$this->client = $this->mockista->create('Drahak\OAuth2\Storage\Clients\IClientStorage');
		$this->clientEntity = $this->mockista->create('Drahak\OAuth2\Storage\Clients\IClient');
		$this->accessTokenEntity = $this->mockista->create('Drahak\OAuth2\Storage\AccessTokens\IAccessToken');
		$this->refreshTokenEntity = $this->mockista->create('Drahak\OAuth2\Storage\RefreshTokens\IRefreshToken');
		$this->accessToken = $this->mockista->create('Drahak\OAuth2\Token\AccessToken');
		$this->refreshToken = $this->mockista->create('Drahak\OAuth2\Token\RefreshToken');
		$this->authorizationCode = $this->mockista->create('Drahak\OAuth2\Token\AuthorizationCode');
		$this->token = $this->mockista->create('Drahak\OAuth2\Token\TokenContext');
		$this->input = $this->mockista->create('Drahak\OAuth2\Http\IInput');
		$this->user = $this->mockista->create('Nette\Security\User');
	}

	/**
	 * Mock input data
	 * @param array $expectedData
	 */
	protected function createInputMock(array $expectedData)
	{
		foreach ($expectedData as $key => $value) {
			$this->input->expects('getParameter')
				->once()
				->with($key)
				->andReturn($value);
		}
	}

	/**
	 * Create tokens mocks
	 * @param array $mocks identifier => MockInterface
	 */
	protected function createTokenMocks(array $mocks)
	{
		foreach ($mocks as $identifier => $mock) {
			$this->token->expects('getToken')
				->atLeastOnce()
				->with($identifier)
				->andReturn($mock);
		}
	}

}