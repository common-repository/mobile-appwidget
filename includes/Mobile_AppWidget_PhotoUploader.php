<?php
function Mobile_AppWidget_PhotoUploader() {
	global $wpdb; // this is how you get access to the database
	
	$error = '';

	if (isset($_FILES)){
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$temp = explode(".", $_FILES["thumb"]["name"]);
		$extension = end($temp);

		if ((($_FILES["thumb"]["type"] == "image/gif")
			|| ($_FILES["thumb"]["type"] == "image/jpeg")
			|| ($_FILES["thumb"]["type"] == "image/jpg")
			|| ($_FILES["thumb"]["type"] == "image/pjpeg")
			|| ($_FILES["thumb"]["type"] == "image/x-png")
			|| ($_FILES["thumb"]["type"] == "image/png"))
			&& ($_FILES["thumb"]["size"] < 1024000)
			&& in_array($extension, $allowedExts)){
	
			$upload_dir = wp_upload_dir();
		
			$target_path = $upload_dir['basedir'].'/mobile-appdata/';
			
			$isLocation = false;
			if (!file_exists($target_path)) {
				if (mkdir($target_path, 0777, true)){
					$isLocation = true;
				}else{
					$error = 'cannot create upload folder, please check upload folder permisions';
				}
			}else{
				$isLocation = true;
			}

			if ($isLocation){
				$target_path = $target_path . basename( $_FILES['thumb']['name']); 

				if(move_uploaded_file($_FILES['thumb']['tmp_name'], $target_path)) {
					$img = resize_image($target_path, 100, 100, $_FILES["thumb"]["type"]);
					echo(
						json_encode(
							array(
								'response'	=> 
									array(
										'error' => 0,
										'avatar_url' => $upload_dir['baseurl'].'/mobile-appdata/' . basename( $_FILES['thumb']['name']),
										'target_path' => $target_path,
									)
							)
						)
					);
					
					die();
				}
			}
		}else{
			$error = 'extension not ok '.$extension.' mime: '.$_FILES["thumb"]["type"].' size: '.$_FILES["thumb"]["size"];
		}
	}else{
		$error = 'no file found';
	}

	echo(
		json_encode(
			array(
				'response'	=> 
					array(
						'error' => 1,
						'message' => $error,
					)
			)
		)
	);
	die(); // this is required to return a proper result
}

function resize_image($file, $w, $h, $type) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    
	// calculate new height / width
	if ($w/$h > $r) {
		$newwidth = $h*$r;
		$newheight = $h;
	} else {
		$newheight = $w/$r;
		$newwidth = $w;
	}
	
	switch($type){
		case 'bmp': 
			$src = imagecreatefromwbmp($file); break;
		case 'image/gif': 
			$src = imagecreatefromgif($file); break;
		
		case 'image/pjpeg':
		case 'image/jpg':
		case 'image/jpeg': 
			$src = imagecreatefromjpeg($file); break;
			
		case 'image/x-png': 
		case 'image/png': 
			$src = imagecreatefrompng($file); break;
		default : return "Unsupported picture type!";
	}
	
    $newImg = imagecreatetruecolor($newwidth, $newheight);
	imagealphablending($newImg, false);
	imagesavealpha($newImg,true);
	$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
	imagefilledrectangle($newImg, 0, 0, $newwidth, $newheight, $transparent);
    
	imagecopyresampled($newImg, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	imagepng($newImg, $file, 0, PNG_NO_FILTER );
    return $newImg;
}