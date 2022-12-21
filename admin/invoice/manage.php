<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php
$type = isset($_GET['type']) ? $_GET['type'] : 1 ;
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `invoice_list` where md5(id) = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
$tax_rate = isset($tax_rate) ? $tax_rate : $_settings->info('tax_rate');
$item_arr = array();
if(isset($id)){
if($type == 1)
	$items = $conn->query("SELECT i.*,p.description,p.id as pid,p.product as `name`,p.category_id as cid FROM invoices_items i inner join product_list p on p.id = i.form_id where i.invoice_id = '{$id}' ");
else
	$items = $conn->query("SELECT i.*,s.description,s.id as `sid`,s.`service` as `name`,s.category_id as cid FROM invoices_items i inner join service_list s on s.id = i.form_id where i.invoice_id = '{$id}' ");
while($row=$items->fetch_assoc()):
	$category = $conn->query("SELECT * FROM `category_list` where id = {$row['cid']}");
	$cat_count = $category->num_rows;
	$res = $cat_count > 0 ? $category->fetch_assoc(): array();
	$row['cat_name'] = $cat_count > 0 ? $res['name'] : "N/A";
	$row['description'] = stripslashes(html_entity_decode($row['description']));
	$item_arr[] = $row;
endwhile;
}
?>
<style>
#item-list th, #item-list td{
	padding:5px 3px!important;
}
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
	<h3 class="card-title"><?php echo !isset($_GET['id']) ? "New Invoice" :"Edit Invoice" ?></h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<form action="" id="invoice-form">
				<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="invoice_code" class="control-label">Invoice Code</label>
							<input type="text" name="invoice_code" value="<?php echo isset($invoice_code) ? $invoice_code : ''; ?>"  class="form-control form-control-sm" required>
						</div>
						<div class="form-group">
							<label for="customer_name" class="control-label">Customer Name</label>
							<textarea name="customer_name" id="customer_name" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($customer_name) ? $customer_name : ''; ?></textarea>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="type" class="control-label">Type</label>
							<select name="type" id="type" <?php echo isset($_GET['id']) ? "disabled": "" ?> class="custom-select custom-select-sm select">
							<option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Product</option>
							<option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Services</option>
							</select>
						</div>
					</div>
				</div>
				<hr>
				<h4>Item Form:</h4>
				<div class="row align-items-end">
					<div class="col-md-3">
						<div class="form-group">
						<label for="category_id" class="control-label">Category</label>
							<select id="category_id" class="custom-select custom-select-sm select select2">
								<?php 
									$i = 0;
									$qry = $conn->query("SELECT * FROM category_list where `type` = {$type} ");
									while($row = $qry->fetch_assoc()):
									$i++;
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo $i == 1 ? 'selected': '' ?>><?php echo $row['name'] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
						<label for="form_id" class="control-label">Product/Service</label>
							<select  id="form_id" class="custom-select custom-select-sm select2">
								<option selected="" disabled>Select Category First</option>
								<?php 
								$data_json = array();
									if($type == 1):
										$qry2 = $conn->query("SELECT * FROM product_list ");
									else:
										$qry2 = $conn->query("SELECT * FROM service_list ");
									endif;
									while($row = $qry2->fetch_assoc()):
										$name = ($type == 1) ? $row['product'] : $row['service'];
										$row['description'] = stripslashes(html_entity_decode($row['description']));
										$row['name'] = $name;
										$data_json[$row['id']] = $row;
								?>
								<!-- <option value="<?php echo $row['id'] ?>" ><?php echo $name ?></option> -->
								<?php endwhile; ?>
							</select>
							<small id="price"></small>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="unit" class="control-label">Unit</label>
							<input type="text" id="unit"  class="form-control">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="qty" class="control-label">QTY</label>
							<input type="number" min='1' id="qty"  class="form-control text-right">
						</div>
					</div>
					<div class="col-md-2 pb-1">
						<div class="form-group">
							<button class="btn btn-flat btn-primary" type="button" id="add_item"><i class="fa fa-plus"></i> Add</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered" id="item-list">
							<colgroup>
								<col width="10%">
								<col width="15%">
								<col width="30%">
								<col width="15%">
								<col width="15%">
								<col width="5%">
							</colgroup>
							<thead>
								<tr>
									<th class="text-center">QTY</th>
									<th class="text-center">UNIT</th>
									<th class="text-center">Product/Service</th>
									<th class="text-center">Cost</th>
									<th class="text-center">Total</th>
									<th class="text-center"></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
									<th class="text-right" colspan="4">Sub Total</th>
									<th class="text-right" id="sub_total">0</th>
									<th><input type="hidden" name="sub_total" value="0"></th>
								</tr>
								<tr>
									<th class="text-right" colspan="4">Tax Rate</th>
									<th class="text-right" id="tax_rate"><?php echo $tax_rate ?>%</th>
									<th><input type="hidden" name="tax_rate" value="<?php echo $tax_rate ?>"></th>
								</tr>
								<tr>
									<th class="text-right" colspan="4">Tax</th>
									<th class="text-right" id="tax">0</th>
									<th></th>
								</tr>
								<tr>
									<th class="text-right" colspan="4">Grand Total</th>
									<th class="text-right" id="gtotal">0</th>
									<th><input type="hidden" name="total_amount" value="0"></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-7">
						<div class="form-group">
							<label for="remarks" class="control-label">Remarks</label>
							<textarea name="remarks" id="" cols="30" rows="2" class="form-control form no-resize summernote"><?php echo isset($remarks) ? $remarks : ''; ?></textarea>
						</div>
					</div>
				</div>
				
				
				
			</form>
		</div>
		<div class="card-footer">
				<button class="btn btn-flat btn-sm btn-primary" form="invoice-form">Save</button>
				<a class="btn btn-flat btn-sm btn-default" href="./?page=invoice">Cancel</a>
		</div>
	</div>
