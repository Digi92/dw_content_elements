<?php

namespace Denkwerk\DwContentElements\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Sascha Zander <sascha.zander@denkwerk.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * **************************************************************/

use Denkwerk\DwContentElements\Service\InjectorService;
use TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface;

/**
 * Class ExtTablesInclusionPostProcessingHook
 * @package Denkwerk\DwContentElements\Hooks
 *
 * Note: This hook will load all content element configuration and add the plugin configuration after
 * all TYPO3 TCA loading tasks.
 */
class ExtTablesInclusionPostProcessingHook implements TableConfigurationPostProcessingHookInterface
{
    /**
     * Function which may process data created / registered by extTables
     * scripts (f.e. modifying TCA data of all extensions)
     */
    public function processData()
    {
        // Register content element plugins
        $injectorService = new InjectorService();
        $injectorService->injectPluginConfiguration();
    }
}
