<?php

########################################################################
# Extension Manager/Repository config file for ext "org_keq".
#
# Auto generated 10-03-2011 08:09
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Org +Rating (keq)',
	'description' => 'This extension helps you to get feedback on your Org Workshops using Questionnaire (ke_questionnaire).',
	'category' => 'plugin',
	'author' => 'Ulfried Herrmann',
	'author_email' => 'http://herrmann.at.die-netzmacher.de',
	'shy' => '',
	'dependencies' => 'org,org_workshops,ke_questionnaire',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'Die Netzmacher',
	'version' => '0.2.2',
	'constraints' => array(
		'depends' => array(
			'org' => '',
			'org_workshops' => '',
			'ke_questionnaire' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:18:{s:9:"ChangeLog";s:4:"df01";s:10:"README.txt";s:4:"9fa9";s:21:"ext_conf_template.txt";s:4:"8c7a";s:12:"ext_icon.gif";s:4:"ec42";s:17:"ext_localconf.php";s:4:"0ad5";s:14:"ext_tables.php";s:4:"4da8";s:14:"ext_tables.sql";s:4:"3181";s:16:"locallang_db.xml";s:4:"8075";s:14:"doc/manual.sxw";s:4:"0e23";s:41:"lib/class.tx_orgkeq_hooks_browser_pi1.php";s:4:"2376";s:49:"lib/class.tx_orgkeq_hooks_kequestionnaire_pi1.php";s:4:"12ef";s:29:"res/icons/ratingstarEmpty.png";s:4:"67b9";s:28:"res/icons/ratingstarFull.png";s:4:"a402";s:28:"res/icons/ratingstarHalf.png";s:4:"79a6";s:31:"res/template/questionnaire.html";s:4:"278e";s:39:"res/template/tx_orgkeq.singlerating.css";s:4:"60a2";s:37:"static/workshops_rating/constants.txt";s:4:"9eed";s:33:"static/workshops_rating/setup.txt";s:4:"3e61";}',
	'suggests' => array(
	),
);

?>