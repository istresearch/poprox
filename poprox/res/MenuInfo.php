<?php
/*
 * Copyright (C) 2012 Blackmoon Info Tech Services
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace BitsTheater\res;
{//begin namespace

class MenuInfo extends MenuInfoBase {
	//menu items
	public $menu_item_poprox;
	
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
		
		$this->menu_item_poprox = array(
				//'filter' => '&right@roxy/poprox',
				'filter' => '&false', //removing old schema hot-or-not
				'label'=>'PopRox',
				'hasSubmenu' => true,
		);
		$this->menu_item_mtask = array(
				'filter' => '&right@roxy/mtask',
				'label' => 'Microtask',
				'hasSubmenu' => true,
		);
		$this->menu_item_mtask_phone = array(
				'link' => BITS_URL.'/mtask/phone',
				'filter' => '&right@roxy/mtask',
				'label'=>'Phone',
		);
		$this->menu_item_mtask_photo = array(
				'link' => BITS_URL.'/mtask/photo',
				'filter' => '&false',
				'label'=>'Photo',
		);
		$this->menu_item_qrox = array(
				//'link' => BITS_URL.'/qrox/roxy',
				//'filter' => '&right@roxy/run_reports',
				'label'=>'Reports',
				'hasSubmenu' => true,
		);
		$this->menu_item_qrox_search = array(
				'link' => BITS_URL.'/qrox/search',
				'filter' => '&right@roxy/run_reports',
				'label'=>'Ad Search',
		);
		$this->menu_item_qrox_phone_list = array(
				'link' => BITS_URL.'/qrox/phone_list',
				'filter' => '&right@roxy/run_reports',
				'label'=>'Phone List',
		);
		$this->menu_item_qrox_bp_region = array(
				'link' => BITS_URL.'/qrox/bp_region',
				'filter' => '&right@roxy/run_reports',
				'label'=>'Backpage Ads By Week',
		);
		$this->menu_item_qtask_phone = array(
				'link' => BITS_URL.'/qrox/qtask_phone',
				'filter' => '&right@roxy/run_reports',
				'label'=>'Phone Microtask Results',
		);
		$this->menu_item_view = array(
				//'filter' => '&right@roxy/poprox',
				'label' => 'View',
				'hasSubmenu' => true,
		);
		$this->menu_item_view_stats = array(
				'link' => BITS_URL.'/stats/view',
				'filter' => '&right@roxy/dashboard',
				'label'=>'Statistics',
				//'hasSubmenu' => true,
		);
		$this->menu_item_view_monitoring = array(
				'link' => BITS_URL.'/stats/monitoring',
				'filter' => '&right@roxy/monitoring',
				'label'=>'Monitoring',
		);
		$this->menu_item_view_ads = array(
				'link' => BITS_URL.'/ads/view',
				'filter' => '&right@roxy/view_data',
				'label'=>'Ads',
		);
		$this->menu_item_view_reviews = array(
				'link' => BITS_URL.'/reviews/view',
				'filter' => '&false',
				'label'=>'Reviews',
		);
		$this->menu_item_view_dash_ingest = array(
				'link' => BITS_URL.'/stats/ingest',
				'filter' => '&false',
				'label'=>'Ingest Stats',
		);
		$this->menu_item_view_dash_spider = array(
				'link' => BITS_URL.'/stats/spider',
				'filter' => '&false',
				'label'=>'Spiders',
		);
		
		//app menu defined here so that updates to main program will not affect derived menus
		$this->menu_item_home['icon'] = ''; //do not want an icon on Home menu
		$this->menu_main = array( //no link defined means submenu is defined as $menu_%name%
				'home' => $this->menu_item_home,
				'view' => $this->menu_item_view,
				'poprox' => $this->menu_item_poprox,
				'mtask' => $this->menu_item_mtask,
				'qrox' => $this->menu_item_qrox,
				'account' => $this->menu_item_account,
				'admin' => $this->menu_item_admin,
		);
		
		$this->menu_poprox = array(
				'poprox_redbook' => array(
						'link' => BITS_URL.'/poprox/rb/ads',
						'filter' => '&right@roxy/poprox',
						'label'=>'myRedBook',
				),
				'poprox_backpage' => array(
						'link' => BITS_URL.'/poprox/backpage/ads',
						'filter' => '&right@roxy/poprox',
						'label'=>'backpage',
				),
				'poprox_myproviderguide' => array(
						'link' => BITS_URL.'/poprox/myproviderguide/ads',
						'filter' => '&right@roxy/poprox',
						'label'=>'My Provider Guide',
				),
				'poprox_craigslist' => array(
						'link' => BITS_URL.'/poprox/craigslist/ads',
						'filter' => '&right@roxy/poprox',
						'label'=>'Craigslist',
				),
				'poprox_naughtyreviews' => array(
						'link' => BITS_URL.'/poprox/naughtyreviews/ads',
						'filter' => '&right@roxy/poprox',
						'label'=>'Naughty Reviews',
				),
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
