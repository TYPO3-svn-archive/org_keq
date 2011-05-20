<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

if (TYPO3_MODE != 'BE') {
		/**
		 * Hooks tx_kequestionnaire_pi1
		 */
	$_hookConf =& $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_questionnaire'];
	$_hookFile = 'EXT:org_keq/lib/class.tx_orgkeq_hooks_kequestionnaire_pi1.php';
	$_hookCall = $_hookFile . ':tx_orgkeq_hooks_kequestionnaire_pi1';

	$_hookConf['pi1_noQuestions'][]          = $_hookCall;  //Hook to manipulate the Error-Message for no questions
###	$_hookConf['pi1_getResultsSaveArray'][]  = $_hookCall;  //Hook to manipulate the loaded Array
###	$_hookConf['pi1_setResultsSaveArray'][]  = $_hookCall;  //Hook to manipulate the saved Array
	$_hookConf['pi1_setResultsSaveFields'][] = $_hookCall;  //Hook to manipulate the saveFields
###	$_hookConf['pi1_renderHiddenFields'][]   = $_hookCall;  //Hook to add hidden fields
	$_hookConf['pi1_renderLastPage'][]       = $_hookCall;  //Hook to do something after the questionnaire is finished
###	$_hookConf['pi1_markerArray'][]          = $_hookCall;  //Hook give more Fields for Questionnaire List
###	$_hookConf['getDifferentQuestionType'][] = $_hookCall;  //Hook undocumented
	$_hookConf['pi1_getQuestions'][]         = $_hookCall;  //Hook to manipulate the Question-Array



		/**
		 * Hooks tx_browser_pi1
		 */
	$_hookConf =& $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['browser'];
	$_hookFile = 'EXT:org_keq/lib/class.tx_orgkeq_hooks_browser_pi1.php';
	$_hookCall = $_hookFile . ':tx_orgkeq_hooks_browser_pi1';

###	$_hookConf['rows_filter_consolidated'][] = $_hookCall . '->showListfilterRating';  //  Hook for handle the consolidated rows
###	$_hookConf['rows_list_consolidated'][]   = $_hookCall . '->showListviewRating';    //  Hook for handle the consolidated rows
	$_hookConf['row_single_consolidated'][]  = $_hookCall . '->showSingleviewRating';  //  Hook for handle the consolidated row
}
?>