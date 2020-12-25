<?php

use MediaWiki\Session\Session;
use Psr\Container\ContainerInterface;

/**
 * @deprecated since 1.36, use CentralAuthServices instead
 */
class CentralAuthUtils {
	public static function getServicesObject( ContainerInterface $services = null ) : CentralAuthServices {
		return CentralAuthServices::getInstance( $services );
	}

	public static function isReadOnly() {
		return self::getServicesObject()->isReadOnly();
	}

	public static function getReadOnlyReason() {
		return self::getServicesObject()->getReadOnlyReason();
	}

	/**
	 * Wait for the CentralAuth DB replicas to catch up
	 */
	public static function waitForReplicas() {
		self::getServicesObject()->waitForReplicas();
	}

	/**
	 * Gets a master (read/write) database connection to the CentralAuth database
	 *
	 * @return \Wikimedia\Rdbms\IDatabase
	 * @throws CentralAuthReadOnlyError
	 */
	public static function getCentralDB() {
		return self::getServicesObject()->getCentralDB();
	}

	/**
	 * Gets a replica (readonly) database connection to the CentralAuth database
	 *
	 * @return \Wikimedia\Rdbms\IDatabase
	 */
	public static function getCentralReplicaDB() {
		return self::getServicesObject()->getCentralReplicaDB();
	}

	/**
	 * @param WebRequest|null $request
	 */
	public static function setP3P( WebRequest $request = null ) {
		self::getServicesObject()->setP3P( $request );
	}

	/**
	 * @param string ...$args
	 * @return string
	 */
	public static function memcKey( ...$args ) {
		return self::getServicesObject()->memcKey( ...$args );
	}

	/**
	 * Wait for and return the value of a key which is expected to exist from a store
	 *
	 * @param BagOStuff $store
	 * @param string $key A key that will only have one value while it exists
	 * @param int $timeout
	 * @return mixed Key value; false if not found or on error
	 */
	public static function getKeyValueUponExistence( BagOStuff $store, $key, $timeout = 3 ) {
		return self::getServicesObject()->getKeyValueUponExistence( $store, $key, $timeout );
	}

	/**
	 * @return BagOStuff
	 */
	public static function getSessionStore() {
		return self::getServicesObject()->getSessionStore();
	}

	/**
	 * Auto-create a user
	 * @param User $user
	 * @return StatusValue
	 */
	public static function autoCreateUser( User $user ) {
		return self::getServicesObject()->autoCreateUser( $user );
	}

	/**
	 * Get the central session data
	 * @param Session|null $session
	 * @return array
	 */
	public static function getCentralSession( $session = null ) {
		return self::getServicesObject()->getCentralSession( $session );
	}

	/**
	 * Attempt to create a local user for the specified username.
	 * @param string $username
	 * @param User|null $performer
	 * @param string|null $reason
	 * @return Status
	 */
	public static function attemptAutoCreateLocalUserFromName(
		string $username,
		$performer = null,
		$reason = null
	): Status {
		return self::getServicesObject()->attemptAutoCreateLocalUserFromName( $username, $performer, $reason );
	}

	/**
	 * Get the central session data
	 * @param string $id
	 * @return array
	 */
	public static function getCentralSessionById( $id ) {
		return self::getServicesObject()->getCentralSessionById( $id );
	}

	/**
	 * Set data in the central session
	 * @param array $data
	 * @param bool|string $reset Reset the session ID. If a string, this is the new ID.
	 * @param Session|null $session
	 * @return string|null Session ID
	 */
	public static function setCentralSession( array $data, $reset = false, $session = null ) {
		return self::getServicesObject()->setCentralSession( $data, $reset, $session );
	}

	/**
	 * Delete the central session data
	 * @param Session|null $session
	 */
	public static function deleteCentralSession( $session = null ) {
		self::getServicesObject()->deleteCentralSession( $session );
	}

	/**
	 * Sets up jobs to create and attach a local account for the given user on every wiki listed in
	 * $wgCentralAuthAutoCreateWikis.
	 * @param CentralAuthUser $centralUser
	 */
	public static function scheduleCreationJobs( CentralAuthUser $centralUser ) {
		self::getServicesObject()->scheduleCreationJobs( $centralUser );
	}
}
