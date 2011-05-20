<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Ulfried Herrmann <herrmann@die-netzmacher.de>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *   48: class tx_orgkeq_hooks_kequestionnaire_pi1
 *   63:     public function pi1_noQuestions(&$pObj)
 *  113:     public function pi1_setResultsSaveFields(&$pObj, $saveFields)
 *  155:     public function pi1_getQuestions(&$pObj)
 *  208:     public function pi1_renderLastPage(&$pObj, $result_id, &$markerArray)
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Hooks for use in tx_kequestionnaire_pi1
 *
 * @author	Ulfried Herrmann <herrmann@die-netzmacher.de>
 * @package	TYPO3
 * @subpackage	tx_orgkeq
 */
class tx_orgkeq_hooks_kequestionnaire_pi1 {

	public $prefixId = 'tx_orgkeq_hooks_browser_pi1';  // Same as class name
	public $extKey   = 'org_keq';                      // The extension key.


	// -------------------------------------------------------------------------
	/**
	 * Method to manipulate pi1_getResultsSaveArray (tx_kequestionnaire_pi1)
	 * If you want to have a different action when no questions are found for the current questionnaire.
	 *
	 * @param   object   $pObj:   procObj
	 * @return  string   $content  content to be shown on the Error that there are no active questions for the plugin
	 * @access public
	 */
	public function pi1_noQuestions(&$pObj) {
		if (!empty ($pObj->piVars['next'])) {
				//  if this is not the first page of questionnaire
			$content = $pObj->renderLastPage();
		} elseif ($pObj->ffdata['access'] != 'FE_USERS' OR empty ($pObj->piVars['tx_org']['workshop'])) {
				//  if access is not restricted to fe users or there is no workshop uid given
			$content = $pObj->pi_getLL('no_questions');
		} else {
				//  default
			$content = $pObj->pi_getLL('no_more');
		}

		return $content;
	}


	// -------------------------------------------------------------------------
	/**
	 * Method to manipulate pi1_getResultsSaveArray (tx_kequestionnaire_pi1)
	 * If you want to work on the saveArray before it is used in ke_questionnaire
	 *
	 * @param   object   $pObj:   procObj
	 * @return  void
	 * @access public
	 */
##	public function pi1_getResultsSaveArray(&$pObj) {
##	}


	// -------------------------------------------------------------------------
	/**
	 * Method to manipulate pi1_setResultsSaveArray (tx_kequestionnaire_pi1)
	 * If you want to work on the saveArray before it is written into the database
	 * SaveArray: questionnaire answers (finally stored as XML in database)
	 *
	 * @param   object   $pObj:   procObj
	 * @return  array    Array with saved Values for the participation taking place to be saved in the setResults Function
	 * @access public
	 */
##	public function pi1_setResultsSaveArray(&$pObj) {
##echo '<pre><b><u>$pObj->saveArray:</u></b> ' . print_r($pObj->saveArray, 1) . '</pre>';
##		return $pObj->saveArray;
##	}


	// -------------------------------------------------------------------------
	/**
	 * Method to manipulate pi1_setResultsSaveFields (tx_kequestionnaire_pi1)
	 * inserts workshop uid
	 *
	 * @param   object   $pObj:   procObj
	 * @param   array    $saveFields: fields to save in database
	 * @return  array    Array with Values of the result-dataset
	 * @access public
	 */
	public function pi1_setResultsSaveFields(&$pObj, $saveFields) {
			//  check if there is a workshop uid given
		if (!empty ($pObj->piVars['tx_org']['workshop'])) {
				//  add field value
			$_workshop = $pObj->piVars['tx_org']['workshop'];
			$saveFields['tx_orgkeq_tx_org_workshop'] = (int)$_workshop;
		}

		return $saveFields;
	}


	// -------------------------------------------------------------------------
	/**
	 * Method to manipulate pi1_renderHiddenFields (tx_kequestionnaire_pi1)
	 *
	 * @param   object   $pObj:   procObj
	 * @return  void
	 * @access public
	 */
// uherrmann, netzmacher, 110201: obsolete (?);
// &?tx_kequestionnaire_pi1[<KEY1>][<KEY2>] wird automatisch als hidden field übergeben!!!
##	public function pi1_renderHiddenFields(&$pObj) {
##		$content = '';
##		if (!empty ($pObj->piVars['org_workshop']) AND is_numeric($pObj->piVars['org_workshop'])) {
##			$content .= chr(10) . '<input type="hidden" name="tx_kequestionnaire_pi1[org_workshop]" value="' . (int)$pObj->piVars['org_workshop'] . '" />';
##		}
##
##		return $content;
##	}


