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
 */

/**
 * Hooks for use in tx_browser_pi1
 *
 * @author	Ulfried Herrmann <herrmann@die-netzmacher.de>
 * @package	TYPO3
 * @subpackage	tx_orgkeq
 */
class tx_orgkeq_hooks_browser_pi1 {

	public $prefixId = 'tx_orgkeq_hooks_browser_pi1';  // Same as class name
	public $extKey   = 'org_keq';                      // The extension key.
	protected $conf;                                   // The relevant part of plugin configuration


	// -------------------------------------------------------------------------
	/**
	 * Method to handle the consolidated rows in list view
	 *
	 * @param   array   $params:   parent plugin configuration
	 * @param   object  $pObj:     parent plugin object
	 * @return void
	 * @access  public
	 */
	public function showListviewRating(&$params, &$pObj) {
		$this->conf =& $params['pObj']->conf['extensions.']['tx_orgkeq.'];
			//  get uids of listed workshops
		$_rowUids = array();
		foreach ($pObj->pObj->rows as $rKey => $rVal) {
			$_rowUids[] = $rVal['tx_org_workshop.uid'];
		}

		$where = 'IN (' . implode(',', $_rowUids) . ')';
		$this->getRating($where, $pObj, 'list');
	}


	// -------------------------------------------------------------------------
	/**
	 * Method to handle the consolidated row in single view
	 *
	 * @param   array   $params:   parent plugin configuration
	 * @param   object  $pObj:     parent plugin object
	 * @return void
	 * @access  public
	 */
	public function showSingleviewRating(&$params, &$pObj) {
		$this->conf =& $params['pObj']->conf['extensions.']['tx_orgkeq.'];
		$where = '= ' . (int)$pObj->pObj->piVars['showUid'];
		$this->getRating($where, $pObj, 'single');
	}


	protected function getRating($where, &$pObj, $modus = 'list') {
			//  get ke_questionnaire results for this uid(s)
		$select_fields = 'xmldata, tx_orgkeq_tx_org_workshop';
		$from_table    = 'tx_kequestionnaire_results';
		$where_clause  = 'tx_orgkeq_tx_org_workshop ' . $where;
		$where_clause .= $pObj->pObj->cObj->enableFields($from_table, $show_hidden = 0);
		$groupBy       = '';
		$orderBy       = '';
		$limit         = '';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);
		$dataArr = array();
		while ($ftc = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$dataArr[$ftc['tx_orgkeq_tx_org_workshop']][] = t3lib_div::xml2Array($ftc['xmldata']);
		}

