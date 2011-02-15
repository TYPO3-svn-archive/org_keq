#
# Table structure for table 'tx_kequestionnaire_results_tx_orgkeq_tx_org_workshop_mm'
# 
#
#CREATE TABLE tx_kequestionnaire_results_tx_orgkeq_tx_org_workshop_mm (
#  uid_local int(11) DEFAULT '0' NOT NULL,
#  uid_foreign int(11) DEFAULT '0' NOT NULL,
#  tablenames varchar(30) DEFAULT '' NOT NULL,
#  sorting int(11) DEFAULT '0' NOT NULL,
#  KEY uid_local (uid_local),
#  KEY uid_foreign (uid_foreign)
#);



#
# Table structure for table 'tx_kequestionnaire_results'
#
CREATE TABLE tx_kequestionnaire_results (
	tx_orgkeq_tx_org_workshop int(11) DEFAULT '0' NOT NULL
);