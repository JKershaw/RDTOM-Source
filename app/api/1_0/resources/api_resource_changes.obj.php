<?php
class api_resource_changes extends api_resource
{
	protected function build_XML($parameters)
	{
		if (!$parameters['since'])
		{
			throw new exception ("since paramter missing. You must supply a GM timestamp in the since perameter", 400);
		}
		
		$comments = get_comments_since($parameters['since']);
		
		$questions_edited = Array();
		$questions_deleted = Array();
		
		// if there are comments
		if ($comments)
		{
			foreach ($comments as $comment)
			{
				// filter to get any deletions and any edits
				if ($comment->get_Type() == QUESTION_CHANGED)
				{
					$questions_edited[] = $comment->get_Question_ID();
				}
				elseif ($comment->get_Type() == QUESTION_DELETED)
				{
					$questions_deleted[] = $comment->get_Question_ID();
				}
			}
			
			// remove duplicates - a question may be edited (or deleted, if there's an error) multiple times
			$questions_edited = array_unique($questions_edited);
			$questions_deleted = array_unique($questions_deleted);
			
			// if a question was edited then deleted, remove it from the edited list
			$questions_edited = array_diff($questions_edited, $questions_deleted);
			
			sort($questions_edited);
			sort($questions_deleted);
			
		}
		
		$XML_updates = $this->resource_XML->addChild('updates');
		
		$XML_change = $XML_updates->addChild('update');
		foreach ($questions_edited as $id)
		{
			$XML_change->addChild('id', $id);
		}
		
		$XML_delete = $XML_updates->addChild('delete');
		foreach ($questions_deleted as $id)
		{
			$XML_delete->addChild('id', $id);
		}
		
	}
}
?>