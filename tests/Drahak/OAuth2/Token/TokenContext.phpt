<?php
namespace Tests\Drahak\OAuth2\Token;

require_once __DIR__ . '/../../bootstrap.php';

use Drahak\OAuth2\Token\TokenContext;
use Mockista\MockInterface;
use Nette;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Drahak\OAuth2\Token\TokenContext.
 *
 * @testCase Tests\Drahak\OAuth2\Token\TokenContextTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Token
 */
class TokenContextTest extends TestCase
{

	/** @var MockInterface */
	private $token;

	/** @var TokenContext */
	private $context;

    protected function setUp()
    {
		parent::setUp();
		$this->token = $this->mockista->create('Drahak\OAuth2\Token\IToken');
		$this->context = new TokenContext;
    }
    
    public function testGetInvalidToken()
    {
		Assert::throws(function() {
			$this->context->getToken('totaly doesn\'t exist');
		}, 'Drahak\OAuth2\InvalidStateException');
    }

	public function testAddToken()
	{
		$this->token->expects('getIdentifier')->once()->andReturn('secured_token');
		$this->context->addToken($this->token);

		Assert::same($this->context->getToken('secured_token'), $this->token);
	}

}
\run(new TokenContextTest());