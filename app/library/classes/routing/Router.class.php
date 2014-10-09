<?php
class Router
{
	static function route($localPath) {
		
		switch (UriPath::part(0)) {
			case "api":
				self::includeFile($localPath . "/api/router.php");
				break;

			case "forget":
				forget_remembered_questions();
				header('Location: ' . get_site_URL());
				die();
				break;

			case "report":
				report_question();
				set_up_question($_POST['report_question_ID']);
				self::includeFile($localPath . "/presentation/question.php");
				break;

			case "stats":
				self::includeFile($localPath . "/presentation/stats.php");
				break;

			case "admin":
				self::includeFile($localPath . "/presentation/admin.php");
				break;

			case "profile":
				self::includeFile($localPath . "/presentation/profile.php");
				break;

			case "passwordreset":
				self::includeFile($localPath . "/presentation/passwordreset.php");
				break;

			case "test":
				self::includeFile($localPath . "/presentation/test.php");
				break;

			case "about":
				self::includeFile($localPath . "/presentation/about.php");
				break;

			case "cat":
				self::includeFile($localPath . "/presentation/cat.php");
				break;

			case "forum":
				self::includeFile($localPath . "/presentation/forum.php");
				break;

			case "minimumskills":
				self::includeFile($localPath . "/presentation/minimumskills.php");
				break;

			case "question":
				set_up_question(UriPath::part(1));
				self::includeFile($localPath . "/presentation/question.php");
				break;

			default:
				set_up_question("random");
				self::includeFile($localPath . "/presentation/question.php");
				break;
		}
	}
	
	static function includeFile($fileToInclude) {
		
		// Nasty hack to get around issues of global variables needed in the includes - to be replaced with sensible code
		foreach ($GLOBALS as $name => $value) {
			global $$name;
			$$name = $value;
		}

		include ($fileToInclude);
	}
}
