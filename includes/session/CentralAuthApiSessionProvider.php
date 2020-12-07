<?php

use MediaWiki\Session\SessionInfo;

/**
 * Session provider for CentralAuth API centralauthtoken
 *
 * This session provider looks for the 'centralauthtoken' request parameter,
 * and checks that it corresponds to an existing token set up by
 * ApiCentralAuthToken. If the parameter is present but invalid, it returns a
 * bogus SessionInfo and hooks ApiBeforeMain to throw an appropriate exception
 * later when MediaWiki is ready to handle it.
 */
class CentralAuthApiSessionProvider extends CentralAuthTokenSessionProvider {

	public function __construct() {
		global $wgHooks;

		parent::__construct();

		$wgHooks['APIGetAllowedParams'][] = $this;
	}

	/**
	 * @param WebRequest $request
	 *
	 * @return string|null
	 */
	protected function getTokenFromRequest( WebRequest $request ) {
		// Only relevant in the API
		if ( !defined( 'MW_API' ) ) {
			return null;
		}

		return $request->getVal( 'centralauthtoken' );
	}

	/**
	 * Throw an exception, later
	 *
	 * @param string $code Error code
	 * @param string|array $error Error message key, or key+parameters
	 * @return SessionInfo
	 */
	protected function makeBogusSessionInfo( $code, $error ) {
		global $wgHooks;

		// Schedule the throwing of the exception for later when the API
		// is ready to catch it.
		$exception = \ApiUsageException::newWithMessage( null, $error, $code );
		$wgHooks['ApiBeforeMain'][] = function () use ( $exception ) {
			throw $exception;
		};

		return parent::makeBogusSessionInfo( $code, $error );
	}

	public function provideSessionInfo( WebRequest $request ) {
		// Only relevant in the API
		if ( !defined( 'MW_API' ) ) {
			return null;
		}

		return parent::provideSessionInfo( $request );
	}

	/**
	 * Overridden to defer actual consumption of the token until the ApiCheckCanExecute
	 * hook is called.
	 *
	 * @param string $token
	 *
	 * @return bool
	 */
	protected function consumeToken( $token ) {
		global $wgHooks;

		// Delete the token once it's actually used
		$wgHooks['ApiCheckCanExecute'][] = $this;
		return true;
	}

	/**
	 * Inject the "centralauthtoken" parameter into the API
	 * @param ApiBase &$module API module
	 * @param array &$params Array of parameter specifications
	 * @param int $flags
	 * @return bool
	 */
	public function onAPIGetAllowedParams( &$module, &$params, $flags ) {
		global $wgCentralAuthCookies;
		if ( !$wgCentralAuthCookies ) {
			return true;
		}

		if ( $module instanceof ApiMain ) {
			$params['centralauthtoken'] = [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_SENSITIVE => true,
			];
		}
		return true;
	}

	/**
	 * Consume the centralauthtoken
	 * @param ApiBase $module
	 * @param User $user
	 * @param array &$message Error message key and params
	 * @return bool
	 */
	public function onApiCheckCanExecute( $module, $user, &$message ) {
		$token = $module->getMain()->getVal( 'centralauthtoken' ); # Mark used

		// NOTE: Call parent, because we override consumeToken() in this class
		//       to operate indirectly via this hook.
		if ( !parent::consumeToken( $token ) ) {
			// Raced out trying to mark the token as expired
			$message = [ 'badtoken', 'apierror-centralauth-badtoken' ];

			return false;
		}

		return true;
	}

}
