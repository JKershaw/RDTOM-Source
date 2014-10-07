<?php
function get_formatted_admin_report($report) {
	$out.= '<br />
		 <a href="' . get_site_URL() . 'admin/edit/' . $report->get_Question_ID() . '">' . $report->get_Question_ID() . '</a>
		(' . get_formatted_admin_report_links($report) . '):' . nl2br(htmlentities(stripslashes($report->get_Text()))) . '<br />';
	
	return $out;
}

function get_formatted_admin_report_links($report) {
	$out.= '<a href="' . get_site_URL() . 'admin/?update_report=' . $report->get_ID() . '&new_status=fixed">fixed</a>, 
		 <a href="' . get_site_URL() . 'admin/?update_report=' . $report->get_ID() . '&new_status=incorrect">incorrect</a>, 
		 <a href="' . get_site_URL() . 'admin/?update_report=' . $report->get_ID() . '&new_status=clarified">clarified</a>, 
		 <a href="' . get_site_URL() . 'admin/?update_report=' . $report->get_ID() . '&new_status=noaction">no action taken</a>';
	
	return $out;
}

function get_open_report_count($reports_open = false) {
	global $mydb, $global_reports_open_count;
	
	// have we been given the open reports array?
	if ($reports_open) {
		$global_reports_open_count = count($reports_open);
		return $global_reports_open_count;
	}
	
	//need to see if we've already worked it out
	if (isset($global_reports_open_count)) {
		return $global_reports_open_count;
	}
	
	// need to work it out and remember it
	$reports_open = $mydb->get_reports(REPORT_OPEN);
	
	if ($reports_open && count($reports_open) > 0) {
		$global_reports_open_count = count($reports_open);
	} else {
		$global_reports_open_count = 0;
	}
	
	return $global_reports_open_count;
}

function get_open_report_count_string() {
	if (get_open_report_count() > 0) {
		return " (" . get_open_report_count() . ")";
	} else {
		return "";
	}
}

function get_admin_terms_checkboxes($term, $question = false) {
	global $mydb;
	
	$terms = $mydb->get_terms($term);
	
	if ($terms) {
		if ($question) {
			$question_terms = $question->get_terms($term);
		}
		
		foreach ($terms as $term) {
			$selected_string = "";
			if ($question_terms) {
				
				// is this rule set already chosen for this question?
				
				foreach ($question_terms as $question_term) {
					if ($question_term->get_ID() == $term->get_ID()) {
						$selected_string = "checked";
					}
				}
			}
			
			// special case where we want the Author
			if ($term->get_taxonomy() == "author-id") {
				try {
					$tmp_user = $mydb->get_user_from_ID($term->get_Name());
					$display_name = $tmp_user->get_Name();
				}
				catch(exception $e) {
					$display_name = "USER NOT FOUND";
				}
			} else {
				$display_name = $term->get_Name();
			}
			
			$out.= "<input $selected_string type=\"checkbox\" id=\"term_checkbox[" . $term->get_ID() . "]\" name=\"term_checkbox[" . $term->get_ID() . "]\">" . htmlentities(stripslashes($display_name)) . "<br />";
		}
	} else {
		$out.= "No terms found";
	}
	
	return $out;
}

function get_admin_terms_checkboxes_ajax($term, $question) {
	global $mydb;
	
	$terms = $mydb->get_terms($term);
	
	if ($terms) {
		if ($question) {
			$question_terms = $question->get_terms($term);
		}
		
		foreach ($terms as $term) {
			$style = "";
			if ($question_terms) {
				
				// is this rule set already chosen for this question?
				
				foreach ($question_terms as $question_term) {
					if ($question_term->get_ID() == $term->get_ID()) {
						$style = "style=\"font-weight: bold;\"";
					}
				}
			}
			
			// special case where we want the Author
			if ($term->get_taxonomy() == "author-id") {
				$tmp_user = $mydb->get_user_from_ID($term->get_Name());
				$display_name = $tmp_user->get_Name();
			} else {
				$display_name = $term->get_Name();
			}
			
			$out.= "<a $style id=\"term_" . $term->get_ID() . "_" . $question->get_ID() . "\" onclick=\"toggle_term_relationship(" . $term->get_ID() . ", " . $question->get_ID() . ")\">" . htmlentities(stripslashes($display_name)) . "</a> ";
		}
	} else {
		$out.= "No terms found";
	}
	
	return $out;
}
?>