<?php
namespace Drahak\OAuth2\DI;

use Nette\Configurator;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\PhpLiteral;

// Support newer Nette version
if (class_exists('Nette\DI\CompilerExtension')) {
	class_alias('Nette\DI\CompilerExtension', 'Nette\Config\CompilerExtension');
}

/**
 * OAuth2 compiler extension
 * @package Drahak\OAuth2\DI
 * @author Drahomír Hanák
 */
class Extension extends CompilerExtension
{

	/**
	 * Default DI settings
	 * @var array
	 */
	protected $defaults = array(
		'access;TokenLifetime' => 3600, // 1 hour
		'refreshTokenLifetime' => 36000, // 10 hours
		'authorizationCodeLifetime' => 360 // 6 minutes
	);

	/**
	 * Load DI configuration
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		// Library common
		$container->addDefinition($this->prefix('keyGenerator'))
			->setClass('Drahak\OAuth2\KeyGenerator');

		$container->addDefinition($this->prefix('input'))
			->setClass('Drahak\OAuth2\Http\Input');

		// Grant types
		$container->addDefinition($this->prefix('authorizationCodeGrant'))
			->setClass('Drahak\OAuth2\Grant\AuthorizationCode');
		$container->addDefinition($this->prefix('refreshTokenGrant'))
			->setClass('Drahak\OAuth2\Grant\RefreshToken');
		$container->addDefinition($this->prefix('passwordGrant'))
			->setClass('Drahak\OAuth2\Grant\Password');
		$container->addDefinition($this->prefix('implicitGrant'))
			->setClass('Drahak\OAuth2\Grant\Implicit');

		$container->addDefinition($this->prefix('grantContext'))
			->setClass('Drahak\OAuth2\Grant\GrantContext')
			->addSetup('$service->addGrantType(?)', array($this->prefix('@authorizationCodeGrant')))
			->addSetup('$service->addGrantType(?)', array($this->prefix('@refreshTokenGrant')))
			->addSetup('$service->addGrantType(?)', array($this->prefix('@passwordGrant')))
			->addSetup('$service->addGrantType(?)', array($this->prefix('@implicitGrant')));

		// Tokens
		$container->addDefinition($this->prefix('accessToken'))
			->setClass('Drahak\OAuth2\Token\AccessToken')
			->setArguments(array($config['accessTokenLifetime']));
		$container->addDefinition($this->prefix('refreshToken'))
			->setClass('Drahak\OAuth2\Token\RefreshToken')
			->setArguments(array($config['refreshTokenLifetime']));
		$container->addDefinition($this->prefix('authorizationCode'))
			->setClass('Drahak\OAuth2\Token\AuthorizationCode')
			->setArguments(array($config['authorizationCodeLifetime']));

		$container->addDefinition('tokenContext')
			->setClass('Drahak\OAuth2\Token\TokenContext')
			->addSetup('$service->addToken(?)', array($this->prefix('@accessToken')))
			->addSetup('$service->addToken(?)', array($this->prefix('@refreshToken')))
			->addSetup('$service->addToken(?)', array($this->prefix('@authorizationCode')));

		// Storage
		$container->addDefinition($this->prefix('accessTokenStorage'))
			->setClass('Drahak\OAuth2\Storage\NDB\AccessTokenStorage');
		$container->addDefinition($this->prefix('refreshTokenStorage'))
			->setClass('Drahak\OAuth2\Storage\NDB\RefreshTokenStorage');
		$container->addDefinition($this->prefix('authorizationCodeStorage'))
			->setClass('Drahak\OAuth2\Storage\NDB\AuthorizationCodeStorage');
		$container->addDefinition($this->prefix('clientStorage'))
			->setClass('Drahak\OAuth2\Storage\NDB\ClientStorage');
	}

	/**
	 * Register OAuth2 extension
	 * @param Configurator $configurator
	 */
	public static function install(Configurator $configurator)
	{
		$configurator->onCompile[] = function($configurator, $compiler) {
			$compiler->addExtension('oauth2', new Extension);
		};
	}

}