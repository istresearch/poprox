<?php
namespace BitsTheater\actors;
use BitsTheater\actors\Understudy\BitsInstall as BaseActor;
use BitsTheater\scenes\Install as MyScene; /* @var $v MyScene */
use com\blackmoonit\database\DbConnInfo;
use com\blackmoonit\Strings;
{//namespace begin

class Install extends BaseActor {
	
	public function getDbConns() {
		$db_conns = array();
	
		$theDbConnInfo = DbConnInfo::asSchemeINI('webapp');
		$theDbConnInfo->dbConnSettings->dbname = 'memex_ist_webapp';
		$theDbConnInfo->dbConnSettings->host = 'roxy-db.istresearch.com';
		$theDbConnInfo->dbConnSettings->username = 'webz';
		$db_conns[] = $theDbConnInfo;
		/* we do not calculate queue-depth stats anymore, no need to read the scrape db
			$theDbConnInfo = DbConnInfo::asSchemeINI('roxy_scrape');
		$theDbConnInfo->dbConnOptions->table_prefix = '';
		$theDbConnInfo->dbConnSettings->dbname = 'roxy_scrape';
		$theDbConnInfo->dbConnSettings->host = 'roxy-db.istresearch.com';
		$theDbConnInfo->dbConnSettings->username = 'roxy';
		$db_conns[] = $theDbConnInfo;
		*/
		$theDbConnInfo = DbConnInfo::asSchemeINI('memex_ist');
		$theDbConnInfo->dbConnOptions->table_prefix = '';
		$theDbConnInfo->dbConnSettings->dbname = 'memex_ist';
		$theDbConnInfo->dbConnSettings->host = 'roxy-db.istresearch.com';
		$theDbConnInfo->dbConnSettings->username = 'roxy';
		$db_conns[] = $theDbConnInfo;
	
		$theDbConnInfo = DbConnInfo::asSchemeINI('memex_ht');
		$theDbConnInfo->dbConnOptions->table_prefix = '';
		$theDbConnInfo->dbConnSettings->dbname = 'memex_ht';
		$theDbConnInfo->dbConnSettings->host = 'roxy-db.istresearch.com';
		$theDbConnInfo->dbConnSettings->username = 'roxy';
		$db_conns[] = $theDbConnInfo;
	
		return $db_conns;
	}
	
}//end class

}//end namespace
