<?php
function get_report_from_array($req_array)
{
	return new report(
		$req_array['ID'],
		$req_array['IP'],
		$req_array['Timestamp'],
		$req_array['Question_ID'],
		$req_array['User_ID'],
		$req_array['Text'],
		$req_array['Status']);
}
?>