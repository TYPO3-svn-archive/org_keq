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
	$str_marker_pid        = '###STORAGE_PID###';
	$str_store_record_conf = '= ###STORAGE_PID###';
	break;
case('Easy 1: all in the same directory'):
	default:
	$str_store_record_conf = 'pid=###CURRENT_PID###';
}



	//  add workshop relation to ke_questionnaire.result
	//  @ToDo: if necessary add other relations
	//  @ToDo: maybe it should be configurable in EM
t3lib_div::loadTCA('tx_org_workshop');
$tempColumns = array (
	'tx_orgkeq_tx_org_workshop' => array (		
		'exclude' => 1,		
		'label'   => 'LLL:EXT:org_keq/locallang_db.xml:tx_kequestionnaire_results.tx_orgkeq_tx_org_workshop',
		'config'  => array (
			'type'                => 'select',
			'foreign_table'       => 'tx_org_workshop',
			'foreign_table_where' => 'AND tx_org_workshop.pid ' . $str_store_record_conf . ' ' . $TCA['tx_org_workshop']['ctrl']['default_sortby'],
			'size'                => 1,
			'minitems'            => 0,
			'maxitems'            => 1,
			'items'               => Array (
				array('', ''),
			),
		),
	),
);

t3lib_div::loadTCA('tx_kequestionnaire_results');
$TCA['tx_kequestionnaire_results']['ctrl']['dividers2tabs'] = 1;
t3lib_extMgm::addTCAcolumns('tx_kequestionnaire_results', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('tx_kequestionnaire_results', '--div--;LLL:EXT:org_keq/locallang_db.xml:tx_kequestionnaire_results.div_relations,tx_orgkeq_tx_org_workshop;;;;1-1-1');

t3lib_extMgm::addStaticFile($_EXTKEY,'static/workshops_rating/', '+Org Workshop: Rating');
?>