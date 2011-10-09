<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}



  // Store record configuration
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
switch($confArr['store_records']) {
case('Multi grouped: record groups in different directories'):
	$str_store_record_conf = 'IN (###PAGE_TSCONFIG_IDLIST###)';
	break;
case('Clear presented: each record group in one directory at most'):
	$str_store_record_conf = 'IN (###PAGE_TSCONFIG_ID###)';
	break;
case('Easy 2: same as easy 1 but with storage pid'):
	$str_store_record_conf = '= ###STORAGE_PID###';
	break;
case('Easy 1: all in the same directory'):
	default:
	$str_store_record_conf = '= ###CURRENT_PID###';
}



	//  ke_questionnaire.result: add workshop relation
$tempColumns = array(
	'tx_orgkeq_tx_org_workshop' => array(
		'exclude' => 1,		
		'label'   => 'LLL:EXT:org_keq/locallang_db.xml:tx_kequestionnaire_results.tx_orgkeq_tx_org_workshop',
		'config'  => array (
			'type'                => 'select',
			'foreign_table'       => 'tx_org_workshop',
			'foreign_table_where' => 'AND tx_org_workshop.pid ' . $str_store_record_conf . ' ' . $TCA['tx_org_workshop']['ctrl']['default_sortby'],
			'size'                => 1,
			'minitems'            => 0,
			'maxitems'            => 1,
			'items'               => array(
				array('', ''),
			),
		),
	),
);

t3lib_div::loadTCA('tx_kequestionnaire_results');
$TCA['tx_kequestionnaire_results']['ctrl']['dividers2tabs'] = 1;
$TCA['tx_kequestionnaire_results']['interface']['showRecordFieldList'] .= ',tx_orgkeq_tx_org_workshop';
t3lib_extMgm::addTCAcolumns('tx_kequestionnaire_results', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('tx_kequestionnaire_results', '--div--;LLL:EXT:org_keq/locallang_db.xml:tx_kequestionnaire_results.div_relations,tx_orgkeq_tx_org_workshop;;;;1-1-1');



	//  add static files
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/workshops_rating/', '+Org Workshop: Rating');
?>