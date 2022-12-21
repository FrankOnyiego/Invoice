<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Category successfully saved.");
			else
				$this->settings->set_flashdata('success',"Category successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `category_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_product(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				if($k == 'price')
				$v = floatval(str_replace(',','',$v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `product_list` where `product` = '{$product}' and `category_id` = '{$category_id}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `product_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `product_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Product successfully saved.");
			else
				$this->settings->set_flashdata('success',"Product successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_product(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `product_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Product successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_service(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				if($k == 'price')
				$v = floatval(str_replace(',','',$v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `service_list` where `service` = '{$service}' and `category_id` = '{$category_id}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Service already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `service_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `service_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Service successfully saved.");
			else
				$this->settings->set_flashdata('success',"Service successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_service(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `service_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Service successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function generate_code(){
		extract($_POST);
		while(true){
			$code = ($type == 1) ? 'Product-' : 'Service-';
			$code .= sprintf("%'.07d",mt_rand(1,9999999));
			$chk = $this->conn->query("SELECT * FROM invoice_list where invoice_code = '{$code}'")->num_rows;
			if($chk <= 0)
				break;
		}
		$resp['status'] = 'success';
		$resp['code'] = $code;
		return json_encode($resp);
	}
	function code_availability(){
		extract($_POST);
		$chk = $this->conn->query("SELECT * FROM invoice_list where invoice_code = '{$code}' " .($id > 0 ? "and id!='{$id}'" :''))->num_rows;
		if($chk <= 0)
			$resp['status'] = 'available';
		else
			$resp['status'] = 'taken';
		
		return json_encode($resp);
	}
	function save_invoice(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','remarks')) && !is_array($_POST[$k])){
				if(!empty($data)) $data .=",";
				if($k == 'price')
				$v = floatval(str_replace(',','',$v));
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['remarks'])){
			if(!empty($data)) $data .=",";
				$data .= " `remarks`='".addslashes(htmlentities($remarks))."' ";
		}
		if(empty($id)){
			$sql = "INSERT INTO invoice_list set $data";
		}else{
			$sql = "UPDATE invoice_list set $data where id= {$id}";
		}
		$save = $this->conn->query($sql);
		if($this->capture_err())
			return $this->capture_err();
		$id = !empty($id) ? $id : $this->conn->insert_id;
		$item_data_ins = "";
		foreach($_POST['item_id'] as $k => $v){
			if(!empty($item_data_ins)) $item_data_ins .= ", ";
			if(empty($v)){
				$item_data_ins .= "('{$id}','{$form_id[$k]}','{$unit[$k]}','{$quantity[$k]}','{$price[$k]}','{$total[$k]}')";
			}else{
				$ids[] = $v;
				$upd = $this->conn->query("UPDATE invoices_items set form_id = '{$form_id[$k]}',unit = '{$unit[$k]}',quantity = '{$quantity[$k]}',price='{$price[$k]}',total='{$total[$k]}' where id = {$v}");
				if($this->capture_err())
				return $this->capture_err();
			}
		}
		if(isset($ids) && count($ids) > 0){
			$ids_imp = implode(",",$ids);
			$del = $this->conn->query("DELETE FROM invoices_items where id not in ({$ids_imp}) and invoice_id = '{$id}' ");
				if($this->capture_err())
				return $this->capture_err();
		}

		if(!empty($item_data_ins)){
			$ins_items = $this->conn->query("INSERT INTO invoices_items (invoice_id,form_id,`unit`,`quantity`,`price`,`total`) VALUES {$item_data_ins} ");
			if($this->capture_err())
				return $this->capture_err();
		}
		$resp['status'] = 'success';
		$resp['id'] = $id;
		$resp['id_encrypt'] = md5($id);
		$this->settings->set_flashdata('success',"Invoice successfully saved.");
		return json_encode($resp);
	}
	function delete_invoice(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `invoice_list` where id = '{$id}'");
		$del2 = $this->conn->query("DELETE FROM `invoices_items` where invoice_id = '{$id}'");
		if($del && $del2){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Invoice successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_product':
		echo $Master->save_product();
	break;
	case 'delete_product':
		echo $Master->delete_product();
	break;
	case 'save_service':
		echo $Master->save_service();
	break;
	case 'delete_service':
		echo $Master->delete_service();
	break;
	case 'generate_code':
		echo $Master->generate_code();
	break;
	case 'code_availability':
		echo $Master->code_availability();
	break;
	case 'save_invoice':
		echo $Master->save_invoice();
	break;
	case 'delete_invoice':
		echo $Master->delete_invoice();
	break;
	default:
		// echo $sysset->index();
		break;
}