		foreach ($pObj->pObj->rows as $_rKey => $_rVal) {
			$_rating = $this->calculateRating($dataArr[$_rVal['tx_org_workshop.uid']], $modus);

			if ($modus == 'single') {
				$_rating['total'] = $this->getSingleRatingContent($pObj, $_rating);
			}

			$pObj->pObj->rows[$_rKey]['tx_org_workshop.rating'] = $_rating['total'];
/*
			if ($modus = 'single') {
				$i = 0;
				foreach ($_rating['groups'] as $_gVal) {
					$i++;
					$pObj->pObj->rows[$_rKey]['tx_org_workshop.rating' . $i] = $_gVal;
				}
			}
*/
		}
	}


	// -------------------------------------------------------------------------
	/**
	 * Method to calculate rating
	 *
	 * @param   array   $dataArr:  data correlating to database query
	 * @param   string  $modus:    'list' || 'single'
	 * @access  protected
	 */
	protected function calculateRating(&$dataArr, $modus = 'list') {
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
					//  value per scoring factor sum
				$_groups[$_gVal['title']] = $_groups[$_gVal['title']] / $_sumScoringFactor;
					//  rounding: possible values are \d.0 and \d.5
				$_groups[$_gVal['title']] = $_groups[$_gVal['title']] * 10 * 2;    //  group of ten; double
				$_groups[$_gVal['title']] = round($_groups[$_gVal['title']], -1);  //  round up to full group of ten
				$_groups[$_gVal['title']] = $_groups[$_gVal['title']] / 10 / 2;    //
			}
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

		return $rating;
	}


	// -------------------------------------------------------------------------
	/**
	 * Method for single View rating content
	 *
	 * @param   array   $rating:   rating data (total, groups)
	 * @return  string  $rating:   html content
	 * @access  protected
	 */
	protected function getSingleRatingContent(&$pObj, $rating) {
		$ratingValues =  $rating;
		$rating       =  '';
		$_stdWrapConf =& $this->conf['viewSingleRating.']['stdWrap.'];

		//
		if (isset($this->conf['viewSingleRating.']['css']) && $this->conf['viewSingleRating.']['css'] != '') {
			$pathToCSS = $GLOBALS['TSFE']->tmpl->getFileName($this->conf['viewSingleRating.']['css']);
			if ($pathToCSS != '') {
				$GLOBALS['TSFE']->additionalHeaderData['org_keq_singlerating'] = '<link rel="stylesheet" href="' . $pathToCSS . '" type="text/css" />';
			}
		}


			//  count scoring
		$numScoring = count($this->conf['scoring.']);

			//  total rating
		$rating .= $this->getSingleRatingImages($pObj, $ratingValues['total'], $numScoring);
		$rating .= ' (' . $ratingValues['total'] . ')';

			//  rating details
		$_boxDetails  = '';
			//  headline
		$_title = $pObj->pObj->pi_getLL('singleRatingTitle');
		$_title = $pObj->pObj->cObj->stdWrap($_title, $_stdWrapConf['title.']);
		$_boxDetails .= $_title;
			//  rating groups
		$_groups      = '';
		foreach ($ratingValues['groups'] as $_gKey => $_gVal) {
			$_gTitle  = $pObj->pObj->cObj->stdWrap($_gKey, $_stdWrapConf['groups.']['title.']);
			$_gImages = $this->getSingleRatingImages($pObj, $_gVal, $numScoring);
			$_groups .= $pObj->pObj->cObj->stdWrap($_gTitle . ' ' . $_gImages, $_stdWrapConf['groups.']['item.']);
		}
		$_groups      = $pObj->pObj->cObj->stdWrap($_groups, $_stdWrapConf['groups.']);
		$_boxDetails .= $_groups;
			// stdwrap
		$_boxDetails  = $pObj->pObj->cObj->stdWrap($_boxDetails, $_stdWrapConf['box.']);

			//  box details
		$rating .= $_boxDetails;

			// stdwrap
		$rating  = $pObj->pObj->cObj->stdWrap($rating, $_stdWrapConf['all.']);
			//  replace marker in link parameters
		if (!empty ($pObj->pObj->piVars['showUid'])) {
			$rating = preg_replace('/%23%23%23TX_ORG_WORKSHOP.UID%23%23%23/', (int)$pObj->pObj->piVars['showUid'], $rating);
		}

		return $rating;
	}


	// -------------------------------------------------------------------------
	/**
	 * Method for single View rating images
	 *
	 * @param   float   $rating:   rating value (total, groups)
	 * @return  string  $images:   html content
	 * @access  protected
	 */
	protected function getSingleRatingImages(&$pObj, $rating, $numScoring) {
		$imagesHTML = '';

		@list($_ratingTotalInt, $_ratingTotalDec) = explode('.', $rating);
		$_ratingTotalDec = (int)$_ratingTotalDec;

		$_images = array();
		for ($i = 1; $i <= $numScoring; $i++) {
		    	//  default
			$_images[$i] = 'empty';
				//  full star
			if ($i <= $_ratingTotalInt) {
				$_images[$i] = 'full';
			}
				//  half star?
			if ($i == ($_ratingTotalInt + 1) AND $_ratingTotalDec == 5) {
				$_images[$i] = 'half';
			}

		}
		//  display images
		foreach ($_images as $_iVal) {
			$_iConf      = $this->conf['viewSingleRating.']['image.'][$_iVal . '.'];
			$imagesHTML .= $pObj->pObj->cObj->IMAGE($_iConf);
		}

		return $imagesHTML;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/org_keq/class.tx_orgkeq_hooks.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/org_keq/class.tx_orgkeq_hooks.php']);
}
?>