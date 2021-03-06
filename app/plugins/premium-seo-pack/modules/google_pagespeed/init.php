<?php
/*
* Define class pspPageSpeedInsights
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('pspPageSpeedInsights') != true) {
    class pspPageSpeedInsights
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $psp;

        	$this->the_plugin = $psp;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/google_pagespeed/';

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}
			
			// load the ajax helper
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/google_pagespeed/ajax.php' );
			new pspPageSpeedInsightsAjax( $this->the_plugin );
        }

		/**
	    * Hooks
	    */
	    static public function adminMenu()
	    {
	       self::getInstance()
	    		->_registerAdminPages();
	    }

	    /**
	    * Register plug-in module admin pages and menus
	    */
		protected function _registerAdminPages()
    	{
    		add_submenu_page(
    			$this->the_plugin->alias,
    			$this->the_plugin->alias . " " . __('PageSpeed Insights', $this->the_plugin->localizationName),
	            __('PageSpeed Insights', $this->the_plugin->localizationName),
	            'manage_options',
	            $this->the_plugin->alias . "_PageSpeedInsights",
	            array($this, 'display_index_page')
	        );

			return $this;
		}

		public function display_meta_box()
		{
			$this->printBoxInterface();
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
?>
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='screen' />
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="psp-wrapper" class="fluid wrapper-psp">
			<?php
			// show the top menu
			pspAdminMenu::getInstance()->make_active('monitoring')->show_menu();
			?>
			
			<!-- Page detail -->
			<div id="psp-pagespeed-detail">
				<div id="psp-pagespeed-ajaxresponse"></div>
			</div>
				
			<!-- Main loading box -->
			<div id="psp-main-loading">
				<div id="psp-loading-overlay"></div>
				<div id="psp-loading-box">
					<div class="psp-loading-text"><?php _e('Loading', $this->the_plugin->localizationName);?></div>
					<div class="psp-meter psp-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>

			<!-- Header -->
			<div id="psp-header">

				<div id="psp-header-bottom">
					<!-- Container -->
					<div class="psp-container clearfix"></div>
				</div>
			</div>

			<!-- Content -->
			<div id="psp-content">

				<!-- Container -->
				<div class="psp-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="psp-content-wrap" class="clearfix">

						<!-- Content Area -->
						<div id="psp-content-area">
							<div class="psp-grid_4">
	                        	<div class="psp-panel">
	                        		<div class="psp-panel-header">
										<span class="psp-panel-title">
											<?php _e('Analyze your website with PageSpeed', $this->the_plugin->localizationName);?>
										</span>
									</div>
									<div class="psp-panel-content">
										<form class="psp-form" id="1" action="#save_with_ajax">
											<div class="psp-form-row psp-table-ajax-list" id="psp-table-ajax-response">
											<?php
											$settings = $this->the_plugin->getAllSettings( 'array', 'pagespeed' );
											$attrs = array(
												'id' 				=> 'pspPageSpeed',
												'show_header' 		=> true,
												'items_per_page' 	=> '10',
												'post_statuses' 	=> 'all',
												'columns'			=> array(
													'checkbox'	=> array(
														'th'	=>  'checkbox',
														'td'	=>  'checkbox',
													),

													'id'		=> array(
														'th'	=> __('ID', $this->the_plugin->localizationName),
														'td'	=> '%ID%',
														'width' => '40'
													),

													'title'		=> array(
														'th'	=> __('Title', $this->the_plugin->localizationName),
														'td'	=> '%title%',
														'align' => 'left'
													),

													'page_speed_desktop_score'	=> array(
														'th'	=> __('Desktop Score', $this->the_plugin->localizationName),
														'td'	=> '%desktop_score%',
														'width' => '130',
														'css' 	=> array(
															'padding' => '0px',
															'background' => '#fcfcfc'
														),
														'class' => 'psp_the_desktop_score'
													),
													
													'page_speed_mobile_score'	=> array(
														'th'	=> __('Mobile Score', $this->the_plugin->localizationName),
														'td'	=> '%mobile_score%',
														'width' => '130',
														'css' 	=> array(
															'padding' => '0px',
															'background' => '#fcfcfc'
														),
														'class' => 'psp_the_mobile_score'
													),
													
													'page_speed_details'	=> array(
														'th'	=> __('View report', $this->the_plugin->localizationName),
														'td'	=> '%button%',
														'option' => array(
															'value' => __('View Report', $this->the_plugin->localizationName),
															'action' => 'do_item_view_report',
															'color' => 'blue'
														),
														'width' => '80'
													),
													
													/*
													'last_check_date'		=> array(
														'th'	=> __('Last Check date', $this->the_plugin->localizationName),
														'td'	=> '%last_check_date%',
														'width' => '160'
													),
													*/
													
													'optimize_btn' => array(
														'th'	=> __('Action', $this->the_plugin->localizationName),
														'td'	=> '%button%',
														'option' => array(
															'value' => __('Test PageSpeed', $this->the_plugin->localizationName),
															'action' => 'do_item_pagespeed_test',
															'color' => 'orange'
														),
														'width' => '80'
													),
												),
												'mass_actions' 	=> array(
														'speed_test_mass' => array(
															'value' => __('Mass PageSpeed test', $this->the_plugin->localizationName),
															'action' => 'do_speed_test_mass',
															'color' => 'blue'
														)
													)
											);
											
											// if report type not both 
											if( isset($settings['report_type']) && $settings['report_type'] != "both" ){
												$removeWhat = 'desktop';
												if( $settings['report_type'] == 'desktop' ){
													$removeWhat = 'mobile';
												}
												unset($attrs['columns']['page_speed_' . ( $removeWhat ) . '_score']);
											}
												
											pspAjaxListTable::getInstance( $this->the_plugin )
												->setup( $attrs )
												->print_html();
								            ?>
								            </div>
							            </form>
				            		</div>
								</div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
		}
		
		/**
	    * Singleton pattern
	    *
	    * @return pspPageSpeedInsights Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
    }
}

// Initialize the pspPageSpeedInsights class
$pspPageSpeedInsights = new pspPageSpeedInsights($this->cfg, ( isset($module) ? $module : array()) );