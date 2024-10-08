<?php
namespace Denkwerk\DwContentElementsSource\Controller;

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
use Denkwerk\DwContentElements\Controller\BaseController;

/**
 * Class ElementsController
 * @package Denkwerk\DwContentElementsSource\Controller
 */
class ElementsController extends BaseController
{
    /**
     * e1000List
     */
    public function e1000List(array $elementData): array
    {
        return [
            'irreRelations' => $this->irreService->getRelations(
                $elementData,
                'tx_dwcontentelementssource_domain_model_listitem'
            )
        ];
    }
}
