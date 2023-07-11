<?php
namespace Denkwerk\DwContentElements\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Marcel Wieser <typo3dev@marcel-wieser.de>, denkwerk
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
 ***************************************************************/
use Psr\Http\Message\ResponseInterface;
use Denkwerk\DwWordpressRss\Domain\Repository\ItemRepository;
use Denkwerk\DwContentElements\Controller\BaseController;
use Denkwerk\DwContentElementsSource\Service\CrisisTeaser;
use Denkwerk\DwContentElementsSource\Utility\Div;
use Denkwerk\DwSiteexport\Service\File as FileService;
use Denkwerk\DwSiteexport\Service\Queue\File;
use Denkwerk\DwSiteexport\Service\Queue\QueueTypes;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;

/**
 * Class ElementsController
 * @package Denkwerk\DwContentElementsSource\Controller
 */
class Elements2Controller extends BaseController
{
    /**
     * e3001AnchorLinkAction
     */
    public function e3001AnchorLink(array $contentObj): array
    {
        $a = 1;
//        return [
//            'irreRelations' => $this->irreService->getRelations(
//                $contentObj,
//                'tx_dwc_anchor_link_item'
//            )
//        ];

        return [
            'bla' => 'blub'
        ];
    }
}
