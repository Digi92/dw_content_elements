<?php

declare(strict_types=1);

/** @var Denkwerk\DwContentElements\Service\InjectorService $injectorService */
$injectorService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \Denkwerk\DwContentElements\Service\InjectorService::class
);
return $injectorService->generateIconConfig();
