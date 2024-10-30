<?php
?>

<style>
body{background-color: transparent!important;}
.mytable td {line-height: 65px!important;}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<h2 class="page-header">Mobile Apps List</h2>
			
			<div class="btn-group">
				<a href="?page=mobile-appwidget-handle&action=add-app" class="btn btn-info">New Application</a>
				<a href="admin.php?page=mobile-appwidget-campaigns" class="btn btn-info">New Campaign</a>
			</div>

			<div class="mytable table-responsive">
				<table class="table table-striped">
				<thead>
					<tr>
						<th>Image</th>
						<th>Name</th>
						<th>Author</th>
						<th>Price</th>
						<th>iOS URL</th>
						<th>Android URL</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach($results as $app){
				?>
					<tr>
						<td>
							<img src="<?php echo($app['thumb']); ?>" alt="<?php echo($app['title']); ?>" width="65" height="65" />
						</td>
						<td><?php echo($app['title']); ?></td>
						<td><?php echo($app['devName']); ?></td>
						<td>
						<?php
						if ($app['price'] == 0){
							echo('FREE');
						}else{
							echo($app['price']);
						}
						?>
						</td>
						<td><a href="#">iOS Download</a></td>
						<td><a href="#">Android Download</a></td>
						<td>
							<a href="?id=<?php echo($app['id']); ?>&action=edit&page=mobile-appwidget-handle" class="btn btn-primary">Edit</a>
							<a href="?id=<?php echo($app['id']); ?>&action=delete&page=mobile-appwidget-handle" class="btn btn-danger">Delete</a>
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