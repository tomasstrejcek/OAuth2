<?php
namespace Drahak\OAuth2\Storage\NDB;

use Drahak\OAuth2\Storage\Clients\IClientStorage;
use Drahak\OAuth2\Storage\Clients\IClient;
use Drahak\OAuth2\Storage\Clients\Client;
use Nette\Database\SelectionFactory;
use Nette\Object;

/**
 * Nette database client storage
 * @package Drahak\OAuth2\Storage\Clients
 * @author Drahomír Hanák
 */
class ClientStorage extends Object implements IClientStorage
{

	/** @var SelectionFactory */
	private $selectionFactory;

	public function __construct(SelectionFactory $selectionFactory)
	{
		$this->selectionFactory = $selectionFactory;
	}

	/**
	 * Get client table selection
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		return $this->selectionFactory->table('oauth_client');
	}

	/**
	 * Find client by ID and/or secret key
	 * @param string $clientId
	 * @param string|null $clientSecret
	 * @return IClient
	 */
	public function getClient($clientId, $clientSecret = NULL)
	{
		if (!$clientId) return NULL;

		$selection = $this->getTable()->where(array('id' => $clientId));
		if ($clientSecret) {
			$selection->where(array('secret' => $clientSecret));
		}
		$data = $selection->fetch();
		if (!$data) return NULL;
		return new Client($data['id'], $data['secret'], $data['redirect_url']);
	}


}