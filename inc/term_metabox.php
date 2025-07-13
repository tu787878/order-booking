<?php 
function dsmart_taxonomy_add_custom_meta_field() {
	wp_nonce_field( 'taxonomy-term-usecoupon-form-save', 'taxonomy-term-usecoupon-save-form-nonce' ); ?>
	<div class="form-field">
		<label for="can_not_use_coupon"><?php _e( 'Can not use coupon?' ); ?></label>
		<input type="checkbox" name="can_not_use_coupon" id="can_not_use_coupon" value="">
		<p class="description"><?php _e( 'Tick if you dont\'t want to use coupon for this category.' ); ?></p>
	</div>
	<tr class="form-field">
		<th><?php _e("Kategorie ON/OFF?"); ?></th>
		<td class="radio-wrap">
			<label><input type="radio" name="tax_enable" value="0">Aus</label>
			<label><input type="radio" name="tax_enable" value="1" checked>An</label>
		</td>
	</tr>
	<div class="form-field">
		<label for="pools"><?php _e( 'Pool Druckverteilung auswählen' ); ?></label>
		<select class="widefat" name="pool" id="pools" >
					<option value=""><?php _e("None") ?></option>
					<?php 
						$pools = get_terms( array(
							'taxonomy' => 'pool',
							'hide_empty' => false,
						) );

						foreach($pools as $pool){
							?>
								<option value="<?php echo $pool->term_id ?>"><?php echo $pool->name ?></option>
							<?php
						}
					?>
				</select>
	</div>
	<div class="form-field">
		<label for="category_pos"><?php _e( 'Enter your category position' ); ?></label>
		<input type="number" name="category_pos" id="category_pos" value="0">
	</div>
	<div class="form-field">
		<label><?php _e( 'Open only time' ); ?></label>
		<div class="list-tax-date">
			<div class="item-date">
				<select class="widefat" name="time_date[]" required>
					<option value="mo"><?php _e("Montag") ?></option>
					<option value="tu"><?php _e("Dienstag") ?></option>
					<option value="we"><?php _e("Mittwoch") ?></option>
					<option value="th"><?php _e("Donnerstag") ?></option>
					<option value="fr"><?php _e("Freitag") ?></option>
					<option value="sa"><?php _e("Samstag") ?></option>
					<option value="su"><?php _e("Sonntag"); ?></option>
				</select>
				<input type="text" name="time_open[]" class="widefat timepicker" placeholder="<?php _e('Open time') ?>" value="" autocomplete="off" style="margin-bottom: 5px;"/>
				<input type="text" name="time_close[]" class="widefat timepicker" placeholder="<?php _e('Close time') ?>" value="" autocomplete="off"/>
				<span class="remove-date">x</span>
			</div>
		</div>
		<button class="button add-new-date" type="button"><?php _e("Neue Zeile hinzufugen"); ?></button>
	</div>
<?php }
add_action( 'product-cat_add_form_fields', 'dsmart_taxonomy_add_custom_meta_field', 10, 2 );

