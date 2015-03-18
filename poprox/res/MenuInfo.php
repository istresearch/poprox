<?php
namespace BitsTheater\res;
use BitsTheater\res\BitsMenuInfo as BaseResources;
use BitsTheater\costumes\MenuItemResEntry;
{//begin namespace

class MenuInfo extends BaseResources {
	//app-specific menu items
	public $menu_item_poprox;
	public $menu_item_poprox_redbook;
	public $menu_item_poprox_backpage;
	public $menu_item_poprox_myproviderguide;
	public $menu_item_poprox_craigslist;
	public $menu_item_poprox_naughtyreviews;
	
	public $menu_item_mtask;
	public $menu_item_mtask_phone;
	public $menu_item_mtask_photo;
	
	public $menu_item_qrox;
	public $menu_item_qrox_search;
	public $menu_item_qrox_phone_count;
	public $menu_item_qrox_bp_region;
	public $menu_item_qtask_phone;
	
	public $menu_item_view;
	public $menu_item_view_stats;
	public $menu_item_view_monitoring;
	public $menu_item_view_ads;
	public $menu_item_view_reviews;
	
	public $menu_item_view_dash_ingest;
	public $menu_item_view_dash_spider;
	
	
	//menus containing menu items
	public $menu_poprox;
	public $menu_mtask;
	public $menu_qrox;
	public $menu_view;
	
	public function setup($aDirector) {
		parent::setup($aDirector);
		//strings that require concatination need to be defined during setup()
		
		$this->menu_item_poprox = MenuItemResEntry::makeEntry($aDirector,'poprox')
				->label('&res@roxy/menu_label_poprox')
				->filter('&right@roxy/poprox')
				->hasSubmenu(true)
				->gone(true)
		;
		$this->menu_item_poprox_redbook = MenuItemResEntry::makeEntry($aDirector,'poprox_redbook')
				->link(BITS_URL.'/poprox/rb/ads')
				->filter('&right@roxy/poprox')
				->label('myRedBook')
				->gone(true)
		;
		$this->menu_item_poprox_backpage = MenuItemResEntry::makeEntry($aDirector,'poprox_backpage')
				->link(BITS_URL.'/poprox/backpage/ads')
				->filter('&right@roxy/poprox')
				->label('backpage')
				->gone(true)
		;
		$this->menu_item_poprox_myproviderguide = MenuItemResEntry::makeEntry($aDirector,'poprox_myproviderguide')
				->link(BITS_URL.'/poprox/myproviderguide/ads')
				->filter('&right@roxy/poprox')
				->label('My Provider Guide')
				->gone(true)
		;
		$this->menu_item_poprox_craigslist = MenuItemResEntry::makeEntry($aDirector,'poprox_craigslist')
				->link(BITS_URL.'/poprox/craigslist/ads')
				->filter('&right@roxy/poprox')
				->label('Craigslist')
				->gone(true)
		;
		$this->menu_item_poprox_naughtyreviews = MenuItemResEntry::makeEntry($aDirector,'poprox_naughtyreviews')
				->link(BITS_URL.'/poprox/naughtyreviews/ads')
				->filter('&right@roxy/poprox')
				->label('Naughty Reviews')
				->gone(true)
		;
		
		$this->menu_item_mtask = MenuItemResEntry::makeEntry($aDirector,'mtask')
				->label('&res@roxy/menu_label_mtask')
				->filter('&right@roxy/mtask')
				->hasSubmenu(true)
		;
		$this->menu_item_mtask_phone = MenuItemResEntry::makeEntry($aDirector,'mtask_phone')
				->link(BITS_URL.'/mtask/phone')
				->label('&res@roxy/menu_label_mtask_phone')
				->filter('&right@roxy/mtask')
		;
		$this->menu_item_mtask_photo = MenuItemResEntry::makeEntry($aDirector,'mtask_photo')
				->link(BITS_URL.'/mtask/photo')
				->label('&res@roxy/menu_label_mtask_photo')
				->filter('&right@roxy/mtask')
				->gone(true)
		;
		$this->menu_item_qrox = MenuItemResEntry::makeEntry($aDirector,'qrox')
				//->link(BITS_URL.'/qrox/roxy')
				->label('&res@roxy/menu_label_qrox')
				//->filter('&right@roxy/run_reports')
				->hasSubmenu(true)
		;
		$this->menu_item_qrox_search = MenuItemResEntry::makeEntry($aDirector,'qrox_search')
				->link(BITS_URL.'/qrox/search')
				->label('&res@roxy/menu_label_qrox_search')
				->filter('&right@roxy/run_reports')
		;
		$this->menu_item_qrox_phone_list = MenuItemResEntry::makeEntry($aDirector,'qrox_phone_list')
				->link(BITS_URL.'/qrox/phone_list')
				->label('&res@roxy/menu_label_qrox_phone_list')
				->filter('&right@roxy/run_reports')
		;
		$this->menu_item_qrox_bp_region = MenuItemResEntry::makeEntry($aDirector,'qrox_bp_region')
				->link(BITS_URL.'/qrox/bp_region')
				->label('&res@roxy/menu_label_qrox_bp_region')
				->filter('&right@roxy/run_reports')
		;
		$this->menu_item_qtask_phone = MenuItemResEntry::makeEntry($aDirector,'qtask_phone')
				->link(BITS_URL.'/qrox/qtask_phone')
				->label('&res@roxy/menu_label_qtask_phone')
				->filter('&right@roxy/run_reports')
		;
		
		$this->menu_item_view = MenuItemResEntry::makeEntry($aDirector,'view')
				->label('&res@roxy/menu_label_view')
				->hasSubmenu(true)
		;
		$this->menu_item_view_stats = MenuItemResEntry::makeEntry($aDirector,'view_stats')
				->link(BITS_URL.'/stats/view')
				->label('&res@roxy/menu_label_view_stats')
				->filter('&right@roxy/dashboard')
		;
		$this->menu_item_view_monitoring = MenuItemResEntry::makeEntry($aDirector,'view_monitoring')
				->link(BITS_URL.'/stats/monitoring')
				->label('&res@roxy/menu_label_view_monitoring')
				->filter('&right@roxy/monitoring')
		;
		$this->menu_item_view_dash_ingest = MenuItemResEntry::makeEntry($aDirector,'dash_ingest')
				->link(BITS_URL.'/stats/ingest')
				->label('&res@roxy/menu_label_dash_ingest')
				->filter('&right@roxy/dashboard')
				->gone(true)
		;
		$this->menu_item_view_dash_spider = MenuItemResEntry::makeEntry($aDirector,'dash_spider')
				->link(BITS_URL.'/stats/spider')
				->label('&res@roxy/menu_label_dash_spider')
				->filter('&right@roxy/dashboard')
				->gone(true)
		;
		
		$this->menu_item_view_ads = MenuItemResEntry::makeEntry($aDirector,'view_ads')
				->link(BITS_URL.'/ads/view')
				->label('&res@roxy/menu_label_view_ads')
				->filter('&right@roxy/view_data')
		;
		$this->menu_item_view_reviews = MenuItemResEntry::makeEntry($aDirector,'view_reviews')
				->link(BITS_URL.'/reviews/view')
				->label('&res@roxy/menu_label_view_reviews')
				->filter('&right@roxy/view_data')
				->gone(true)
		;
		
		//app menu defined here so that updates to main program will not affect derived menus
		$this->menu_item_home->icon(''); //do not want an icon on Home menu
		$this->menu_main = array(
				'home' => $this->menu_item_home,
				'view' => $this->menu_item_view,
				'poprox' => $this->menu_item_poprox,
				'mtask' => $this->menu_item_mtask,
				'qrox' => $this->menu_item_qrox,
				'account' => $this->menu_item_account,
				'admin' => $this->menu_item_admin,
		);
		
		$this->menu_poprox = array(
				'poprox_redbook' => $this->menu_item_poprox_redbook,
				'poprox_backpage' => $this->menu_item_poprox_backpage,
				'poprox_myproviderguide' => $this->menu_item_poprox_myproviderguide,
				'poprox_craigslist' => $this->menu_item_poprox_craigslist,
				'poprox_naughtyreviews' => $this->menu_item_poprox_naughtyreviews,
		);
		
		$this->menu_mtask = array(
				'mtask_phone' => $this->menu_item_mtask_phone,
				'mtask_photo' => $this->menu_item_mtask_photo,
		);
		
		$this->menu_view = array(
				'view_stats' => $this->menu_item_view_stats,
				'view_monitoring' => $this->menu_item_view_monitoring,
				'view_ads' => $this->menu_item_view_ads,
				'view_reviews' => $this->menu_item_view_reviews,
				//'view_stats_dash_ingest' => $this->menu_item_view_dash_ingest, //do not want to use real-time stats
				//'view_stats_dash_spider' => $this->menu_item_view_dash_spider, //merged into view_monitoring
		);
		
		$this->menu_qrox = array(
				//'qrox_search' => $this->menu_item_qrox_search,
				'qrox_phone_list' => $this->menu_item_qrox_phone_list,
				'qrox_bp_region' => $this->menu_item_qrox_bp_region,
				'qtask_phone' => $this->menu_item_qtask_phone,
		);
				
	}
	
}//end class

}//end namespace
