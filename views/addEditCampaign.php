
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<h2 class="page-header">
			<?php 
			if ($defaultSave == 'edit'){
				echo('Edit Campaign `'.$campaign['name'].'`');
				
				if ($campaign['end_day'] == '2038-01-01 00:00:00'){
					$campaign['end_day'] = '';
				}else{
					$campaign['endDaySet'] = true;
				}
					
				if ($campaign['total_clicks'] == 9999999){
					$campaign['total_clicks'] = '';	
				}else{
					$campaign['clicksSet'] = true;
				}
					
				if ($campaign['daily_clicks'] == 9999999)
					$campaign['daily_clicks'] = '';
					
				if ($campaign['total_views'] == 999999999){
					$campaign['total_views'] = '';
				}else{
					$campaign['viewsSet'] = true;
				}
					
				if ($campaign['daily_views'] == 999999999)
					$campaign['daily_views'] = '';					
			}else{
				echo('Add New Campaign ');
			}
			?>
			</h2>
			
			<form action="#" method="post" class="form-horizontal" role="form" id="addCampaignForm">
				<input type="hidden" name="apps" id="apps" value="" />
				<?php
				if ($defaultSave == 'edit'){
				?>
				<input type="hidden" name="updateCampaign" value="1" />
				<input type="hidden" name="campaignId" value="<?php echo($campaign['id']); ?>">
				<?php }else{ ?>
				<input type="hidden" name="addCampaign" value="1" />
				<?php } ?>
				
				<div class="form-group">
					<label for="name" class="col-sm-2 control-label">Campaign Name</label>
					<div class="col-sm-5">
						<input type="text" class="form-control required" id="name" name="name" placeholder="ex. Google Maps promo CPM" value="<?php echo($campaign['name']); ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="typeSelect" class="col-sm-2 control-label">Start Time</label>
					<div class="col-sm-5">
						<input size="20" type="text" value="<?php echo($campaign['start_day']); ?>" name="start_day" id="start_day" readonly class="form_datetime">
					</div>
				</div>
				<div class="form-group">
					<label for="typeSelect" class="col-sm-2 control-label">End Time</label>
					<div class="col-sm-5">
						<label class="radio-inline col-sm-3">
						  <input type="radio" name="endDayOptions" id="noEnding" <?php echo((isset($campaign['endDaySet']) && ($campaign['endDaySet'] == true))?'':'checked'); ?> value="no-time"> No ending
						</label>

						<div class="col-sm-8">
							<label class="radio-inline">
							  <input type="radio" name="endDayOptions" id="endDayOptions-date" <?php echo((isset($campaign['endDaySet']) && ($campaign['endDaySet'] == true))?'checked':''); ?> value="time-frame"> Time Frame
							</label>
							<input type="text" value="<?php echo($campaign['end_day']); ?>" name="end_day" id="end_day" readonly class="form-control" style="margin: 10px 0 0 20px;">
						</div>
					</div>
				</div>				

				<div class="form-group">
					<label for="typeSelect" class="col-sm-2 control-label">Total Views</label>
					<div class="col-sm-5">
						<label class="radio-inline col-sm-3">
						  <input type="radio" name="viewsRadioOptions" id="viewsUnlimited" <?php echo((isset($campaign['viewsSet']) && ($campaign['viewsSet'] == true))?'':'checked'); ?> value="unlimited"> Unlimited
						</label>
						
						<div class="col-sm-4">
							<label class="radio-inline">
							  <input type="radio" name="viewsRadioOptions" id="viewsFinit" <?php echo((isset($campaign['viewsSet']) && ($campaign['viewsSet'] == true))?'checked':''); ?> value="max-views"> Max. Views
							</label>
							<input size="16" type="text" value="<?php echo($campaign['total_views']); ?>" name="maxViews" id="maxViews" class="form-control" placeholder="eg. 12000" style="margin: 10px 0 0 20px;">
						</div>
						
						<div class="col-sm-5">
							<label class="radio-inline" for="dailyMaxViews">Daily Max. Views</label>
							<input size="16" type="text" value="<?php echo($campaign['daily_views']); ?>" name="dailyMaxViews" id="dailyMaxViews" class="form-control" placeholder="eg. 2000" style="margin: 10px 0 0 0px;">
						</div>						
					</div>
				</div>
				
				<div class="form-group">
					<label for="typeSelect" class="col-sm-2 control-label">Total Clicks</label>
					<div class="col-sm-5">
						<label class="radio-inline col-sm-3">
						  <input type="radio" name="clicksRadioOptions" id="clicksUnlimited" <?php echo((isset($campaign['clicksSet']) && ($campaign['clicksSet'] == true))?'':'checked'); ?> value="unlimited"> Unlimited
						</label>
						
						<div class="col-sm-4">
							<label class="radio-inline">
							  <input type="radio" name="clicksRadioOptions" <?php echo((isset($campaign['clicksSet']) && ($campaign['clicksSet'] == true))?'checked':''); ?> value="max-views"> Max. Clicks
							</label>
							<input size="16" type="text" value="<?php echo($campaign['total_clicks']); ?>" name="maxClicks" id="maxClicks" class="form-control" placeholder="eg. 200" style="margin: 10px 0 0 20px;">
						</div>
						
						<div class="col-sm-5">
							<label class="radio-inline" for="dailyMaxViews">Daily Max. Clicks</label>
							<input size="16" type="text" value="<?php echo($campaign['daily_clicks']); ?>" name="dailyMaxClicks" id="dailyMaxClicks" class="form-control" placeholder="eg. 15" style="margin: 10px 0 0 0px;">
						</div>	
					</div>
				</div>				

				<div class="form-group">
					<label for="authorSelect" class="col-sm-2 control-label">Campaigns Total Views</label>
					<div class="col-sm-5" >
						<div class="col-sm-6">
							<label for="availableApps">Available applications</label>
							<select multiple class="form-control" id="availableApps" name="availableApps">
								<?php
								$apps = 0;
								foreach($allMobileApps as $app){
									if (isset($campaignApps[$app['id']]))
										continue;
									$apps++;
								?>
									<option value="<?php echo($app['id']); ?>"><?php echo($app['title']); ?></option>
								<?php
								}
								
								if ($apps == 0){
									echo('<option value="0">No mobile apps found, please ad one or more from MobileApps->Add App</option>');
								}
								?>
							</select>
							<button type="button" id="addapp" class="btn btn-primary" style="margin-top: 10px; width: 151px!important; float: right" >Add to campaign &gt;&gt;</button>
						</div>
						
						<div class="col-sm-6">
							<label for="availableApps">Campaign applications</label>
							<select multiple class="form-control " id="campaignApps" name="campaignApps">
								<?php
								foreach($campaignApps as $key => $app){
								?>
									<option value="<?php echo($key); ?>"><?php echo($app['title']); ?></option>
								<?php
								}
								?>
							</select>
							<button type="button" id="removeapp" class="btn btn-warning" style="margin-top: 10px; width: 100px!important; float: left;">&lt;&lt; Remove</button>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="btn-group">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" onClick="document.location='admin.php?page=mobile-appwidget-campaigns'" class="btn btn-primary">
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
	<?php /* http://www.malot.fr/bootstrap-datetimepicker/demo.php */ ?>
	jQuery("#start_day").datetimepicker({format: 'yyyy-mm-dd hh:ii:ss',autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left"});
	jQuery("#end_day")
	.datetimepicker({format: 'yyyy-mm-dd hh:ii:ss',autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-left"})
	.on('changeDate', function(ev){
		jQuery('#endDayOptions-date').attr('checked', 'checked');
	});

	jQuery('#noEnding').click(function(){
		jQuery('#end_day').val('');
	});
	
	jQuery('#viewsUnlimited').click(function(){
		jQuery('#maxViews').val('');
	});
	
	jQuery('#clicksUnlimited').click(function(){
		jQuery('#maxClicks').val('');
	});	
	
	jQuery('#addapp').click(function() {  
		return !jQuery('#availableApps option:selected').remove().appendTo('#campaignApps');  
	});  
	
	jQuery('#removeapp').click(function() {  
	return !jQuery('#campaignApps option:selected').remove().appendTo('#availableApps');  
	}); 
	 
	jQuery( "#addCampaignForm" ).submit(function( event ) {
		var isFormValid = true;
		
		if (jQuery('#campaignApps option').size() == 0){
			jQuery('#campaignApps').parent().addClass("has-error");
			isFormValid = false;
		}
		
		if (jQuery.trim(jQuery('#name').val()).length == 0){
			jQuery('#name').parent().addClass("has-error");
			isFormValid = false;
		}		
		
		if (!isFormValid){ 
			alert("Please fill in all the required fields (highlighted in red)");
			return isFormValid;
		}
		
		var selectedApps = {
		};
		
		jQuery("#campaignApps option").each(function(){
			selectedApps[jQuery(this).val()] = jQuery(this).text();
		});

		jQuery('#apps').val(jQuery.param(selectedApps));

		return true;
	}); 
});
</script>