function dsmart_taxonomy_edit_custom_meta_field($term) {
    $t_id = $term->term_id;
   	$can_not_use_coupon = get_term_meta( $t_id, 'can_not_use_coupon', true );
   	$p = get_term_meta( $t_id, 'pool', true );
   	// $p = 7;
   	$tax_time = get_term_meta( $t_id, 'tax_time', true );
	$dsmart_new_custom_date = get_term_meta( $t_id, 'tax_new_custom_date', true );
	if($dsmart_new_custom_date == "" || !$dsmart_new_custom_date){
		$dsmart_new_custom_date = [];
	}
	$tax_enable = get_term_meta( $t_id, 'tax_enable', true );
	if(!isset($tax_enable) || $tax_enable === null || $tax_enable === "") $tax_enable = "1";
   	$category_pos = (intval(get_term_meta( $t_id, 'category_pos', true )) > 0) ? intval(get_term_meta( $t_id, 'category_pos', true )) : 0;
    wp_nonce_field( 'taxonomy-term-usecoupon-form-save', 'taxonomy-term-usecoupon-save-form-nonce' );
	?>
	<tr class="form-field">
		<th><?php _e("Kategorie ON/OFF?"); ?></th>
		<td class="radio-wrap">
			<label><input type="radio" name="tax_enable" value="0" <?php if ($tax_enable !== "1") echo 'checked'; ?>>Aus</label>
			<label><input type="radio" name="tax_enable" value="1" <?php if ($tax_enable === "1") echo 'checked'; ?>>An</label>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="can_not_use_coupon"><?php _e( 'Can not use coupon?' ); ?></label></th>
		<td>
			<input type="checkbox" name="can_not_use_coupon" id="can_not_use_coupon" value="1" <?php echo ( $can_not_use_coupon == "1") ? 'checked' : ''; ?>/>
			<p class="description"><?php _e( 'Tick if you dont\'t want to use coupon for this category.' ); ?></p>
		</td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="pools"><?php _e( 'Pool Druckverteilung auswählen' ); ?></label></th>
		<td>
		<select class="widefat" name="pool" id="pools" >
					<option value=""><?php _e("None") ?></option>
					<?php 
						$pools = get_terms( array(
							'taxonomy' => 'pool',
							'hide_empty' => false,
						) );

						foreach($pools as $pool){
							
							?>
								<option value="<?php echo $pool->term_id ?>" <?php if($p == $pool->term_id) echo 'selected' ?>><?php echo $pool->name ?></option>
							<?php
						}
					?>
				</select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="category_pos"><?php _e( 'Enter your category position' ); ?></label></th>
		<td>
			<input type="number" name="category_pos" id="category_pos" value="<?php echo $category_pos; ?>" />
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label><?php _e( 'Open only time' ); ?></label></th>
		<td>
			<div class="list-tax-date">
				<?php if($tax_time != "" && is_array($tax_time) && count($tax_time) > 0): 
					foreach($tax_time as $key => $item): ?>
						<div class="item-date">
							<select class="widefat" name="time_date[]" required>
								<option value="mo" <?php if($item['date'] == "mo"){echo 'selected';} ?>><?php _e("Montag") ?></option>
								<option value="tu" <?php if($item['date'] == "tu"){echo 'selected';} ?>><?php _e("Dienstag") ?></option>
								<option value="we" <?php if($item['date'] == "we"){echo 'selected';} ?>><?php _e("Mittwoch") ?></option>
								<option value="th" <?php if($item['date'] == "th"){echo 'selected';} ?>><?php _e("Donnerstag") ?></option>
								<option value="fr" <?php if($item['date'] == "fr"){echo 'selected';} ?>><?php _e("Freitag") ?></option>
								<option value="sa" <?php if($item['date'] == "sa"){echo 'selected';} ?>><?php _e("Samstag") ?></option>
								<option value="su" <?php if($item['date'] == "su"){echo 'selected';} ?>><?php _e("Sonntag"); ?></option>
							</select>
							<input type="text" name="time_open[]" class="widefat timepicker" placeholder="<?php _e('Open time') ?>" value="<?php echo $item['open']; ?>" autocomplete="off" style="margin-bottom: 5px;"/>
							<input type="text" name="time_close[]" class="widefat timepicker" placeholder="<?php _e('Close time') ?>" value="<?php echo $item['close']; ?>" autocomplete="off"/>
							<span class="remove-date">x</span>
						</div>
					<?php endforeach;
				endif; ?>
			</div>
			<button class="button add-new-date" type="button"><?php _e("Neue Zeile hinzufugen"); ?></button>
		</td>
	</tr>

	<tr class="form-field timepicker-group table-custom-date-tax">
			<th><?php _e('Benutzerdefinierte Öffnungszeit') ?></th>
			<td>

			<h4><?php _e('Feiertage importieren:') ?></h4>
			<div class="row" style="display: flex;">
				<select name="holiday_year" class="widefat">
				<?php 
					for($i = 2024; $i <= 2070; $i++){
				?>
						<option value="<?php echo $i ?>"><?php echo $i ?></option>
				<?php
				}
				?>
				</select>

				<select name="holiday_region" class="widefat">
				<?php 
					$region = array(
						"de" => "Deutschland",
						"de-bw" => "Baden-Württemberg",
						"de-by" => "Bayern",
						"de-be" => "Berlin",
						"de-bb" => "Brandenburg",
						"de-hb" => "Bremen",
						"de-hh" => "Hamburg",
						"de-he" => "Hessen",
						"de-mv" => "Mecklenburg-Vorpommern",
						"de-ni" => "Niedersachsen",
						"de-nw" => "Nordrhein-Westfalen",
						"de-rp" => "Rheinland-Pfalz",
						"de-sl" => "Saarland",
						"de-sn" => "Sachsen",
						"de-st" => "Sachsen-Anhalt",
						"de-sh" => "Schleswig-Holstein",
						"de-th" => "Thüringen",
					);

					foreach ($region as $key => $label) {
					
				?>
						<option value="<?php echo $key ?>"><?php echo $label ?></option>
				<?php
				}
				?>
				</select>

				<button type="button" class="button import-holiday"><?php _e("Importieren") ?></button>
			</div>
			<table style="border-spacing: 0 15px;" class="table">
				<thead>
					<tr>
						<th><?php _e("Öffnen/Schliessen") ?></th>
						<th><?php _e("Datum") ?></th>
						<th><?php _e("Zeit") ?></th>
						<th><?php _e("Action") ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ($dsmart_new_custom_date != "") {
						foreach ($dsmart_new_custom_date as $item) {
							?>
							<tr style="vertical-align: top;">
								<td>
									<select name="new_custome_date_status[]" class="widefat">
										<option value="open" <?php echo $item["status"] === "open" ? "selected" : "" ?>>Open</option>
										<option value="close" <?php echo $item["status"] === "close" ? "selected" : "" ?>>Close</option>
									</select>
								</td>
								<td class="date_choosing">
									<select name="new_custome_date_type[]" class="widefat">
										<option value="single" <?php echo $item["date_type"] === "single" ? "selected" : "" ?> >Date</option>
										<option value="multiple" <?php echo $item["date_type"] === "multiple" ? "selected" : "" ?>>Date Range</option>
									</select>
									<br>
									<input type="text" placeholder="start date" name="new_custome_date_start_date[]" class="widefat datepicker start_date_picker" value="<?php echo $item["start_date"]?>" autocomplete="off" required />
									<br>
									<?php if ($item["date_type"] === "single"){
										?>
											<input type="text" style="display: none;" placeholder="end date" name="new_custome_date_end_date[]" class="widefat datepicker end_date_picker" value="<?php echo $item["end_date"]?>" autocomplete="off" />
										<?php
									}else{
										?>
											<input type="text" placeholder="end date" name="new_custome_date_end_date[]" class="widefat datepicker end_date_picker" value="<?php echo $item["end_date"]?>" autocomplete="off" />
										<?php
									} ?>
								</td>
								<td class="time_choosing">
									<select name="new_custome_date_time_type[]" class="widefat select_time_type">
										<option class="should_disable" <?php echo $item["status"] === "close" ? "disabled" : "" ?> value="time_to_time" <?php echo $item["time_type"] === "time_to_time" ? "selected" : "" ?>>Uhrzeit</option>
										<option value="full_day" <?php echo $item["time_type"] === "full_day" ? "selected" : "" ?>>Ganztägig (00:00 - 23:59)</option>
									</select>
									<?php if ($item["time_type"] === "time_to_time"){
										?>
											<br>
											<input type="text" placeholder="start time" name="new_custome_date_start_time[]" class="widefat timepicker start_time_picker" value="<?php echo $item["start_time"]?>" autocomplete="off"/>
											<br>
											<input type="text" placeholder="end time" name="new_custome_date_end_time[]" class="widefat timepicker end_time_picker" value="<?php echo $item["end_time"]?>" autocomplete="off" />
										<?php
									}else{
										?>
											<br>
											<input type="text" style="display: none;" placeholder="start time" name="new_custome_date_start_time[]" class="widefat timepicker start_time_picker" value="<?php echo $item["start_time"]?>" autocomplete="off"/>
											<br>
											<input type="text" style="display: none;" placeholder="end time" name="new_custome_date_end_time[]" class="widefat timepicker end_time_picker" value="<?php echo $item["end_time"]?>" autocomplete="off" />
										<?php
									} ?>
									
								</td>
								
								<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
							</tr>
					<?php }
					} ?>
					<tr class="hidden-field">
							<td>
								<select name="new_custome_date_status[]" class="widefat">
									<option value="open" >Open</option>
									<option value="close" >Close</option>
								</select>
							</td>
							<td class="date_choosing">
								<select name="new_custome_date_type[]" class="widefat">
									<option value="single" >Date</option>
									<option value="multiple" >Date Range</option>
								</select>
								<br>
								<input type="text" placeholder="start date" name="new_custome_date_start_date[]" class="widefat datepicker start_date_picker" value="" autocomplete="off" />
								<br>
								<input type="text" style="display: none;" placeholder="end date" name="new_custome_date_end_date[]" class="widefat datepicker end_date_picker" value="" autocomplete="off" />
							</td>
							<td class="time_choosing">
								<select name="new_custome_date_time_type[]" class="widefat select_time_type">
									<option class="should_disable" value="time_to_time">Uhrzeit</option>
									<option value="full_day">Ganztägig (00:00 - 23:59)</option>
								</select>
								<br>
								<input type="text" placeholder="start time" name="new_custome_date_start_time[]" class="widefat timepicker start_time_picker" value="" autocomplete="off"/>
								<br>
								<input type="text" placeholder="end time" name="new_custome_date_end_time[]" class="widefat timepicker end_time_picker" value="" autocomplete="off" />
							</td>
							
							<td><span class="button remove-row"><?php _e("Löschen") ?></span></td>
					</tr>
				</tbody>
			</table>
			<button type="button" class="button add-new-row"><?php _e("Neue Zeile hinzufugen") ?></button>
		</td>
	</tr>
<?php }

