<?php

########################################################################
# Extension Manager/Repository config file for ext "org_keq".
#
# Auto generated 31-01-2011 16:20
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Org +Rating with Questionnaire',
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
	'version' => '0.2.0',
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
	'_md5_values_when_last_written' => 'a:10:{s:9:"ChangeLog";s:4:"f6c7";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"7a3d";s:14:"ext_tables.sql";s:4:"56fa";s:16:"locallang_db.xml";s:4:"3f03";s:19:"doc/wizard_form.dat";s:4:"cfb8";s:20:"doc/wizard_form.html";s:4:"75b5";s:23:"static/ts/constants.txt";s:4:"4f1b";s:19:"static/ts/setup.txt";s:4:"6e0f";}',
);
?>