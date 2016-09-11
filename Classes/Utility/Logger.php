<?php
namespace Denkwerk\DwContentElements\Utility;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2016 Sascha Zander <sascha.zander@denkwerk.com>, denkwerk GmbH
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
 * Class with functions for write into the system log
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Logger
{

    /**
     * Writes an entry in the logfile/table
     * Documentation in "TYPO3 Core API"
     *
     * @param $message Default text that follows the message (in english!).
     * @param string $tableName Table name. Special field used by tce_main.php.
     * @param integer $recuid Record UID.
     * @param integer $eventPid The page_uid (pid) where the event occurred.
     */
    public static function simpleErrorLog($message, $tableName, $recuid, $eventPid)
    {
        $fieldsValues = array(
            'userid' => (isset($GLOBALS['BE_USER']->id) ? $GLOBALS['BE_USER']->id : 0),
            'type' => 5,
            'action' => 0,
            'error' => 1,
            'details_nr' => 0,
            'details' => $message,
            'log_data' => serialize(array()),
            'tablename' => $tableName,
            'recuid' => $recuid,
            'IP' => (string)\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'),
            'tstamp' => time(),
            'event_pid' => $eventPid,
            'NEWid' => '',
            'workspace' => 0
        );
        $GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_log', $fieldsValues);
        $GLOBALS['TYPO3_DB']->sql_insert_id();
    }

}