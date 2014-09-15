<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Francois Suter <typo3@cobweb.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Provides additional fields to the "Synchronize Users" Scheduler task.
 *
 * @author     Francois Suter <typo3@cobweb.ch>
 * @package    TYPO3
 * @subpackage ig_ldap_sso_auth
 */
class Tx_IgLdapSsoAuth_Task_ImportUsersAdditionalFields implements tx_scheduler_AdditionalFieldProvider {

	/**
	 * Gets additional fields to render in the form to add/edit a task.
	 *
	 * Two extra fields are provided. One is used to define the context (FE, BE or both)
	 * and one to select a LDAP configuration (or all).
	 *
	 * @param array $taskInfo Values of the fields from the add/edit task form
	 * @param tx_scheduler_Task $task The task object being edited. Null when adding a task!
	 * @param tx_scheduler_Module $schedulerModule Reference to the scheduler backend module
	 * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $schedulerModule) {
		$additionalFields = array();

		// Process the context field
		$fieldName = 'tx_igldapssoauth_context';
		// Initialize extra field value, if not yet defined
		if (empty($taskInfo[$fieldName])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo[$fieldName] = 'both';
			} elseif ($schedulerModule->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo[$fieldName] = $task->getContext();
			}
		}

		// Write the code for the field
		$fieldID = 'task_' . $fieldName;
		$fieldCode  = '<select name="tx_scheduler[' . $fieldName . ']" id="' . $fieldID . '">';
		// Assemble selector options
		$selected = '';
		if ($taskInfo[$fieldName] == 'both') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="both"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.context.both') . '</option>';
		$selected = '';
		if ($taskInfo[$fieldName] == 'FE') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="FE"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.context.fe') . '</option>';
		$selected = '';
		if ($taskInfo[$fieldName] == 'BE') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="BE"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.context.be') . '</option>';
		$fieldCode .= '</select>';
		// Register the field
		$additionalFields[$fieldID] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.context',
			'cshLabel' => $fieldID
		);

		// Process the configuration field
		$fieldName = 'tx_igldapssoauth_configuration';
		// Initialize extra field value, if not yet defined
		if (empty($taskInfo[$fieldName])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo[$fieldName] = 0;
			} elseif ($schedulerModule->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo[$fieldName] = $task->getConfiguration();
			}
		}

		// Write the code for the field
		$fieldID = 'task_' . $fieldName;
		$fieldCode  = '<select name="tx_scheduler[' . $fieldName . ']" id="' . $fieldID . '">';
		// Assemble selector options
		$selected = '';
		$taskInfo[$fieldName] = intval($taskInfo[$fieldName]);
		if ($taskInfo[$fieldName] === 0) {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="0"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.configuration.all') . '</option>';
		// Get the existing LDAP configurations
		/** @var Tx_IgLdapSsoAuth_Domain_Repository_ConfigurationRepository $configurationRepository */
		$configurationRepository = t3lib_div::makeInstance('Tx_IgLdapSsoAuth_Domain_Repository_ConfigurationRepository');
		$ldapConfigurations = $configurationRepository->fetchAll();
		foreach ($ldapConfigurations as $configuration) {
			$uid = $configuration['uid'];
			$selected = '';
			if ($taskInfo[$fieldName] == $uid) {
				$selected = ' selected="selected"';
			}
			$fieldCode .= '<option value="' . $uid . '"' . $selected . '>' . $configuration['name'] . '</option>';
		}
		$fieldCode .= '</select>';
		// Register the field
		$additionalFields[$fieldID] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.configuration',
			'cshLabel' => $fieldID
		);

		// Process the missing user handling field
		$fieldName = 'tx_igldapssoauth_missinguserhandling';
		// Initialize extra field value, if not yet defined
		if (empty($taskInfo[$fieldName])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo[$fieldName] = 'nothing';
			} elseif ($schedulerModule->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo[$fieldName] = $task->getMissingUsersHandling();
			}
		}

		// Write the code for the field
		$fieldID = 'task_' . $fieldName;
		$fieldCode  = '<select name="tx_scheduler[' . $fieldName . ']" id="' . $fieldID . '">';
		// Assemble selector options
		$selected = '';
		if ($taskInfo[$fieldName] == 'disable') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="disable"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.missinguserhandling.disable') . '</option>';
		$selected = '';
		if ($taskInfo[$fieldName] == 'delete') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="delete"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.missinguserhandling.delete') . '</option>';
		$selected = '';
		if ($taskInfo[$fieldName] == 'nothing') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="nothing"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.missinguserhandling.nothing') . '</option>';
		$fieldCode .= '</select>';
		// Register the field
		$additionalFields[$fieldID] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.missinguserhandling',
			'cshLabel' => $fieldID
		);

		// Process the restored user handling field
		$fieldName = 'tx_igldapssoauth_restoreduserhandling';
		// Initialize extra field value, if not yet defined
		if (empty($taskInfo[$fieldName])) {
			if ($schedulerModule->CMD == 'add') {
				$taskInfo[$fieldName] = 'nothing';
			} elseif ($schedulerModule->CMD == 'edit') {
				// In case of edit, set to internal value if no data was submitted already
				$taskInfo[$fieldName] = $task->getRestoredUsersHandling();
			}
		}

		// Write the code for the field
		$fieldID = 'task_' . $fieldName;
		$fieldCode  = '<select name="tx_scheduler[' . $fieldName . ']" id="' . $fieldID . '">';
		// Assemble selector options
		$selected = '';
		if ($taskInfo[$fieldName] == 'enable') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="enable"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.restoreduserhandling.enable') . '</option>';
		$selected = '';
		if ($taskInfo[$fieldName] == 'undelete') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="undelete"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.restoreduserhandling.undelete') . '</option>';
		$selected = '';
		if ($taskInfo[$fieldName] == 'both') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="both"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.restoreduserhandling.both') . '</option>';
		$selected = '';
		if ($taskInfo[$fieldName] == 'nothing') {
			$selected = ' selected="selected"';
		}
		$fieldCode .= '<option value="nothing"' . $selected . '>' . $GLOBALS['LANG']->sL('LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.restoreduserhandling.nothing') . '</option>';
		$fieldCode .= '</select>';
		// Register the field
		$additionalFields[$fieldID] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:ig_ldap_sso_auth/Resources/Private/Language/locallang.xml:task.import_users.field.restoreduserhandling',
			'cshLabel' => $fieldID
		);

		return $additionalFields;
	}

	/**
	 * Validates the additional fields' values.
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param tx_scheduler_Module $schedulerModule Reference to the scheduler backend module
	 * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $schedulerModule) {
		// Since only valid values could be chosen from the selectors, always return true
		return TRUE;
	}

	/**
	 * Takes care of saving the additional fields' values in the task's object.
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param tx_scheduler_Task $task Reference to the scheduler backend module
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$task->setContext($submittedData['tx_igldapssoauth_context']);
		$task->setConfiguration($submittedData['tx_igldapssoauth_configuration']);
		$task->setMissingUsersHandling($submittedData['tx_igldapssoauth_missinguserhandling']);
		$task->setRestoredUsersHandling($submittedData['tx_igldapssoauth_restoreduserhandling']);
	}
}
