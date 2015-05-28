<?php

namespace Denkwerk\DwContentElements\Domain\Model;


class SysFileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference {

	/**
	 * tx_dw_button_type
	 *
	 * @var string
	 */
	protected $buttonType;

	/**
	 * @param string $buttonType
	 */
	public function setButtonType($buttonType)
	{
		$this->buttonType = $buttonType;
	}

	/**
	 * @return string
	 */
	public function getButtonType()
	{
		return $this->buttonType;
	}


} 