<?php
namespace Denkwerk\DwContentElements\Utility;

	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2012-2013 Marcel Wieser <typo3dev@marcel-wieser.de>, denkwerk GmbH
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

/**
 * Helper Class which makes various tools and helper available
 *
 * @author Andy Hausmann <andy@sota-studio.de>
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Div {

	/**
	 * Better implementation of php's array_combine().
	 * This wont throw false in case both array haven't an identical size.
	 *
	 * @static
	 * @param array $a Array containing the keys.
	 * @param array $b Array containing the values.
	 * @param bool $pad Switch for allowing padding. Fills the combined array with empty values if any array is larger than the other one.
	 * @return array Combined array.
	 */
	public static function combineArray($a, $b, $pad = true) {
		$acount = count($a);
		$bcount = count($b);
		// more elements in $a than $b but we don't want to pad either
		if (!$pad) {
			$size = ($acount > $bcount) ? $bcount : $acount;
			$a = array_slice($a, 0, $size);
			$b = array_slice($b, 0, $size);
		} else {
			// more headers than row fields
			if ($acount > $bcount) {
				$more = $acount - $bcount;
				// how many fields are we missing at the end of the second array?
				// Add empty strings to ensure arrays $a and $b have same number of elements
				$more = $acount - $bcount;
				for ($i = 0; $i < $more; $i++) {
					$b[] = "";
				}
				// more fields than headers
			} else if ($acount < $bcount) {
				$more = $bcount - $acount;
				// fewer elements in the first array, add extra keys
				for ($i = 0; $i < $more; $i++) {
					$key = 'extra_field_0' . $i;
					$a[] = $key;
				}

			}
		}

		return array_combine($a, $b);
	}


	/**
	 * Method to merge arrays to one multidimensional array.
	 *
	 * @param array $arrays
	 * @return array
	 */
	public static function mergeArrays($arrays) {
		$arrayCount = 0;
		foreach ($arrays as $array) {
			if (empty($array[0])) {
				$arrayCount++;
			}
		}

		if ($arrayCount == count($arrays)) return array();

		$newArray = array();

		for ($i = 0; count($arrays) > $i; $i++) {
			$arrays[$i] = array_values($arrays[$i]);
		}

		for ($i = 0; count($arrays) > $i; $i++) {
			for ($j = 0; count($arrays[0]) > $j; $j++) {
				$newArray[$j][] = $arrays[$i][$j];
			}
		}

		return $newArray;
	}
}