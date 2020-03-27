<?php
// The following script defines any functions required by the various forms.
function create_form_input($name, $type, $label = '', $placeholder = '', $errors = array(), $values = 'POST', $options = array()) {
	
	if (isset($_SESSION['listing_id'])) {
		$lid = $_SESSION['listing_id'];
	} elseif (isset($_POST['lid'])) {
		$lid = $_POST['lid'];
	} else {
		$lid = $_GET['lid'];
	}
	
	// Assume no value already exists:
	$value = false;
	if ($values === 'SESSION') {
		if (isset($_SESSION[$name]) && !is_array($_SESSION[$name])) {
			$value = htmlspecialchars($_SESSION[$name], ENT_QUOTES, 'UTF-8');
		}
	} elseif ($values === 'POST') {
		if (isset($_POST[$name]) && !is_array($_POST[$name])) {
			$value = htmlspecialchars($_POST[$name], ENT_QUOTES, 'UTF-8');
		}
		// Strip slashes if Magic Quotes is enabled:
		if ($value && get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
	}
	
	// Check for a value in POST:
	// if (isset($_POST[$name])) $value = $_POST[$name];

	// Start the DIV:
	echo '<div class="form-group';
	// Add a class if an error exists:
	if (array_key_exists($name, $errors)) echo ' has-error';
	// Complete the DIV:
	echo '">';

	// Create the LABEL, if one was provided:
	if (!empty($label)) echo '<label for="' . $name . '" class="control-label">' . '<strong>' . $label . '</strong>' . '</label>';

// Conditional to determine what kind of element to create:
	if ($type === 'checkbox') {
// CHECKBOXES
		$db_listings = mysqli_connect(DB_HOST3, DB_USER3, DB_PASSWORD3, DB_NAME3);
		mysqli_set_charset($db_listings,'utf8'); /* sets UTF8 character set */
		
		$all_checkboxes = array();
		$selected_checkboxes = array();
		
		// Start the form input DIV:
		echo '<div class="control-label">';
		
		// Start the form input-group DIV:
		echo '<div class="input-group">';		
		
		// Select which tables to use by passing into $placeholder and exploding
		// $table is separate chkbx_ table
		// $column is found in listingcheckboxes table
		$placeholder = explode(',', $placeholder); // creates array
		$table = escape_data($placeholder[0], $db_listings);
		$column = escape_data($placeholder[1], $db_listings);
		
		if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
		
		$q = "SELECT id, name FROM " . $table . "";
		$r = mysqli_query($db_listings, $q);
		
		if ($r) {
			$totalRows = mysqli_num_rows($r);
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$all_checkboxes[$row['id']] = $row['name'];
			}
		} else {
			echo 'No results for table ' . $table . '.<br />';
		}		
		
		if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);		
		
		$q = "SELECT id, " . $column . " FROM listingcheckboxes WHERE listing_id = '$lid'";
		$r = mysqli_query($db_listings, $q);
		
		if ($r) {
			$totalRows = mysqli_num_rows($r);
			$row = mysqli_fetch_assoc($r);
			$selected_checkboxes = explode(',', $row[$column]); // turns into an array			
		} else {
			echo 'No results for column ' . $column . ' in listingcheckboxes table.<br />';
		}
		
		
		//create_form_input('list_checkboxes', 'checkbox', '', 'checkboxes, checkboxes', $listing_errors);
		if (!empty($all_checkboxes) && is_array($all_checkboxes)) {
			foreach ($all_checkboxes as $all_checkboxes_key => $all_checkboxes_value) {
				echo '<input type="checkbox" name="' . $name . '[]" value="' . $all_checkboxes_key . '" ';
				foreach ($selected_checkboxes as $selected_checkboxes_key => $selected_checkboxes_value) {
					if ($selected_checkboxes_value == $all_checkboxes_key) {
						echo 'checked="checked"';
					}
				}
				echo '/> ' . $all_checkboxes_value . '<br />';
			}
		}
		// Close DIVs:
		echo '</div>';
		echo '</div>';
		
		// Show the error message, if one exists:
		if (array_key_exists($name, $errors)) echo '<span class="help-block">' . $errors[$name] . '</span>';
		
	} elseif ($type === 'text' && $name === 'ship_phone') {
// PHONE	
		// Start the form input DIV:
		echo '<div class="control-label">';
		
		// Start the form input-group DIV:
		echo '<div class="input-group">';
		
		// Insert span
		echo '<span class="input-group-addon"><i class="fa fa-phone"></i></span>';
		
		// Start creating the input:
		echo '<input type="'. $type .'" name="' . $name . '" id="' . $name . '" data-plugin-masked-input data-input-mask="(999) 999-9999" placeholder="' . $placeholder . '" class="form-control"';
		
		// Add the value to the input:
		if ($value) echo ' value="' . $value . '"';
		
		// Check for additional options:
		if (!empty($options) && is_array($options)) {
			foreach ($options as $k => $v) {
				echo " $k=\"$v\"";
			}
		}
		
		// Complete the element:
		echo '>';
		
		// Close DIVs:
		echo '</div>';
		echo '</div>';
		
		// Show the error message, if one exists:
		if (array_key_exists($name, $errors)) echo '<span class="help-block">' . $errors[$name] . '</span>';
		
	} elseif ($type === 'currency') {
// CURRENCY	
		// Start the form input DIV:
		echo '<div class="control-label ">';
		
		// Start the form input-group DIV:
		echo '<div class="input-group">';
		
		// Insert span
		echo '<span class="input-group-addon"><i class="fa fa-usd"></i></span>';
		
		// Start creating the input:
		echo '<input name="' . $name . '" id="' . $name . '" placeholder="' . $placeholder . '" class="form-control"';
		
		// Add the value to the input:
		if ($value) echo ' value="' . $value . '"';
		
		// Check for additional options:
		if (!empty($options) && is_array($options)) {
			foreach ($options as $k => $v) {
				echo " $k=\"$v\"";
			}
		}
		
		// Complete the element:
		echo '>';
				
		echo '</div>';
		echo '</div>';
		
		if ($name === 'list_price') {
			echo '<div class="col-sm-3"></div><p class="mb-lg col-sm-6">Amount will be rounded to the nearest dollar.</p>';
		} elseif ($name === 'list_assessment' || $name === 'list_taxes') {
			echo '<div class="col-sm-3"></div><p class="mb-lg col-sm-6">OPTIONAL<br/>Amount will be rounded to the nearest dollar.</p>';
		}
		
		// Show the error message, if one exists:
		if (array_key_exists($name, $errors)) echo '<div class="col-sm-3"></div><span class="help-block">' . $errors[$name] . '</span>';
		
	} elseif ($type === 'text') {
// TEXT
		// Start the form input DIV:
		echo '<div class="control-label">';
		
		// Start creating the input:
		echo '<input type="' . $type . '" name="' . $name . '" id="' . $name . '" placeholder="' . $placeholder . '" class="form-control"';
		
		// Add the value to the input:
		if ($value) echo ' value="' . $value . '"';
		
		// Check for additional options:
		if (!empty($options) && is_array($options)) {
			foreach ($options as $k => $v) {
				echo " $k=\"$v\"";
			}
		}
		
		// Complete the element:
		echo '>';
		
		// Close DIV class="control-label:
		echo '</div>';
		
		// Show the error message, if one exists:
		if (array_key_exists($name, $errors)) echo '<span class="help-block">' . $errors[$name] . '</span>';

	} elseif ($type === 'password') {
// PASSWORD
		// Start the form input DIV:
		echo '<div class="control-label">';
		
		// Start the form input-group DIV:
		echo '<div class="input-group">';
		
		// Insert span
		echo '<span class="input-group-addon"><i class="fa fa-key"></i></span>';		
		
		// Start creating the input:
		echo '<input type="' . $type . '" name="' . $name . '" id="' . $name . '" placeholder="' . $placeholder . '" class="form-control"';
		
		// Add the value to the input:
		if ($value) echo ' value="' . $value . '"';
		
		// Check for additional options:
		if (!empty($options) && is_array($options)) {
			foreach ($options as $k => $v) {
				echo " $k=\"$v\"";
			}
		}
		
		// Complete the input element:
		echo '>';
		
		// Close DIVs:
		echo '</div>';
		echo '</div>';
		
		// Show the error message, if one exists:
		if (array_key_exists($name, $errors)) echo '<span class="help-block">' . $errors[$name] . '</span>';
		
	} elseif ($type === 'email') {
// EMAIL
		// Start the form input DIV:
		echo '<div class="control-label">';
		
		// Start the form input-group DIV:
		echo '<div class="input-group">';
		
		// Insert span
		echo '<span class="input-group-addon"><i class="fa fa-envelope"></i></span>';		
		
		// Start creating the input:
		echo '<input type="' . $type . '" name="' . $name . '" id="' . $name . '" placeholder="' . $placeholder . '" class="form-control"';
		
		// Add the value to the input:
		if ($value) echo ' value="' . $value . '"';
		
		// Check for additional options:
		if (!empty($options) && is_array($options)) {
			foreach ($options as $k => $v) {
				echo " $k=\"$v\"";
			}
		}
		
		// Complete the input element:
		echo '>';
		
		// Close DIVs:
		echo '</div>';
		echo '</div>';
		
		// Show the error message, if one exists:
		if (array_key_exists($name, $errors)) echo '<span class="help-block">' . $errors[$name] . '</span>';
		
	} elseif ($type === 'textarea') { // Create a TEXTAREA.
// TEXTAREA
		// Show the error message above the textarea (if one exists):
		if (array_key_exists($name, $errors)) echo '<span class="help-block">' . $errors[$name] . '</span>';

		// Start creating the textarea:
		// id was previously set to "textareaAutosize"
		echo '<textarea class="form-control" name="' . $name . '" id="' . $name . '" style="height: 300px; -ms-overflow-x: hidden; -ms-overflow-y: visible; -ms-word-wrap: break-word;" rows="3" data-plugin-textarea-autosize';
		
		// Check for additional options:
		if (!empty($options) && is_array($options)) {
			foreach ($options as $k => $v) {
				echo " $k=\"$v\"";
			}
		}

		// Complete the opening tag:
		echo ' >';		
		
		// Add the value to the textarea:
		if ($value) echo $value;

		// Complete the textarea:
		echo '</textarea>';
				
	} elseif ($type === 'boolean') {
// BOOLEAN YES or NO
		$data = array('0' => 'No', '1' => 'Yes');
		
		// Start the tag:
		echo '<select name="' . $name  . '" id="' . $name . '" class="form-control';
		
		// Add the error class, if applicable:
		if (array_key_exists($name, $errors)) echo ' error';

		// Close the tag:
		echo '">';
				
		// Create each option:
		foreach ($data as $k => $v) {
			echo '<option value="' . $k . '"';
			
			// Select the existing value:
			if ($value && $value == $k) echo ' selected="selected"';
			
			echo '>' . $v . '</option>';
			
		} // End of FOREACH.
	
		// Complete the tag:
		echo '</select>';
		
		// Add an error, if one exists:
		if (array_key_exists($name, $errors)) {
			echo '<span class="help-block">' . $errors[$name] . '</span>';
		}
	} elseif ($type === 'select') {
// SELECT
		$db_listings = mysqli_connect(DB_HOST3, DB_USER3, DB_PASSWORD3, DB_NAME3);
		mysqli_set_charset($db_listings,'utf8'); /* sets UTF8 character set */
		
		 if ($name === 'list_air_conditioning') {
// SELECT >> Create a list of basement finish types
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, type FROM select_air_conditioning ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['type'];
			}	
		} elseif ($name === 'list_basement') {
// SELECT >> Create a list of basement finish types
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, name FROM select_basement ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['name'];
			}	
		} elseif ($name === 'list_country') { 
// SELECT >> Create a list of countries.	
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			// Get all Countries
			$r = mysqli_query($db_listings, 'SELECT countryID, countryName FROM select_countries WHERE active = 1 ORDER BY countryID ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['countryID']] = $row['countryName'];
			}	 		
		} elseif ($name === 'list_fireplace_fuel') {
// SELECT >> Create a list of foundation types
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, name FROM select_fireplace_fuel ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['name'];
			}			
		} elseif ($name === 'list_foundation') {
// SELECT >> Create a list of foundation types
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, type FROM select_foundationtype ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['type'];
			}			
		} elseif ($name === 'list_garage_size') {
// SELECT >> Create a list of garage sizes
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, size FROM select_garagesize ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['size'];
			}
		} elseif ($name === 'list_garage_type') {
// SELECT >> Create a list of garage types
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, type FROM select_garagetype ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['type'];
			}
		} elseif ($name === 'list_lot_measurement') {
// SELECT >> Create a list of garage types
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			// Get all Street Directions
			$r = mysqli_query($db_listings, 'SELECT id, type FROM select_measurement ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['type'];
			}
		} elseif ($name === 'list_property_type') {
// SELECT >> Create a list of property types
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			// Get all Street Directions
			$r = mysqli_query($db_listings, 'SELECT id, name FROM select_propertytype ORDER BY ID ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['name'];
			}
		} elseif ($name === 'list_property_use') {
// SELECT >> Create a list of property use
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			// Get all Street Directions
			$r = mysqli_query($db_listings, 'SELECT id, name FROM select_propertyuse ORDER BY ID ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['name'];
			}
		} elseif ($name === 'list_roof_type') {
// SELECT >> Create a list of roofing types
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, name FROM select_rooftype ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['name'];
			}
		} elseif ($name === 'list_sewer') {
// SELECT >> Create a list of possible sewer connections
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, name FROM select_sewer ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['name'];
			}
		} elseif ($name === 'list_state_province') { 
// SELECT >> Create a list of States and Provinces
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			// Get all States and Provinces
			$r = mysqli_query($db_listings, 'SELECT stateProvinceID, stateProvinceName FROM select_statesprovinces WHERE active = 1 ORDER BY stateProvinceID ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['stateProvinceID']] = $row['stateProvinceName'];
			}	 		
		} elseif ($name === 'list_status') {
// SELECT >> Create a list of possible listing status
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, status FROM select_listingstatus ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['status'];
			}
		} elseif ($name === 'list_street_direction') {
// SELECT >> Create a list of street directions
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			// Get all Street Directions
			$r = mysqli_query($db_listings, 'SELECT streetDirID, streetDirName FROM select_streetdirection ORDER BY streetDirID ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['streetDirID']] = $row['streetDirName'];
			}
		} elseif ($name === 'list_street_suffix') {
// SELECT >> Create a list of street suffixes
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			// Get all Street Suffixes
			$r = mysqli_query($db_listings, 'SELECT streetSuffixID, streetSuffixName FROM select_streetsuffix ORDER BY streetSuffixID ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['streetSuffixID']] = $row['streetSuffixName'];
			}
		} elseif ($name === 'list_water') {
// SELECT >> Create a list of possible water supply
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, name FROM select_watersupply ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['name'];
			}
		} elseif ($name === 'list_room_floor_level') {
// SELECT >> Create a list of possible water supply
			$data = array();
			
			if (mysqli_more_results($db_listings)) mysqli_next_result($db_listings);
			
			$r = mysqli_query($db_listings, 'SELECT id, name FROM select_floor_level ORDER BY id ASC');
			$totalRows = mysqli_num_rows($r);
			
			for ($i = 0; $i < $totalRows; ++$i) {
				$row = mysqli_fetch_assoc($r);
				$data[$row['id']] = $row['name'];
			}
		} elseif ($name === 'list_year_built') {
// SELECT >> Create a list of past years starting from current year
			$data = array();
			$current_year = date('Y');
			$start = '1890';
			
			for ($i = $current_year; $i >= $start; $i--) {
				$data[0] = 'Unknown';
				$data[$i] = $i;
			}
		} elseif ($name === 'list_bedrooms' || 'list_floorlevels' || 'list_fireplace' || 'list_parking_spaces') {
// SELECT >> Create ascending numbers 0 to 30
			$data = array();
			$max = '30';
			
			$data[0] = 'None';
			
			for ($i = 1; $i <= $max; ++$i) {
				$data[$i] = $i;
			}
		}
		
		// Start the tag:
		echo '<select name="' . $name  . '" id="' . $name . '" class="form-control';
		
		// Add the error class, if applicable:
		if (array_key_exists($name, $errors)) echo ' error';

		// Close the tag:
		echo '">';
				
		// Create each option:
		foreach ($data as $k => $v) {
			echo '<option value="' . $k . '"';
			
			// Select the existing value:
			if ($value && $value == $k) echo ' selected="selected"';
			
			echo '>' . $v . '</option>';
			
		} // End of FOREACH.
	
		// Complete the tag:
		echo '</select>';
		
		// Add an error, if one exists:
		if (array_key_exists($name, $errors)) {
			echo '<span class="help-block">' . $errors[$name] . '</span>';
		}

	}// End of primary IF-ELSE.
	
	// Complete the DIV class="form-group col-md-12:
	echo '</div>';

} // End of the create_form_input() function.

// Omit the closing PHP tag to avoid 'headers already sent' errors!