add_action( 'product-cat_edit_form_fields', 'dsmart_taxonomy_edit_custom_meta_field', 10, 2 );

function dsmart_save_taxonomy_custom_meta_field( $term_id ) {
		if (
			isset( $_POST['taxonomy-term-usecoupon-save-form-nonce'] ) &&
			wp_verify_nonce( $_POST['taxonomy-term-usecoupon-save-form-nonce'], 'taxonomy-term-usecoupon-form-save' ) &&
			isset( $_POST['taxonomy'] )
		){
			$new_can_not_use_coupon = (isset($_POST['can_not_use_coupon']) && $_POST['can_not_use_coupon'] == "1") ? "1" : "0";
			update_term_meta( $term_id, 'can_not_use_coupon', $new_can_not_use_coupon );
			$pool = (intval($_POST['pool']) > 0) ? intval($_POST['pool']) : '' ;
			$pool = (intval($_POST['pool']) > 0) ? intval($_POST['pool']) : '' ;
			$tax_enable = $_POST['tax_enable'];
			update_term_meta( $term_id, 'tax_enable', $tax_enable );
			$category_pos = (intval($_POST['category_pos']) > 0) ? intval($_POST['category_pos']) : 0 ;
			update_term_meta( $term_id, 'category_pos', $category_pos );
			$time_date = $_POST['time_date'];
			$time_open = $_POST['time_open'];
			$time_close = $_POST['time_close'];
			$array = array();
			if(is_array($time_date) && count($time_date) > 0){
				foreach ($time_date as $key => $value) {
					$array[] = array('date' => $value,'open' => $time_open[$key],'close' => $time_close[$key]);
				}
			}else{
				$array = "";
			}
			update_term_meta( $term_id, 'tax_time', $array );

			// custom date time
			$new_custome_date_type = $_POST['new_custome_date_type'];
		$new_custome_date_start_date = $_POST['new_custome_date_start_date'];
		$new_custome_date_end_date = $_POST['new_custome_date_end_date'];
		$new_custome_date_time_type = $_POST['new_custome_date_time_type'];
		$new_custome_date_start_time = $_POST['new_custome_date_start_time'];
		$new_custome_date_end_time = $_POST['new_custome_date_end_time'];
		$new_custome_date_status = $_POST['new_custome_date_status'];
		if ($new_custome_date_type != "" && count($new_custome_date_type) > 0) {
			$count = 0;
			$array = array();
			foreach ($new_custome_date_type as $item) {
				if ($item != "" && $new_custome_date_start_date[$count] != "" && $new_custome_date_time_type[$count] != "" && $new_custome_date_status[$count] != "") {
					$array[] = array(
						"date_type" => $item, 
						"start_date" => $new_custome_date_start_date[$count], 
						"end_date" => $new_custome_date_end_date[$count],
						"time_type" => $new_custome_date_time_type[$count],
						"start_time" => $new_custome_date_start_time[$count],
						"end_time" => $new_custome_date_end_time[$count],
						"status" => $new_custome_date_status[$count],
					);
				}
				$count++;
			}

			if (count($array) > 0) {
				update_term_meta( $term_id, 'tax_new_custom_date', $array );
			}else{
				update_term_meta( $term_id, 'tax_new_custom_date', '' );
			}
		}else{
			update_term_meta( $term_id, 'tax_new_custom_date', '' );
		}
		}

}  
add_action( 'edited_product-cat', 'dsmart_save_taxonomy_custom_meta_field', 10, 2 );  
add_action( 'create_product-cat', 'dsmart_save_taxonomy_custom_meta_field', 10, 2 );