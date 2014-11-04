<?php
namespace ISTResearch_Roxy\models;
use ISTResearch_Roxy\models\RoxyCore as BaseModel;
use com\blackmoonit\Strings;
use com\blackmoonit\database\FinallyCursor;
use com\blackmoonit\exceptions\DbException;
use \PDO;
use \PDOStatement;
use \PDOException;
use \DateTime;
use \DateInterval;
{//namespace begin

class SiteNaughtyReviews extends BaseModel {
	public $site_id = 'naughtyreviews'; //this should the part of the URL that defines this site in roxy, e.g. [%site]/poprox/backpage/ads
	public $site_display_name = 'Naughty Reviews';  //the nice display name to use
	
	protected function setupAfterDbConnected() {
		parent::setupAfterDbConnected();
		//these vars need to be defined here because we need the value of the tbl_ var which is set in parent::setup().
		$this->tnAds = $this->tbl_.'scraped_naughtyreviews_ads';
		$this->tnPhotos = $this->tbl_.'scraped_naughtyreviews_photos';
		$this->tnAdPoprox = $this->tbl_.'poprox_naughtyreviews_ads';
		//$this->tnAdInfoBot = $this->tbl_.'machine_naughtyreviews_ads';
		$this->tnAdQueue = str_replace($this->dbConnName,'roxy_scrape',$this->tbl_).'naughtyreviews_queue'; //different db, same connection
	}
	
	public function poproxScore($aAcctId, $aPostVars) {
		$v = &$aPostVars;
		$theUserId = $aAcctId+0;
		$theRoxyId = $v->ad_id+0;
		
		if (!empty($v) && !empty($theRoxyId)) {

			$theParams = array();
			$theParamTypes = array();
			//ensure poprox data exists
			$theSql = 'SELECT ad_id FROM '.$this->tnAdPoprox.' WHERE user_id=:user_id AND ad_id=:roxy_id';
			$theParams['user_id'] = $theUserId;
			$theParamTypes['user_id'] = PDO::PARAM_INT;
			$theParams['roxy_id'] = $theRoxyId;
			$theParamTypes['roxy_id'] = PDO::PARAM_INT;
			$rs = $this->query($theSql,$theParams,$theParamTypes);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			if ($rs->fetch()===false) {
				$theSql = 'INSERT INTO '.$this->tnAdPoprox.' SET user_id=:user_id, ad_id=:roxy_id, score=0';
				$this->execDML($theSql,$theParams,$theParamTypes);
			}
			$rs->closeCursor();

			$theSql = 'UPDATE '.$this->tnAdPoprox.' SET';
			$theSql .= ' score=:score';
			$theSql .= ', user_comment=:user_comment';
			$theSql .= ', title=:title';
			$theSql .= ', ad_text=:ad_text';
			//modify the following fields to match our particular poprox table
			$theSql .= ', city=:city';
			$theSql .= ', city2=:city2';
			$theSql .= ', region=:region';
			$theSql .= ', region2=:region2';
			$theSql .= ', street=:street';
			$theSql .= ', zip=:zip';
			$theSql .= ', country=:country';
			$theSql .= ', url=:url';
			$theSql .= ', overallrating=:overallrating';
			$theSql .= ', performancerating=:performancerating';
			$theSql .= ', appearancerating=:appearancerating';
			$theSql .= ', attituderating=:attituderating';
			$theSql .= ', atmosphererating=:atmosphererating';
			$theSql .= ', daytime:=daytime';
			$theSql .= ', nighttime:=nighttime';
			$theSql .= ', sunday:=sunday';
			$theSql .= ', monday:=monday';
			$theSql .= ', tuesday:=tuesday';
			$theSql .= ', wednesday:=wednesday';
			$theSql .= ', thursday:=thursday';
			$theSql .= ', friday:=friday';
			$theSql .= ', saturday:=saturday';
			$theSql .= ', 30minuterate:=30minuterate';
			$theSql .= ', hourrate:=hourrate';
			$theSql .= ', creditcards:=creditcards';
			$theSql .= ', idverification:=idverification';
			$theSql .= ', age:=age';
			$theSql .= ', gender:=gender';
			$theSql .= ', ethnicity:=ethnicity';
			$theSql .= ', email:=email';
			$theSql .= ', phone:=phone';
			$theSql .= ', phone2:=phone2';
			$theSql .= ', website:=website';
			$theSql .= ', adwebsite:=adwebsite';
			$theSql .= ', eyes=:eyes';
			$theSql .= ', height=:height';
			$theSql .= ', build=:build';
			$theSql .= ', hairtype=:hairtype';
			$theSql .= ', haircolor=:haircolor';
			$theSql .= ', breastsize=:breastsize';
			$theSql .= ', breastcup=:breastcup';
			$theSql .= ', implants=:implants';
			$theSql .= ', smokes=:smokes';
			$theSql .= ', piercings=:piercings';
			$theSql .= ', tattoos=:tattoos';
			$theSql .= ', travel=:travel';
			$theSql .= ', languages=:languages';
			$theSql .= ', grooming=:grooming';
			$theSql .= ', datecheck=:datecheck';
			$theSql .= ', preferred411=:preferred411';
			$theSql .= ', latitude=:latitude';
			$theSql .= ', longitude=:longitude';
			$theSql .= ', alias=:alias';
				
			$theSql .= ' WHERE user_id=:user_id AND ad_id=:roxy_id';
			
			//param values to save
			$theParams['score'] = (!empty($v->poprox_likely))?1:0;
			$theParams['user_comment'] = $v->poprox_comment;
			$theParams['title'] = (!empty($v->poprox_title))?1:0;
			$theParams['ad_text'] = (!empty($v->poprox_ad_text))?1:0;
			//modify the following fields to match our particular poprox table
			$theParams['city'] = (!empty($v->poprox_city))?1:0;
			$theParams['city2'] = (!empty($v->poprox_city2))?1:0;
			$theParams['region'] = (!empty($v->poprox_region))?1:0;
			$theParams['region2'] = (!empty($v->poprox_region2))?1:0;
			$theParams['street'] = (!empty($v->poprox_street))?1:0;
			$theParams['zip'] = (!empty($v->poprox_zip))?1:0;
			$theParams['country'] = (!empty($v->poprox_country))?1:0;
			$theParams['url'] = (!empty($v->poprox_url))?1:0;
			$theParams['overallrating'] = (!empty($v->poprox_overallrating))?1:0;
			$theParams['performancerating'] = (!empty($v->poprox_performancerating))?1:0;
			$theParams['appearancerating'] = (!empty($v->poprox_appearancerating))?1:0;
			$theParams['attituderating'] = (!empty($v->poprox_attituderating))?1:0;
			$theParams['atmosphererating'] = (!empty($v->poprox_atmosphererating))?1:0;
			$theParams['daytime'] = (!empty($v->poprox_daytime))?1:0;
			$theParams['nighttime'] = (!empty($v->poprox_nighttime))?1:0;
			$theParams['sunday'] = (!empty($v->poprox_sunday))?1:0;
			$theParams['monday'] = (!empty($v->poprox_monday))?1:0;
			$theParams['tuesday'] = (!empty($v->poprox_tuesday))?1:0;
			$theParams['wednesday'] = (!empty($v->poprox_wednesday))?1:0;
			$theParams['thursday'] = (!empty($v->poprox_thursday))?1:0;
			$theParams['friday'] = (!empty($v->poprox_friday))?1:0;
			$theParams['saturday'] = (!empty($v->poprox_saturday))?1:0;
			$theParams['30minuterate'] = (!empty($v->poprox_30minuterate))?1:0;
			$theParams['hourrate'] = (!empty($v->poprox_hourrate))?1:0;
			$theParams['creditcards'] = (!empty($v->poprox_creditcards))?1:0;
			$theParams['idverification'] = (!empty($v->poprox_idverification))?1:0;
			$theParams['age'] = (!empty($v->poprox_age))?1:0;
			$theParams['gender'] = (!empty($v->poprox_gender))?1:0;
			$theParams['ethnicity'] = (!empty($v->poprox_ethnicity))?1:0;
			$theParams['email'] = (!empty($v->poprox_email))?1:0;
			$theParams['phone'] = (!empty($v->poprox_phone))?1:0;
			$theParams['phone2'] = (!empty($v->poprox_phone2))?1:0;
			$theParams['website'] = (!empty($v->poprox_website))?1:0;
			$theParams['adwebsite'] = (!empty($v->poprox_adwebsite))?1:0;
			$theParams['eyes'] = (!empty($v->poprox_eyes))?1:0;
			$theParams['height'] = (!empty($v->poprox_height))?1:0;
			$theParams['build'] = (!empty($v->poprox_build))?1:0;
			$theParams['hairtype'] = (!empty($v->poprox_hairtype))?1:0;
			$theParams['haircolor'] = (!empty($v->poprox_haircolor))?1:0;
			$theParams['breastsize'] = (!empty($v->poprox_breastsize))?1:0;
			$theParams['breastcup'] = (!empty($v->poprox_breastcup))?1:0;
			$theParams['implants'] = (!empty($v->poprox_implants))?1:0;
			$theParams['smokes'] = (!empty($v->poprox_smokes))?1:0;
			$theParams['piercings'] = (!empty($v->poprox_piercings))?1:0;
			$theParams['tattoos'] = (!empty($v->poprox_tattoos))?1:0;
			$theParams['travel'] = (!empty($v->poprox_travel))?1:0;
			$theParams['languages'] = (!empty($v->poprox_languages))?1:0;
			$theParams['grooming'] = (!empty($v->poprox_grooming))?1:0;
			$theParams['datecheck'] = (!empty($v->poprox_datecheck))?1:0;
			$theParams['preferred411'] = (!empty($v->poprox_preferred411))?1:0;
			$theParams['latitude'] = (!empty($v->poprox_latitude))?1:0;
			$theParams['longitude'] = (!empty($v->poprox_longitude))?1:0;
			$theParams['alias'] = (!empty($v->poprox_alias))?1:0;

				
			$theParamTypes['score'] = PDO::PARAM_INT;
			$theParamTypes['user_comment'] = PDO::PARAM_STR;
			$theParamTypes['title'] = PDO::PARAM_INT;
			$theParamTypes['ad_text'] = PDO::PARAM_INT;
			//modify the following fields to match our particular poprox table (all will be PDO::PARAM_INT;)
			$theParams['city'] = PDO::PARAM_INT;
			$theParams['city2'] = PDO::PARAM_INT;
			$theParams['region'] = PDO::PARAM_INT;
			$theParams['region2'] = PDO::PARAM_INT;
			$theParams['street'] = PDO::PARAM_INT;
			$theParams['zip'] = PDO::PARAM_INT;
			$theParams['country'] = PDO::PARAM_INT;
			$theParams['url'] = PDO::PARAM_INT;
			$theParams['overallrating'] = PDO::PARAM_INT;
			$theParams['performancerating'] = PDO::PARAM_INT;
			$theParams['appearancerating'] = PDO::PARAM_INT;
			$theParams['attituderating'] = PDO::PARAM_INT;
			$theParams['atmosphererating'] = PDO::PARAM_INT;
			$theParams['daytime'] = PDO::PARAM_INT;
			$theParams['nighttime'] = PDO::PARAM_INT;
			$theParams['sunday'] = PDO::PARAM_INT;
			$theParams['monday'] = PDO::PARAM_INT;
			$theParams['tuesday'] = PDO::PARAM_INT;
			$theParams['wednesday'] = PDO::PARAM_INT;
			$theParams['thursday'] = PDO::PARAM_INT;
			$theParams['friday'] = PDO::PARAM_INT;
			$theParams['saturday'] = PDO::PARAM_INT;
			$theParams['30minuterate'] = PDO::PARAM_INT;
			$theParams['hourrate'] = PDO::PARAM_INT;
			$theParams['creditcards'] = PDO::PARAM_INT;
			$theParams['idverification'] = PDO::PARAM_INT;
			$theParams['age'] = PDO::PARAM_INT;
			$theParams['gender'] = PDO::PARAM_INT;
			$theParams['ethnicity'] = PDO::PARAM_INT;
			$theParams['email'] = PDO::PARAM_INT;
			$theParams['phone'] = PDO::PARAM_INT;
			$theParams['phone2'] = PDO::PARAM_INT;
			$theParams['website'] = PDO::PARAM_INT;
			$theParams['adwebsite'] = PDO::PARAM_INT;
			$theParams['eyes'] = PDO::PARAM_INT;
			$theParams['height'] = PDO::PARAM_INT;
			$theParams['build'] = PDO::PARAM_INT;
			$theParams['hairtype'] = PDO::PARAM_INT;
			$theParams['haircolor'] = PDO::PARAM_INT;
			$theParams['breastsize'] = PDO::PARAM_INT;
			$theParams['breastcup'] = PDO::PARAM_INT;
			$theParams['implants'] = PDO::PARAM_INT;
			$theParams['smokes'] = PDO::PARAM_INT;
			$theParams['piercings'] = PDO::PARAM_INT;
			$theParams['tattoos'] = PDO::PARAM_INT;
			$theParams['travel'] = PDO::PARAM_INT;
			$theParams['languages'] = PDO::PARAM_INT;
			$theParams['grooming'] = PDO::PARAM_INT;
			$theParams['datecheck'] = PDO::PARAM_INT;
			$theParams['preferred411'] = PDO::PARAM_INT;
			$theParams['latitude'] = PDO::PARAM_INT;
			$theParams['longitude'] = PDO::PARAM_INT;
			$theParams['alias'] = PDO::PARAM_INT;

			$this->execDML($theSql,$theParams,$theParamTypes);
		}
	}
	
	protected function getSqlForGeoLocWhere() {
		return 's.url LIKE :loc OR s.body LIKE :ad_text OR s.city LIKE :region';
	}
	
	protected function getSqlForPhoneAdList() {
		$theSql = 'SELECT s.id as roxy_id, "'.$this->site_id.'" as site_id, s.url, s.phone';
		$theSql .= ' FROM '.$this->tnAds.' as s';
		$theSql .= ' WHERE s.phone LIKE :phone';
		$theSql2 = 'SELECT s.id as roxy_id, "'.$this->site_id.'" as site_id, s.url, s.phone2 as phone';
		$theSql2 .= ' FROM '.$this->tnAds.' as s';
		$theSql2 .= ' WHERE s.phone2 LIKE :phone';
		
		return '('.$theSql.') UNION ('.$theSql2.')';
	}
	
	
}//end class

}//end namespace
