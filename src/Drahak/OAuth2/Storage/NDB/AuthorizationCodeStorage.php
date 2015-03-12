<?php
namespace Drahak\OAuth2\Storage\NDB;

use Drahak\OAuth2\InvalidScopeException;
use Drahak\OAuth2\Storage\AuthorizationCodes\AuthorizationCode;
use Drahak\OAuth2\Storage\AuthorizationCodes\IAuthorizationCodeStorage;
use Drahak\OAuth2\Storage\AuthorizationCodes\IAuthorizationCode;
use Nette\Database\Context;
use Nette\Database\SelectionFactory;
use Nette\Database\SqlLiteral;
use Nette\Database\Table\ActiveRow;
use Nette\Object;

/**
 * AuthorizationCode
 * @package Drahak\OAuth2\Storage\AuthorizationCodes
 * @author Drahomír Hanák
 */
class AuthorizationCodeStorage extends Object implements IAuthorizationCodeStorage
{

	/** @var Context */
	private $db;

	public function __construct(Context $selectionFactory)
	{
		$this->db = $selectionFactory;
	}

	/**
	 * Get authorization code table
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		return $this->db->table('oauth_authorization_code');
	}

	/**
	 * Get scope table
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getScopeTable()
	{
		return $this->db->table('oauth_authorization_code_scope');
	}

	/******************** IAuthorizationCodeStorage ********************/

	/**
	 * Store authorization code
	 * @param IAuthorizationCode $authorizationCode
	 * @throws InvalidScopeException
	 */
	public function store(IAuthorizationCode $authorizationCode)
	{

		$this->getTable()->insert(array(
			'authorization_code' => $authorizationCode->getAuthorizationCode(),
			'client_id' => $authorizationCode->getClientId(),
			'user_id' => $authorizationCode->getUserId(),
			'expires_at' => $authorizationCode->getExpires()
		));

		$connection = $this->getTable()->getConnection();
		$connection->beginTransaction();
		try {
			foreach ($authorizationCode->getScope() as $scope) {
				$this->getScopeTable()->insert(array(
					'authorization_code' => $authorizationCode->getAuthorizationCode(),
					'scope_name' => $scope
				));
			}
		} catch (\PDOException $e) {
			// MySQL error 1452 - Cannot add or update a child row: a foreign key constraint fails
			if (in_array(1452, $e->errorInfo)) {
				throw new InvalidScopeException;
			}
			throw $e;
		}
		$connection->commit();
	}

	/**
	 * Remove authorization code
	 * @param string $authorizationCode
	 * @return void
	 */
	public function remove($authorizationCode)
	{
		$this->getTable()->where(array('authorization_code' => $authorizationCode))->delete();
	}

	/**
	 * Validate authorization code
	 * @param string $authorizationCode
	 * @return IAuthorizationCode
	 */
	public function getValidAuthorizationCode($authorizationCode)
	{
		/** @var ActiveRow $row */
		$row = $this->getTable()
			->where(array('authorization_code' => $authorizationCode))
			->where(new SqlLiteral('TIMEDIFF(expires_at, NOW()) >= 0'))
			->fetch();

		if (!$row) return NULL;

		$scopes = $this->getScopeTable()
			->where(array('authorization_code' => $authorizationCode))
			->fetchPairs('scope_name');

		return new AuthorizationCode(
			$row['authorization_code'],
			new \DateTime($row['expires_at']),
			$row['client_id'],
			$row['user_id'],
			array_keys($scopes)
		);
	}


}