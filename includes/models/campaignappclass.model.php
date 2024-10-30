<?php
class CampaignsAppClass{
	private $db;
	public function __construct($dbExt) {
		$this->db = $dbExt;
	}
	
	public function getCampaigns(){
		$campaigns = $this->db->get_results( '
					SELECT id, name, start_day, end_day, status FROM '.$this->db->prefix.TB_CAMPAIGNS
					, ARRAY_A );
					
		foreach($campaigns as $key => $item){
			$campaigns[$key]['clicks'] = $this->getTotalClicks($item['id']);
			$campaigns[$key]['views'] = $this->getTotalViews($item['id']);
		}
		
		return $campaigns;
	}
	
	public function getCampaignById($campaignId){
		$app = $this->db->get_results( '
						SELECT *
						FROM '.$this->db->prefix.TB_CAMPAIGNS.'
						WHERE `id` = "'.$campaignId.'" LIMIT 1;', ARRAY_A );

		if (isset($app[0]))
			return $app[0];
		
		return array();	
	}
	
	public function setCampaignStatus($campaignId, $status='paused'){
		if ($status == 'start')
			$status = 'started';
		else if ($status == 'pause')
			$status = 'paused';
			
		if (($status != 'started') && ($status != 'paused'))
			$status = 'paused';
 			
		$this->db->update( 
				$this->db->prefix.TB_CAMPAIGNS, 
				array( 
					'status' => $status, 
				), 
				array('id' => $campaignId),
				array( 
					'%s', 
				),
				array('%d')
			);	
	}
	
	public function getCampaignApps($campaignId){
		$tmp = $this->db->get_results( '
					SELECT `ca`.*, `a`.* FROM '.$this->db->prefix.TB_CAMPAIGNS_APPS.' as ca 
					JOIN '.$this->db->prefix.TB_APPSLIST.' AS a ON (ca.app_id = a.id)
					WHERE `campaign_id` = '.$campaignId
					, ARRAY_A );

		$final = array();
		foreach ($tmp as $item){
			$final[$item['app_id']] = $item;
		}
		
		return $final;
	}
	
	public function getTotalClicks($id, $today = false){
		$checkToday = '';
		if ($today == true){
			$checkToday = ' AND `date` = date(NOW())';
		}
		
		$tmp = $this->db->get_results( '
					SELECT COALESCE(SUM(`clicks`),0) AS totalClicks FROM '.$this->db->prefix.TB_STATS.' WHERE `campaign_id` = '.$id.' '.$checkToday
					, ARRAY_A );

		if (isset($tmp[0]))
			return $tmp[0]['totalClicks'];

		return 0;
	}
	
	public function getTotalViews($id, $today = false){
		$checkToday = '';
		if ($today == true){
			$checkToday = ' AND `date` = date(NOW())';
		}
	
		$tmp = $this->db->get_results( '
					SELECT COALESCE(SUM(`views`),0) AS totalViews FROM '.$this->db->prefix.TB_STATS.' WHERE `campaign_id` = '.$id.' '.$checkToday
					, ARRAY_A );
				
		if (isset($tmp[0]))
			return $tmp[0]['totalViews'];
			
		return 0;	
	}
	
	public function addView($campaignId, $appId){
		$this->db->query('INSERT INTO '.$this->db->prefix.TB_STATS.' SET
				`app_id` = "'.$appId.'",
				`campaign_id` = "'.$campaignId.'",
				`date` = "'.date('Y-m-d H:i:s').'",
				`clicks` = 0,
				`views` = 1
				ON DUPLICATE KEY UPDATE `views` = `views` + 1;');
	}

	public function addClick($campaignId, $appId){
		$this->db->query('INSERT INTO '.$this->db->prefix.TB_STATS.' SET
				`app_id` = "'.$appId.'",
				`campaign_id` = "'.$campaignId.'",
				`date` = "'.date('Y-m-d H:i:s').'",
				`clicks` = 1,
				`views` = 1
				ON DUPLICATE KEY UPDATE `clicks` = `clicks` + 1;');
	}	
	
	public function saveNewCampaign($post){
		$maxClicks = (isset($post['maxClicks']) && (is_numeric($post['maxClicks'])))?$post['maxClicks']:9999999;
		$dailyMaxClicks = (isset($post['dailyMaxClicks']) && (is_numeric($post['dailyMaxClicks'])))?$post['dailyMaxClicks']:9999999;
		
		$maxViews = (isset($post['maxViews']) && (is_numeric($post['maxViews'])))?$post['maxViews']:999999999;
		$dailyMaxViews = (isset($post['dailyMaxViews']) && (is_numeric($post['dailyMaxViews'])))?$post['dailyMaxViews']:999999999;
		
		$endDay = (isset($post['end_day']) && ($post['end_day'] != ''))?$post['end_day']:'2038-01-01 00:00:00';

		// do the insert
		$this->db->insert( 
			$this->db->prefix.TB_CAMPAIGNS, 
			array( 
				'name' => $post['name'], 
				'template_id' => '1',
				'total_clicks' => $maxClicks,
				'daily_clicks' => $dailyMaxClicks,
				'total_views' => $maxViews,
				'daily_views' => $dailyMaxViews,
				'start_day' => $post['start_day'],
				'end_day' => $endDay,
				'status' => 'paused',
			), 
			array( 
				'%s', 
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',					
			) 
		);
		
		$this->updateApps($this->db->insert_id, $post['apps']);

		return true;
	}
	
	public function updateApps($campaignId, $apps){
		$notIn = array();
		foreach($apps as $key => $app){
			$this->db->query('INSERT INTO '.$this->db->prefix.TB_CAMPAIGNS_APPS.' SET
				`app_id` = "'.$key.'",
				`campaign_id` = "'.$campaignId.'"
				ON DUPLICATE KEY UPDATE `app_id` = `app_id`;');
				
			$notIn[] = $key;
		}
		
		$this->db->query('DELETE FROM '.$this->db->prefix.TB_CAMPAIGNS_APPS.' WHERE `campaign_id` = '.$campaignId.' AND `app_id` NOT IN ('.implode(',', $notIn).')');
	}
	
	public function updateCampaign($post){
		if (!isset($post['campaignId'])){
			return false;
		}
		
		$maxClicks = (isset($post['maxClicks']) && (is_numeric($post['maxClicks'])))?$post['maxClicks']:9999999;
		$dailyMaxClicks = (isset($post['dailyMaxClicks']) && (is_numeric($post['dailyMaxClicks'])))?$post['dailyMaxClicks']:9999999;
		
		$maxViews = (isset($post['maxViews']) && (is_numeric($post['maxViews'])))?$post['maxViews']:999999999;
		$dailyMaxViews = (isset($post['dailyMaxViews']) && (is_numeric($post['dailyMaxViews'])))?$post['dailyMaxViews']:999999999;
		
		$endDay = (isset($post['end_day']) && ($post['end_day'] != ''))?$post['end_day']:'2038-01-01 00:00:00';		

		$this->db->update( 
			$this->db->prefix.TB_CAMPAIGNS, 
			array( 
				'name' => $post['name'], 
				'template_id' => '1',
				'total_clicks' => $maxClicks,
				'daily_clicks' => $dailyMaxClicks,
				'total_views' => $maxViews,
				'daily_views' => $dailyMaxViews,
				'start_day' => $post['start_day'],
				'end_day' => $endDay,
				'status' => 'paused',
			),
			array('id' => $post['campaignId']),			
			array( 
				'%s', 
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',					
			),
			array('%d')
		);

		$this->updateApps($post['campaignId'], $post['apps']);

		return true;
	}
	
	public function deleteCampaign($campaignId){
		$this->db->delete( $this->db->prefix.TB_CAMPAIGNS, array( 'id' => $campaignId ) );
		$this->db->delete( $this->db->prefix.TB_CAMPAIGNS_APPS, array( 'campaign_id' => $campaignId ) );
		$this->db->delete( $this->db->prefix.TB_STATS, array( 'campaign_id' => $campaignId ) );
	}
}