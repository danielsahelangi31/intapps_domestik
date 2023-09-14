<?php
$grid_id = 'grid';
foreach($trace as $key => $val){
	$grid_id .= '-'.$key;
}
?>

<div class="table-responsive">
	<table class="table table-bordered table-grid" id="<?php echo $grid_id ?>">
		<thead>
			<tr>
				<?php
				foreach($suggestion_fields as $field => $label){
				?>
				<th><?php echo $label ?></th>
				<?php
				}
				?>
			</tr>
		</thead>
		<tbody id="container_landing">
		<?php
		if($suggestions){
			foreach($suggestions as $row){
		?>
			<tr>
				<?php
				foreach($row as $field => $val){
					if($field == $class_name.'_id'){
				?>
				<td><label><input type="radio" class="data_join_select" name="choose<?php echo $field_name ?>" value="<?php echo $val ?>"><?php echo $val ?></label></td>
				<?php
					}else{
				?>
				<td><?php echo $val ?></td>
				<?php
					}
				}
				?>
			</tr>
			<?php
			}
		}else{
		?>
			<tr><td colspan="<?php echo count($suggestion_fields) ?>">Tidak ada saran penggabungan untuk data ini </td></tr>
		<?php
		}
		?>
		</tbody>
	</table>
</div>