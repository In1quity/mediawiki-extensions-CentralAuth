<?php

use MediaWiki\MediaWikiServices;

return [
	'CentralAuth.CentralAuthServices' => function ( MediaWikiServices $services ) : CentralAuthServices {
		return new CentralAuthServices(
			$services->getDBLoadBalancerFactory(),
			$services->getReadOnlyMode(),
			$services->getMainConfig(),
			$services->getAuthManager(),
			$services->getUserFactory(),
			$services->getPermissionManager(),
			$services->getStatsdDataFactory(),
			$services->getTitleFactory(),
			$services->getHookContainer()
		);
	},
];