</div>
<script>
var item_arr = $.parseJSON('<?php echo json_encode($item_arr) ?>');
	function generate_code(){
		start_loader();
		$.ajax({
			url:_base_url_+'classes/Master.php?f=generate_code',
			method:'POST',
			data:{type:'<?php echo $type ?>'},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast(' An error occured while generating invoice code','error')
			},
			success:function(resp){
				if(resp.status == 'success'){
					$('[name="invoice_code"]').val(resp.code)
				}else{
					console.log(resp)
					alert_toast(' An error occured while generating invoice code','error')
				}
				end_loader(); 
			}
		})
	}
	function calc_total(){
		var total = 0;
		var tax_rate = parseFloat('<?php echo $tax_rate ?>') /100;
		$('#item-list tbody tr').each(function(){
			var tr = $(this)
				total += parseFloat(tr.find('[name="total[]"]').val());
		})
		$('[name="sub_total"]').val(total)
		$('#sub_total').text(parseFloat(total).toLocaleString('en-US'))
		var tax = parseFloat(total) * parseFloat(tax_rate);
		var gtotal = parseFloat(tax) + parseFloat(total);
		$('#tax').text(parseFloat(tax).toLocaleString('en-US'))
		$('#gtotal').text(parseFloat(gtotal).toLocaleString('en-US'))
		$('[name="total_amount"]').val(gtotal)


	}
	function rem_item(_this){
		_this.closest('tr').remove();
		calc_total()
	}
	function add_item($obj = null){
		var tr, td, item_id='', qty='', unit='',form_name,form_id='',price,total, description, category;
		if($obj == null){
		start_loader();
			var prod_d = $.parseJSON('<?php echo json_encode($data_json) ?>')
				prod_d = prod_d[$('#form_id').val()]
			qty = $('#qty').val();
			unit = $('#unit').val();
			form_id = $('#form_id').val();
			category = $('#category_id option:selected').text();
			price = parseFloat(prod_d.price)
			form_name = prod_d.name
			description = prod_d.description
			total = parseFloat(price) * parseFloat(qty)
		}else{
			item_id = $obj.id
			qty = $obj.quantity;
			unit = $obj.unit;
			form_id = $obj.form_id;
			category = $obj.cat_name;
			price = parseFloat($obj.price)
			form_name = $obj.name
			description = $obj.description
			total = parseFloat($obj.price) * parseFloat(qty)
		}
		if($('#item-list tbody').find('[name="form_id[]"][value="'+form_id+'"]').length > 0){
			alert_toast(' Item already on the list.','warning')
			end_loader();
			return false;
		}
		
		tr = $("<tr>")
		// quantity column
			td = $("<td>")
			td.addClass('text-center')
			td.text(qty)
			td.append("<input type='hidden' name='item_id[]' value='"+item_id+"' />") //item id input
			td.append("<input type='hidden' name='form_id[]' value='"+form_id+"' />") //item product/service input
			td.append("<input type='hidden' name='quantity[]' value='"+qty+"' />") //item quantity input
			td.append("<input type='hidden' name='unit[]' value='"+unit+"' />") //item unit input
			td.append("<input type='hidden' name='price[]' value='"+price+"' />") //item price input
			td.append("<input type='hidden' name='total[]' value='"+total+"' />") //item total input
			tr.append(td)
		// unit column
			td = $("<td>")
			td.addClass('text-center')
			td.text(unit)
			tr.append(td)
		// details column
			td = $("<td>")
			td.html("<p class='m-0'><small><b>Category:</b> "+category+"</small></p>"+
					"<p class='m-0'><small><b>Name: </b>"+form_name+"</p>"+
					"<div class='m-0'>"+description+"</div>");
			tr.append(td)
		// cost column
		td = $("<td>")
			td.addClass('text-right')
			td.text(parseFloat(price).toLocaleString('en-US'))
			tr.append(td)
		// total column
		td = $("<td>")
			td.addClass('text-right')
			td.text(parseFloat(total).toLocaleString('en-US'))
			tr.append(td)
		// action column
		td = $("<td>")
			td.addClass('text-center')
			td.append("<button class='btn btn-sm btn-outline-danger' type='button' onclick='rem_item($(this))'><i class='fa fa-trash'></i></button>")
			tr.append(td)
		$('#item-list tbody').append(tr)
		$('#qty').val('').trigger('change')
		$('#unit').val('').trigger('change')
		$('#form_id').val('').trigger('change')
		calc_total()
		if($obj == null)
		end_loader();
	}
	function load_items(){
		Object.keys(item_arr).map(k=>{
			add_item(item_arr[k])
		})
		end_loader()
	}
	$(document).ready(function(){
		$('.select2').select2()
		if('<?php echo isset($_GET['id']) ? 1 : 0 ?>' == 0)
		generate_code();
		
		if(Object.keys(item_arr).length > 0)
			load_items();
		$('[name="type"]').change(function(){
			location.href = "./?page=invoice/manage&type="+$(this).val()
		})
		$('#add_item').click(function(){
			if($('#qty').val() == '' ||$('#unit').val() == '' || $('#form_id').val() == ''){
				alert_toast("Please complete the Item form first.",'warning')
				return false;
			}
			add_item()
		})
		$('#category_id').change(function(){
			var cid = $(this).val()
			prods = $.parseJSON('<?php echo json_encode($data_json) ?>')
			$('#form_id').html('')
			Object.keys(prods).map(k=>{
				if(prods[k].category_id == cid){
					opt = "<option value='"+k+"'>"+prods[k].name+"</option>"
					$('#form_id').append(opt)
				}
			})
			$('#form_id').select2()
		})
		$('#category_id').trigger('change')
			$('[name="invoice_code"]').on('input',function(){
				$(this).removeClass('border-danger')
				$(this).removeClass('border-success')
				$.ajax({
				url:_base_url_+'classes/Master.php?f=code_availability',
				method:'POST',
				data:{id:'<?php echo isset($id) ? $id : 0 ?>',code:$(this).val()},
				dataType:"json",
				error:err=>{
					console.log(err)
					alert_toast(' An error occured','error')
				},
				success:function(resp){
					if(resp.status == 'available'){
						$('[name="invoice_code"]').addClass('border-success')
					}else if(resp.status == 'taken'){
						$('[name="invoice_code"]').addClass('border-danger')
					}else{
						console.log(resp)
						alert_toast(' An error occured while validating invoice code','error')
					}
					end_loader(); 
				}
			})
		})
		$('#invoice-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			if($('#item-list tbody tr').length <= 0){
				alert_toast("No Items Listed.",'warning')
				return false;
			}
			if($('[name="invoice_code"]').hasClass('border-danger') == true){
				alert_toast("Invoice Code already exist.",'warning')
				return false;
			}

			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_invoice",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						var nw = window.open("./invoice/print.php?id="+resp.id,"_blank","width=700,height=500")
							setTimeout(()=>{
								nw.print()
								setTimeout(()=>{
									nw.close()
									end_loader();
									location.replace('./?page=invoice/manage&id='+resp.id_encrypt)
								},500)
							},500)
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})
        
	})
</script>