	// -------------------------------------------------------------------------
	/**
	 * Method to manipulate pi1_getQuestions (tx_kequestionnaire_pi1)
	 * used to check here the user is allowed to vote this workshop
	 *
	 * @param   object   $pObj:   procObj
	 * @return  void
	 * @access public
	 */
	public function pi1_getQuestions(&$pObj) {
			//  do all the next only on first page
		if (!empty ($pObj->piVars['next'])) {
			return;
		}

			//  check if access is restricted to fe users
			//  check if there is a workshop uid given
			//
		if ($pObj->ffdata['access'] != 'FE_USERS' OR empty ($pObj->piVars['tx_org']['workshop'])) {
			return;
		}

			//  get number of votings THIS user made to THIS workshop
		$select_fields = 'COUNT(*) AS num_results';
		$from_table    = 'tx_kequestionnaire_results';
		$where_clause  = 'cruser_id = ' . (int)$GLOBALS['TSFE']->fe_user->user['uid'];
		$where_clause .= ' AND pid = ' . (int)$pObj->ffdata['storage_pid'];
		$where_clause .= ' AND tx_orgkeq_tx_org_workshop = ' . (int)$pObj->piVars['tx_org']['workshop'];
		$where_clause .= $pObj->cObj->enableFields($from_table, $show_hidden = 0);
		$groupBy       = '';
		$orderBy       = '';
		$limit         = '';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($ftc['num_results'] > 0) {
				//  user has rated this workshop yet!
				//  clear question array: return something else than an empty string or an array
			return 'Error';
		}
	}


	// -------------------------------------------------------------------------
	/**
	 * Method to manipulate getDifferentQuestionType (tx_kequestionnaire_pi1)
	 *
	 * @param   object   $pObj:   procObj
	 * @param   ?    $question: question from Database
	 * @param   array    $piVars: plugin piVars
	 * @return  void
	 * @access public
	 */
##	public function getDifferentQuestionType(&$pObj, $question, $piVars) {
##	}


	// -------------------------------------------------------------------------
	/**
	 * Method to do something after the questionnaire is finished (tx_kequestionnaire_pi1)
	 * write calculated rating average for given workshop to db
	 *
	 * @param   object   $pObj:   procObj
	 * @param   integer  $result_id: last insert id / update id
	 * @param   array    $markerArray
	 * @return  array    $markerArray
	 * @access public
	 */
	public function pi1_renderLastPage(&$pObj, $result_id, &$markerArray) {
		if (!empty ($pObj->piVars['tx_org']['workshop'])) {
#echo '<pre><b><u>' . __LINE__ . ' $pObj->piVars[tx_org]:</u></b> ' . print_r($pObj->piVars['tx_org'], 1) . '</pre>';
			$this->conf =& $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_browser_pi1.']['extensions.']['tx_orgkeq.'];
			$this->getRating($pObj);
		}

			//  always return $markerArray even it's untouched!
		return $markerArray;
	}


	// -------------------------------------------------------------------------
	/**
	 * Method to calculate rating average
	 *
	 * @param   string  $where:    prepared part of where clause
	 * @param   object  $pObj:     parent plugin object
	 * @param   string  $modus:    'list' || 'single'
	 * @access  protected
	 *
	 * @see tx_orgkeq_hooks_browser_pi1::getRating()
	 */
	protected function getRating(&$pObj) {
		$this->workshop = (int)$pObj->piVars['tx_org']['workshop'];
			//  get ke_questionnaire results for this uid(s)
		$select_fields = 'xmldata, tx_orgkeq_tx_org_workshop';
		$from_table    = 'tx_kequestionnaire_results';
		$where_clause  = 'tx_orgkeq_tx_org_workshop = ' . $this->workshop;
		$where_clause .= $pObj->cObj->enableFields($from_table, $show_hidden = 0);
		$groupBy       = '';
		$orderBy       = '';
		$limit         = '';

$sql = $GLOBALS['TYPO3_DB']->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
#echo '<pre><b><u>' . __LINE__ . ' $sql:</u></b> ' . print_r($sql, 1) . '</pre>';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$dataArr = array();
		while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$dataArr[$ftc['tx_orgkeq_tx_org_workshop']][] = t3lib_div::xml2Array($ftc['xmldata']);
		}

