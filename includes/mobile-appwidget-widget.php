<?php

class MobileAppWidget_Widget extends WP_Widget{
	protected $campaignAppObj = null;
	
	function MobileAppWidget_Widget()
	{
		global $wpdb;
    
		$this->campaignAppObj = new CampaignsAppClass($wpdb);
	
		$widget_ops = array('classname' => 'MobileAppWidget_Widget', 'description' => 'Promote mobile apps in your sidebar' );
		$this->WP_Widget('MobileAppWidget_Widget', 'Mobile AppWidget', $widget_ops);
	
	}
 
	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'selectedCampaign' => 0) );
		$title = $instance['title'];
		$campaignId = $instance['selectedCampaign'];
	?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Title: 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('selectedCampaign'); ?>">Campaign: 
			<select name="<?php echo $this->get_field_name('selectedCampaign'); ?>" id="<?php echo $this->get_field_id('selectedCampaign'); ?>" class="widefat" >
			<?php
			$results = $this->campaignAppObj->getCampaigns(); 
			foreach ($results as $c){
				$selected = '';
				if (esc_attr($campaignId) == $c['id']){
					$selected = ' selected = "selected"';
				}
			?>
				<option value="<?php echo($c['id']); ?>" <?php echo($selected); ?>><?php echo($c['name']); ?></option>
			<?php
			}
			?>
			</select>
		</label>
	</p>	
	<?php
	}
 
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['selectedCampaign'] = $new_instance['selectedCampaign'];
		return $instance;
	}
 
	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);
 
		echo $before_widget;
	
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$campaignId = empty($instance['selectedCampaign']) ? -1 : $instance['selectedCampaign'];
 
		$campaign = $this->campaignAppObj->getCampaignById($campaignId);
	
		// check if campaign is still available
		$start_day = strtotime($campaign['start_day']);
		$end_day = strtotime($campaign['end_day']);
	
		$now = time(); $showAd = true;
		if (($start_day <= $now) && ($end_day >= $now)){
			if ($campaign['status'] != 'started'){
				$showAd = false;
			}
			
			// views
			if ($showAd && $campaign['total_views'] != 999999999){
				$currentViews = $this->campaignAppObj->getTotalViews($campaignId);
			
				if ($currentViews >= $campaign['total_views']){
					$showAd = false;
					//echo('a');
				}
			}
		
			if ($showAd && $campaign['daily_views'] != 999999999){
				$currentTodayViews = $this->campaignAppObj->getTotalViews($campaignId, true);

				if ($currentTodayViews >= $campaign['daily_views']){
					$showAd = false;			
					//echo('b');
				}
			}
		
			// clicks
			if ($showAd && $campaign['total_clicks'] != 9999999){
				$currentClicks = $this->campaignAppObj->getTotalClicks($campaignId);
			
				if ($currentClicks >= $campaign['total_clicks']){
					$showAd = false;
					//echo('c');
				}
			}
		
			if ($showAd && $campaign['daily_clicks'] != 9999999){
				$currentTodayClicks = $this->campaignAppObj->getTotalClicks($campaignId, true);

				if ($currentTodayClicks >= $campaign['daily_clicks']){
					$showAd = false;			
					//echo('d');
				}
			}		
		}else{
			$showAd = false;
			//echo('e');
		}
	
		// select ad if is ok above
		$addToShow = null;
		if ($showAd == true){
			$campaignApps = $this->campaignAppObj->getCampaignApps($campaignId, 'started');
		
			if ((is_array($campaignApps)) && (count($campaignApps) > 0)){
				$keys = array_keys($campaignApps);
				$rndKey = $keys[rand(0, (count($keys)-1))];
				$addToShow = $campaignApps[$rndKey];
			}
		}else{
			//echo('no add for you');
		}
	
	if ($addToShow != null){
		// register view
		global $wpdb;
		
		$mobileAppObj = new MobileAppClass($wpdb);
		$urls = $mobileAppObj->getDownloadLinks($addToShow['id']);
		
		$this->campaignAppObj->addView($campaignId, $addToShow['id']);
		if (!empty($title))
		  echo $before_title . $title . $after_title;;
	 
		// WIDGET CODE GOES HERE
		
		$price = 'FREE';
		if ($addToShow['price'] > 0){
			$price = $addToShow['price'].' '.$addToShow['currency'];
		}
		echo(
		'<div class="widget_mobile_app" id="widget_mobile_app">
			<ul class="appwidget_adslist">
				<li class="appwidget_content">
					<div class="appwidget_application_title">'.$addToShow['title'].'</div>
					<div class="appwidget_price">'.$price.'</div>
					
					<div style="clear:both; float:left; display: block; width: 100%;">
						<a href="" title="'.$addToShow['title'].'" class="appwidget_thumbnail">
							<img src="'.$addToShow['thumb'].'" alt="'.$addToShow['title'].'" height="100" width="100">	
						</a>

						<div class="appwidget_description">'.$addToShow['description'].'</div>
					</div>
					<div style="clear:both; float:left; display: block; width: 100%;">
		');
		
		//var_dump($urls);
		$urlAddon = '&mobile-appwidget-redirect=1&cid='.$campaignId.'&i='.$addToShow['id'];
		if (isset($urls['android'])){
			echo('<a href="'.network_site_url( '/' ).'?url='.$urls['android'].$urlAddon.'" target="_blank" class="appwidget_btn btn-android">Android</a>');
		}
		if (isset($urls['ios'])){
			echo('<a href="'.network_site_url( '/' ).'?url='.$urls['ios'].$urlAddon.'" target="_blank" class="appwidget_btn btn-ios">iOS</a>');
		}
		if (isset($urls['windows'])){
			echo('<a href="'.network_site_url( '/' ).'?url='.$urls['windows'].$urlAddon.'" target="_blank" class="appwidget_btn btn-windows">Windows</a>');
		}		
		echo('
					</div>
				</li>
				<li class="appwidget_adsby">
					ads by <a href="http://en.nisi.ro/blog/" title="Mobile appWidget">Mobile appWidget</a>
				</li>
		</div>'		
		);
	 
	 //		'<a href="'.get_site_url().'/wp-content/plugins/mobile-appwidget/redirect.php?app=1&url=ios" target="_blank">Press me.</a>'

		echo $after_widget;
	}
  }
 
}
