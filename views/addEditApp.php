<?php

?>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<h2 class="page-header">
			<?php 
			if ($defaultSave == 'edit'){
				echo('Edit Application `'.$app['title'].'`');
			}else{
				echo('Add New Mobile Application ');
			}
			?>
			</h2>
			
			<form action="#" method="post" class="form-horizontal" role="form" id="addAppForm">
				<?php
				if ($defaultSave == 'edit'){
				?>
				<input type="hidden" name="updateApp" value="1" />
				<input type="hidden" name="appId" value="<?php echo($app['id']); ?>">
				<?php }else{ ?>
				<input type="hidden" name="addApp" value="1" />
				<?php } ?>
				
				<div class="form-group">
					<label for="title" class="col-sm-2 control-label">Application Title</label>
					<div class="col-sm-5">
						<input type="text" class="form-control required" name="title" placeholder="ex. Google Maps" value="<?php echo($app['title']); ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="description" class="col-sm-2 control-label">Description</label>
					<div class="col-sm-5">
						<textarea class="form-control required" rows="3" name="description"><?php echo($app['description']); ?></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<label for="authorSelect" class="col-sm-2 control-label">Select Developer/Author</label>
					<div class="col-sm-5" >
						<select class="form-control" id="authorSelect" name="authorSelect">
							<option value="0">Add New Author</option>
							<?php
							foreach($devsList as $dev){
							
								$selected = '';
								if ((isset($app['dev_id'])) && ($dev['id'] == $app['dev_id'])){
									$selected = ' selected = "selected" ';
								}
							?>
							<option value="<?php echo($dev['id']); ?>" <?php echo($selected); ?>><?php echo($dev['name']); ?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				
				<div class="form-group">
					<label for="author" class="col-sm-2 control-label">Author/Dev Name</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="author" id="author" placeholder="ex. Mike Sands or Yahoo Inc." value="<?php echo($app['dev_name']); ?>">
						<input type="hidden" name="authorId" id="authorId" value="<?php echo($app['dev_id']); ?>" class="required" /> 
					</div>
				</div>

				<div class="form-group">
					<label for="price" class="col-sm-2 control-label">Price <em>(* 0 if app is free)</em></label>
					<div class="col-sm-5">
						<input type="text" class="form-control required" name="price" placeholder="ex. 0.99" value="<?php echo($app['price']); ?>">
					</div>
				</div>		

				<div class="form-group">
					<label for="currency" class="col-sm-2 control-label">Currency</label>
					<div class="col-sm-5">
						<select class="form-control" name="currency">
						<?php
						foreach($currencys as $cod => $name){
							$selected = '';
							if ((isset($app['currency'])) && ($cod == $app['currency'])){
								$selected = ' selected = "selected" ';
							}
						?>
							<option value="<?php echo($cod); ?>" <?php echo($selected); ?>><?php echo($name); ?></option>
						<?php
						}
						?>							
						</select>
					</div>
				</div>				

				<div class="form-group">
					<label for="upload_image" class="col-sm-2 control-label">App Image</label>
					<div class="col-sm-5">
						
						<ul class="nav nav-tabs" id="uploader">
						  <li class="active"><a href="#fromurl">From URL</a></li>
						  <li><a href="#uploadimage">Upload image</a></li>
						</ul>

							<div id='content' class="tab-content" style="height: 100px;">
							  <div class="tab-pane active" id="fromurl">
								<br/>
								<em>Paste here the Image URL</em><br/>
								<input class="col-sm-12" id="upload_image" type="text" placeholder="http://www.mydomain.com/myimage.jpg" name="upload_image" value="<?php echo($app['thumb']); ?>" />
								
							  </div>
							  <div class="tab-pane" id="uploadimage">
								<img id="preview" src="<?php echo(WP_PLUGIN_URL . '/mobile-appwidget/assets/img/default_img.png'); ?>" width="80" height="80" />
								<input type="button" class="btn btn-primary" value="Select File" onclick="jQuery('#thumb').click();" />
						
							  </div>
							</div>    

							
							
					</div>
				</div>
				
				<div class="form-group">
					<label for="ios_url" class="col-sm-2 control-label">iOS Download URL</label>
					<div class="col-sm-5">
						<img src="<?php echo(WP_PLUGIN_URL . '/mobile-appwidget/assets/img/apple.png'); ?>" style="float: left;" alt="iOS download URL" width="28" height="28" />
						<input type="text" class="col-sm-11" style="float: left;" name="ios_url" value="<?php echo(isset($app['links']['ios'])?$app['links']['ios']:''); ?>" placeholder="https://itunes.apple.com/us/app/google-maps/id585027354">
					</div>
				</div>
				
				<div class="form-group">
					<label for="android_url" class="col-sm-2 control-label">Android Download URL</label>
					<div class="col-sm-5">
						<img src="<?php echo(WP_PLUGIN_URL . '/mobile-appwidget/assets/img/google_android.png'); ?>" style="float: left;" alt="Android download URL" width="28" height="28" />
						<input type="text" class="col-sm-11" style="float: left;" name="android_url" value="<?php echo(isset($app['links']['android'])?$app['links']['android']:''); ?>" placeholder="https://play.google.com/store/apps/details?id=com.google.android.apps.maps">
					</div>
				</div>
				
				<div class="form-group">
					<label for="windows_url" class="col-sm-2 control-label">Windows Phone Download URL</label>
					<div class="col-sm-5">
						<img src="<?php echo(WP_PLUGIN_URL . '/mobile-appwidget/assets/img/windows_8.png'); ?>" style="float: left;" alt="Windows download URL" width="28" height="28" />
						<input type="text" class="col-sm-11" style="float: left;" name="windows_url" value="<?php echo(isset($app['links']['windows'])?$app['links']['windows']:''); ?>" placeholder="http://myhostingdomain.com/my_app_url/">
					</div>
				</div>

				<div class="form-group">
					<div class="btn-group">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" onClick="document.location='admin.php?page=mobile-appwidget-handle'" class="btn btn-primary">
						<?php
						if ($defaultSave == 'edit'){
							echo('Back');
						}else{
							echo('Cancel');
						}
						?>
						</button>
					</div>
				</div>

			</form>

		</div>
	</div>
</div>

<style>
body{background-color: transparent!important;}
</style>
<script>
jQuery(document).ready(function() {

	jQuery('#uploader a').click(function (e) {
	  e.preventDefault()
	  jQuery(this).tab('show')
	});



	jQuery("#addAppForm").submit(function(){
		var isFormValid = true;
		
		jQuery("#addAppForm input:text, #addAppForm textarea").each(function(){ // Note the :text
			if (jQuery(this).hasClass( "required" )){
				if (jQuery.trim(jQuery(this).val()).length == 0){
					jQuery(this).parent().addClass("has-error");
					isFormValid = false;
				} else {
					jQuery(this).parent().removeClass("has-error");
				}
			}
		});
		
		
		if (!isFormValid) alert("Please fill in all the required fields (highlighted in red)");
		return isFormValid;
	});	

	jQuery('#authorSelect').change(function (){
		if (jQuery(this).val() == 0){
			jQuery('#author').val('');
			jQuery('#author').prop('disabled', false);
		}else{
			jQuery('#author').val(jQuery('#authorSelect option:selected').text());
			jQuery('#authorId').val(jQuery(this).val());
			jQuery('#author').prop('disabled', true);
		}
	});
 
});
</script>







<form id="thumb_form" action="#" method="post" enctype="multipart/form-data" target="uploader_iframe" style="display: none;">
		<!--<input id="avatar" type="file" name="avatar" size="30" />-->
		<input type="hidden" name="action" value="mobile_app_widget_photo_upload" />
		<div class="fileUpload btn btn-primary">
			<span>Upload</span>
			<input type="file" id="thumb" name="thumb" class="upload" />
		</div>
	</form>
	 
	<!-- Hidden iframe which will interact with the server, do not forget to give both name and id values same -->
	<iframe id="uploader_iframe" name="uploader_iframe" style="display: none;"></iframe>
	 
	<!-- Just added to show the preview when the image is uploaded. -->
	
	<script>
	
	function readURL(input) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				jQuery('#preview').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	jQuery(function() {
		jQuery("#thumb_form").attr("action", ajaxurl);	// ajaxurl is a wordpress ajax variable
		
	  jQuery("#thumb").change(function() {
		jQuery('#preview').fadeTo( "slow" , 0.5, function() {
			// Animation complete.
		});
		
		readURL(this);
		jQuery("#thumb_form").submit();  // Submits the form on change event, you consider this code as the start point of your request (to show loader)
		
		jQuery("#uploader_iframe").unbind().load(function() {  // This block of code will execute when the response is sent from the server.
			console.log(jQuery(this).contents().text());
			result = jQuery.parseJSON(jQuery(this).contents().text());  // Content of the iframe will be the response sent from the server. Parse the response as the JSON object
			
			jQuery('#preview').fadeTo( "slow" , 1, function() {
				// Animation complete.
			});
			
		  jQuery("#preview").attr("src", result.response.avatar_url); 
		  jQuery('#upload_image').val(result.response.avatar_url);
		});
	  });
	});
	</script>
	
	
	<style>
	.fileUpload {
		position: relative;
		overflow: hidden;
		margin: 10px;
	}
	.fileUpload input.upload {
		position: absolute;
		top: 0;
		right: 0;
		margin: 0;
		padding: 0;
		font-size: 20px;
		cursor: pointer;
		opacity: 0;
		filter: alpha(opacity=0);
	}
	</style>