		$_rating = $this->calculateRating($dataArr[$this->workshop]);
	}


	// -------------------------------------------------------------------------
	/**
	 * Method to calculate rating
	 *
	 * @param   array   $dataArr:  data correlating to database query
	 * @access  protected
	 *
	 * @see tx_orgkeq_hooks_browser_pi1::calculateRating()
	 */
	protected function calculateRating(&$dataArr) {
#echo '<pre><b><u>' . __LINE__ . ' $dataArr:</u></b> ' . print_r($dataArr, 1) . '</pre>';
			//  rewrite array answers
		$_answersFactor = array();
		foreach ($this->conf['groups.'] as $_cVal) {
			$_answersFactor = $_answersFactor + $_cVal['answers.'];  //  do not use array_merge(), it renumbers array keys!
		}


		/**
		 * consolidate given ratings
		 */
		$_dataConsolidated = array();
		foreach ($dataArr as $_dkey => $_dVal) {
				// check if array value is an array itselfs (excludes strings like messages)
			if (is_array($_dVal)) {
				foreach ($_dVal as $_ddVal) {
						//  check if array value includes an answer (excludes empty ratings)
					if (isset($_ddVal['answer']) AND is_array($_ddVal['answer'])) {
						$_dataConsolidated[] = $_ddVal['answer']['options'];
					}
				}
			}
		}

			//  count given ratings
		$_numRatings = count($_dataConsolidated);


		/**
		 * assign questions with accumulated values
		 */
		$_questions = array();
		foreach ($_dataConsolidated as $_dVal) {
			foreach ($_dVal as $_ddKey => $_ddVal) {
				if (!isset ($_questions[$_ddKey])) {
						//  initiate question as array key, value 0
					$_questions[$_ddKey] = 0;
				}
					//  cumulate values
				$_questions[$_ddKey] += $this->conf['scoring.'][$_ddVal['single']];
			}
		}

		/**
		 * get average and scoring, do rounding
		 */
		foreach ($_questions as $_qKey => $_qVal) {
				//  average
			$_questions[$_qKey] = $_qVal / $_numRatings;
		}


		/**
		 * group answer values
		 */
		$_groups = array();
		foreach ($this->conf['groups.'] as $_gVal) {
				//  initiate group as array key, value 0
			$_groups[$_gVal['title']] = 0;
				//  initiate scoring factor sum
			$_sumScoringFactor = 0;
			foreach ($_gVal['answers.'] as $_gaKey => $_gaVal) {
					//  initiate group as array key, value 0
				if (!isset ($_groups[$_gVal['title']])) {
						//  initiate group as array key, value 0
					$_groups[$_gVal['title']] = 0;
				}
				$_questionValue = $_questions[$_gaKey];
					//  scoring
				$_questionValue = $_questionValue * $_answersFactor[$_gaKey];
					//  cumulate scoring factor sum
				$_sumScoringFactor += $_answersFactor[$_gaKey];

					//  cumulate values
				$_groups[$_gVal['title']] += $_questionValue;
			}
			//  value per scoring factor sum
			$_groups[$_gVal['title']] = (int)$_groups[$_gVal['title']] / (int)$_sumScoringFactor;
				//  rounding: possible values are \d.0 and \d.5
			$_groups[$_gVal['title']] = $_groups[$_gVal['title']] * 10 * 2;    //  group of ten; double
			$_groups[$_gVal['title']] = round($_groups[$_gVal['title']], -1);  //  round up to full group of ten
			$_groups[$_gVal['title']] = $_groups[$_gVal['title']] / 10 / 2;    //
		}


		/**
		 * total
		 */
			//  count groups
		$_numGroups = count($_groups);
		$_total     = 0;
		foreach ($_groups as $_gKey => $_gVal) {
		    $_total += $_gVal;
		}
		$_total = $_total / $_numGroups;
			//  rounding: possible values are \d.0 and \d.5
		$_total = $_total * 10 * 2;    //  group of ten; double
		$_total = round($_total, -1);  //  round up to full group of ten
		$_total = $_total / 10 / 2;    //


		$rating = array(
		    'total'  => $_total,
		    'groups' => ($modus == 'list' ? NULL : $_groups),
		);


			//  update rating for this workshop
		$table           = 'tx_org_workshop';
		$where           = 'uid = ' . $this->workshop;
		$fields_values   = array(
			'rating' => (int)$rating['total'],
		);
		$no_quote_fields = FALSE;
$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $fields_values, $no_quote_fields);
#echo '<pre><b><u>' . __LINE__ . ' $sql:</u></b> ' . print_r($sql, 1) . '</pre>';
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);

			// Clear cache
		$clearCache   = t3lib_div::trimExplode(',', $this->conf['clear_cacheCmd'], TRUE);
	##	$clearCache[] = $GLOBALS['TSFE']->id;
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		/* @var $tce t3lib_TCEmain */
		foreach (array_unique($clearCache) as $pid) {
			$pid = (int)$pid;
#echo '<pre><b><u>' . __LINE__ . ' $tce->clear_cacheCmd(' . $pid . '):</u></b></pre>';
			$tce->clear_cacheCmd($pid);
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/org_keq/class.tx_orgkeq_hooks.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/org_keq/class.tx_orgkeq_hooks.php']);
}
?>