<?php
class Neuron_Auth_OAuthStore
{
	public static function getStore ()
	{
		return OAuthStore::instance('MySQLi', array ('conn' => Neuron_DB_Database::getInstance ()->getConnection ()));
	}
}