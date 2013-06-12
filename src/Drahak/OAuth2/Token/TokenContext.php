<?php
namespace Drahak\OAuth2\Token;

use Drahak\OAuth2\InvalidStateException;
use Nette\Object;

/**
 * TokenContext
 * @package Drahak\OAuth2\Token
 * @author Drahomír Hanák
 */
class TokenContext extends Object
{

	/** @var array */
	private $tokens = array();

	/**
	 * Add identifier to collection
	 * @param IToken $token
	 */
	public function addToken(IToken $token)
	{
		$this->tokens[$token->getIdentifier()] = $token;
	}

	/**
	 * Get token
	 * @param string $identifier
	 * @return IToken
	 *
	 * @throws InvalidStateException
	 */
	public function getToken($identifier)
	{
		if 	(!isset($this->tokens[$identifier])) {
			throw new InvalidStateException('Token called "' . $identifier . '" not found in Token context');
		}

		return $this->tokens[$identifier];
	}

}