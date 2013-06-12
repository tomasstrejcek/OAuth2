<?php
namespace Drahak\OAuth2\Storage\Clients;

/**
 * Client manager interface
 * @package Drahak\OAuth2\DataSource
 * @author Drahomír Hanák
 */
interface IClientStorage
{

	/**
	 * Get client data
	 * @param string|int $clientId
	 * @param string $clientSecret
	 * @return IClient
	 */
	public function getClient($clientId, $clientSecret = NULL);

}