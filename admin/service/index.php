<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Services</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_service" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="13%">
					<col width="17%">
					<col width="10%">
					<col width="30%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Category</th>
						<th>Name</th>
						<th>Description</th>
						<th>Price</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT p.*,c.name as cname from `service_list` p inner join category_list c on c.id = p.category_id order by date(p.date_created) desc ");
						while($row = $qry->fetch_assoc()):
                            $row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])));
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['cname'] ?></td>
							<td><?php echo $row['service'] ?></td>
							<td ><p class="truncate-1 m-0"><?php echo $row['description'] ?></p></td>
							<td class="text-right"><?php echo number_format($row['price'],2) ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#create_service').click(function(){
			uni_modal("<i class='fa fa-plus'></i> New Service","service/manage.php","mid-large")
		})
		$('.edit_data').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Update Service","service/manage.php?id="+$(this).attr('data-id'),"mid-large")
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this service permanently?","delete_service",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
		$('#uni_modal').on('shown.bs.modal', function() {
			$('.select2').select2({width:'resolve'})
			$('.summernote').summernote({
				height: 200,
				toolbar: [
					[ 'style', [ 'style' ] ],
					[ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
					[ 'fontname', [ 'fontname' ] ],
					[ 'fontsize', [ 'fontsize' ] ],
					[ 'color', [ 'color' ] ],
					[ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
					[ 'table', [ 'table' ] ],
					[ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
				]
			})
		})
	})
	function delete_service($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_service",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>