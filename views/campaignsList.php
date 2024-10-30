<?php
?>

<style>
body{background-color: transparent!important;}
.mytable td {line-height: 65px!important;}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<h2 class="page-header">Campaigns List</h2>
			
			<div class="btn-group">
				<a href="admin.php?page=mobile-appwidget-handle" class="btn btn-info">Mobile Applications</a>
				<a href="admin.php?page=mobile-appwidget-handle&action=add-app" class="btn btn-info">New Application</a>
				<a href="admin.php?page=mobile-appwidget-campaigns&action=add-campaign" class="btn btn-info">New Campaign</a>
			</div>

			<div class="mytable table-responsive">
				<table class="table table-striped">
				<thead>
					<tr>
						<th>Name</th>
						<th>Status</th>
						<th>Start</th>
						<th>End</th>
						<th>Clicks</th>
						<th>Views</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach($results as $campaign){
				?>
					<tr>
						<td><?php echo($campaign['name']); ?></td>
						<td><?php echo($campaign['status']); ?></td>
						<td><?php echo($campaign['start_day']); ?></td>
						<td>
						<?php
						$end_day = strtotime($campaign['end_day']);						
						if (($end_day == false) || ($end_day >= strtotime('2038-01-01')))
							echo('no ending');
						else
							echo($campaign['end_day']); 
						?>
						</td>
						<td><?php echo($campaign['clicks']); ?></td>
						<td><?php echo($campaign['views']); ?></td>
						<td>
							<a href="?id=<?php echo($campaign['id']); ?>&action=edit&page=mobile-appwidget-campaigns" class="btn btn-primary">Edit</a>
						<?php
						//print_r($campaign);
						if ($campaign['status'] == 'paused'){
						?>
							<a href="?id=<?php echo($campaign['id']); ?>&action=start_campaign&page=mobile-appwidget-campaigns" title="Start campaign" class="btn btn-primary">Start</a>
						<?php
						}else{
						?>
							<a href="?id=<?php echo($campaign['id']); ?>&action=pause_campaign&page=mobile-appwidget-campaigns" title="Pause campaign" class="btn btn-primary">Pause</a>
						<?php
						}
						?>
							<a href="?id=<?php echo($campaign['id']); ?>&action=delete&page=mobile-appwidget-campaigns" class="btn btn-danger">Delete</a>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>