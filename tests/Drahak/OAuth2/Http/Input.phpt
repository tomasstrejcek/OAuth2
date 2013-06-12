<?php
namespace Tests\Drahak\OAuth2\Http;

require_once __DIR__ . '/../../bootstrap.php';

use Drahak\OAuth2\Http\Input;
use Mockista\MockInterface;
use Nette;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Drahak\OAuth2\Http\Input.
 *
 * @testCase Tests\Drahak\OAuth2\Http\InputTest
 * @author Drahomír Hanák
 * @package Tests\Drahak\OAuth2\Http
 */
class InputTest extends TestCase
{

	/** @var MockInterface */
	private $request;

	/** @var Input */
	private $input;

    protected function setUp()
    {
		parent::setUp();
		$this->request = $this->mockista->create('Nette\Http\IRequest');
		$this->input = new Input($this->request);
    }
    
    public function testGetParameterByName()
    {
		$this->request->expects('getQuery')->once()->andReturn(NULL);
		$this->request->expects('getPost')->once()->andReturn(array('test' => 'hello'));
		$value = $this->input->getParameter('test');

		Assert::equal($value, 'hello');
    }

	public function testGetAllParameters()
	{
		$input = array('test' => 'hello', 'oauth2' => TRUE);
		$this->request->expects('getQuery')->once()->andReturn(NULL);
		$this->request->expects('getPost')->once()->andReturn($input);
		$params = $this->input->getParameters();
		Assert::equal($input, $params);
	}

	public function testGetAccessToken()
	{
		$token = '546ad8fs8bd18be8tj48ku8yl418uo4vs81515';
		$this->request->expects('getHeader')
			->once()
			->with('Authorization')
			->andReturn('Bearer ' . $token);

		$accessToken = $this->input->getAuthorization();
		Assert::equal($accessToken, $token);
	}

}
\run(new InputTest());