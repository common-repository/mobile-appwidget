<?php
class MobileAppClass{
	private $db;
	public function __construct($dbExt) {
		$this->db = $dbExt;
	}
	
	public function getAllMobileApplications(){
		return $this->db->get_results( '
					SELECT al.*, dl.name as devName FROM '.$this->db->prefix.TB_APPSLIST.' AS al
					LEFT JOIN '.$this->db->prefix.TB_DEVLIST.' AS dl ON (al.dev_id = dl.id)', ARRAY_A );
	}
   
	public function getAppById($id){
		$app = $this->db->get_results( '
						SELECT al.*, dl.name as dev_name
						FROM '.$this->db->prefix.TB_APPSLIST.' AS al
						JOIN '.$this->db->prefix.TB_DEVLIST.' AS dl ON (al.dev_id = dl.id)
						WHERE `al`.`id` = "'.$id.'" LIMIT 1;', ARRAY_A );

		if (isset($app[0]))
			return $app[0];
		
		return array();
	}
	
	public function saveNewApplication($post){
		
		$devId = $post['authorId'];
		if (0 == $devId){
			$devId = $this->addNewDev($post['author']);
		}
		
		if ($devId > 0){
			$this->db->insert( 
				$this->db->prefix.TB_APPSLIST, 
				array( 
					'dev_id' => $devId, 
					'title' => $post['title'],
					'description' => $post['description'],
					'price' => $post['price'],
					'currency' => $post['currency'],
					'thumb' => $post['upload_image'],
				), 
				array( 
					'%d', 
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
				) 
			);
			
			$this->updateDownloadUrls($this->db->insert_id, $this->getPostUrls($post));
			return true;
		}
		
		return false;
	}
	
	public function updateApplication($post){
		if (!isset($post['appId'])){
			return false;
		}
	
		$devId = $post['authorId'];
		if (0 == $devId){
			$devId = $this->addNewDev($post['author']);
		}
		
		if ($devId > 0){
			$this->db->update( 
				$this->db->prefix.TB_APPSLIST, 
				array( 
					'dev_id' => $devId, 
					'title' => $post['title'],
					'description' => $post['description'],
					'price' => $post['price'],
					'currency' => $post['currency'],
					'thumb' => $post['upload_image'],
				), 
				array('id' => $post['appId']),
				array( 
					'%d', 
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
				),
				array('%d')
			);
			
			$this->updateDownloadUrls($post['appId'], $this->getPostUrls($post));
			
			return true;
		}
		
		return false;
	}
	
	public function getDownloadLinks($appId){
		$app = $this->db->get_results( '
						SELECT `platform`, `url`
						FROM '.$this->db->prefix.TB_DOWNLOADURLS.' 
						WHERE `app_id` = "'.$appId.'";', ARRAY_A );

		$links = array();
		foreach($app as $item){
			if (strlen($item['url']) > 0)
				$links[$item['platform']] = $item['url'];
		}
		unset($app);
		return $links;
	}
	
	public function getPostUrls($post){
		$urls = array();
		
		if (isset($post['ios_url']) && (strlen($post['ios_url']) > 0))
			$urls['ios'] = $post['ios_url'];
			
		if (isset($post['android_url']) && (strlen($post['android_url']) > 0))
			$urls['android'] = $post['android_url'];

		if (isset($post['windows_url']) && (strlen($post['windows_url']) > 0))
			$urls['windows'] = $post['windows_url'];	

		return $urls;
	}
	
	public function updateDownloadUrls($appId, $urls){
		foreach($urls as $platform => $url){
			$this->db->query('INSERT INTO '.$this->db->prefix.TB_DOWNLOADURLS.' SET
				`app_id` = "'.$appId.'",
				`platform` = "'.$platform.'",
				`url` = "'.$url.'"
				ON DUPLICATE KEY UPDATE `url` = "'.$url.'";');
		}
	}
	
	public function addNewDev($devName){
		$ret = $this->db->insert( 
			$this->db->prefix.TB_DEVLIST, 
			array( 
				'name' => $devName, 
			), 
			array( 
				'%s', 
			) 
		);
		
		if ($ret){
			return $this->db->insert_id;
		}
		
		return 0;
	}
	
	public function deleteApp($appId){
		$this->db->delete( $this->db->prefix.TB_APPSLIST, array( 'id' => $appId ) );
		$this->db->delete( $this->db->prefix.TB_DOWNLOADURLS, array( 'app_id' => $appId ) );
		$this->db->delete( $this->db->prefix.TB_CAMPAIGNS_APPS, array( 'app_id' => $appId ) );
		$this->db->delete( $this->db->prefix.TB_STATS, array( 'app_id' => $appId ) );
	}
}