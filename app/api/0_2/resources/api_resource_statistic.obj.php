<?php
class api_resource_statistic extends api_resource
{
	protected function build_XML($parameters)
	{
		// name of stat goes here: 
		$XML_newstatistic = $this->resource_XML->addChild('statistic');
		
		switch ($parameters['ID']) 
		{
			// Total number of responses
			case "responses":
				global $mydb;
				$statistic_value = $mydb->get_response_count();	
			break;	
			
			// number of calls to the API in the past hour
			case "api_calls_hourly":
				$api_calls = cache_get("api_calls");
				if ($api_calls)
				{
					$statistic_value = count($api_calls);
				}
				else
				{
					$statistic_value = 0;
				}
			break;	
			
			// responses in the past 24 hours
			case "responses_daily":
				$raw_data = cache_get("stats_hourly_posts");
				$raw_data[24] = cache_get("response_count_last_hour");
				$statistic_value = array_sum($raw_data);
			break;	
			
			// responses in the past 60 minutes
			case "responses_hourly":
				$statistic_value = cache_get("response_count_last_hour");
			break;	
			
			// responses in the past 60 seconds
			case "responses_minutly":
				global $mydb;
				$statistic_value = $mydb->get_response_count_since(gmmktime() - 60);
			break;	
			
			// total number of questions
			case "questions":
				$statistic_value = get_question_count();
			break;
			
			// total number of answers
			case "answers":
				global $mydb;
				$statistic_value = $mydb->get_answer_count();	
			break;
			
			// total number of users
			case "users":
				global $mydb;
				$statistic_value = $mydb->get_user_count();
			break;
			
			// total unique IPs in the responses
			case "unique_IPs":
				$statistic_value = cache_get("response_distinct_ip_count");
			break;
			
			// Throw an exception if ID not found
			default:
				throw new exception ("Statistic ID not found", 416);
			break;
		}
		
		// build the XML
		$XML_newstatistic->addChild('id', $parameters['ID']);
		$XML_newstatistic->addChild('value', $statistic_value);
		$XML_newstatistic->addChild('formatted', number_format($statistic_value));
		
	}
}
?>