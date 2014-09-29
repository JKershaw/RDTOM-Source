<?php
/*
 * All the functions needed to show a forum
 * Forum 	-< Topics 	-< Threads	-< Posts
 * 
 *  * RDTOM.com/Forum	/[topic]	/[thread]	
 * 				/0		/1			/2		
 */

/*
 * This code is different to the rest of the site. It's a side-project-of-sorts to create a self-contained forum
 */

function show_forum()
{
	$forum = new forum();
	if ($_POST)
	{
		$forum->process_post();
	}
	$forum->show();
}

/*
 * The forum objects
 */

class forum
{
	public function show()
	{
		global $url_array;
		// determine what page we're looking at
		if ($url_array[1])
		{
			// either a topic or a thread
			if ($url_array[2])
			{
				// a thread
				$thread = get_thread_from_slug($url_array[2], $url_array[1]);
				if ($thread)
				{
					//TODO save the fact this thread has been viewed
					$out .= $thread->get_body();
				}
				else
				{
					$error_message = "Sorry, the Thread you were looking for couldn't be found.";
				}
			}
			else
			{
				// a topic
				$topic = get_topic_from_slug($url_array[1]);
				if ($topic)
				{
					$out .= $topic->get_body();
				}
				else
				{
					$error_message = "Sorry, the Topic you were looking for couldn't be found.";
				}
			}
		}
		
		// if it's not a thread or a topic (or the slug couldn't be found)
		if (!$out)
		{
			
			// latest threads
			$out .= "<h3>Latest Threads</h3>";
				
			// latest Threads
			$latest_threads = get_latest_threads();
			if ($latest_threads)
			{
				$out .= "<ol class=\"threads\">";
				foreach($latest_threads as $thread)
				{
					$out .= $thread->get_head(true);
				}
				$out .= "</ol>";
			}
			else
			{
				$out .= "<p><i>No threads found</i></p>";
			}
			
			// Forums
			$out .= "<h3>Forum</h3>";
			
			if ($error_message)
			{
				$out .= "<p class=\"error_string\">" . $error_message . "</p>";
			}
			
			// Topics
			$topics = get_topics();
			if ($topics)
			{
				$out .= "<ol class=\"topics\">";
				foreach($topics as $topic)
				{
					$out .= $topic->get_head();
				}
				$out .= "</ol>";
				
			}
			else
			{
				$out .= "<p><i>No topics found</i></p>";
			}
			
		}
		
		// display the forum
		echo $out;
	}
	
	public function process_post()
	{
		// must be logged in to post anything
		if (!is_logged_in())
		{
			return true;
		}
		
		global $myPDO, $user;
		
		// a POST has been submitted, so handle it
		if($_POST['forum_form_name'] == "new_thread")
		{
			
			// clean input
			if (trim($_POST['new_thread_title']) == "")
			{
				throw new exception ("You must give your thread a title");
			}
			
			if (trim($_POST['new_thread_body']) == "")
			{
				throw new exception ("You must give your thread some text");
			}
			
			if (!get_topic_from_id($_POST['new_thread_topic_ID']))
			{
				throw new exception ("Attempted to save a thread for a topic which doesn't exist");
			}
			
			// make the new thread
			$slug = str_replace(" ", "-", preg_replace("[^A-Za-z0-9]", "", strtolower(substr($_POST['new_thread_title'], 0, 100))));
			
			// is this a unique slug?
			if (get_thread_from_slug($slug))
			{
				// no! Append a number
				$slug_ID = 2;
				while(get_thread_from_slug($slug . "-" . $slug_ID))
				{
					$slug_ID++;
					
					if ($slug_ID > 100)
					{
						throw new exception ("Too many threads with that slug");
					}
				}
				$slug .= "-" . $slug_ID;
			}
			
			$statement = $myPDO->prepare("
			INSERT 
				INTO rdtom_forum_threads 
				(
					Title ,
					Creator_User_ID ,
					Slug,
					Timestamp,
					Topic_ID
				)
				VALUES 
				(
					:Title ,
					:Creator_User_ID ,
					:Slug,
					:Timestamp,
					:Topic_ID
				);
			");
			
			if(!$statement->execute(
				array(	':Title' => $_POST['new_thread_title'], 
						':Creator_User_ID' => $user->get_ID(), 
						':Slug' => $slug, 
						':Timestamp' => gmmktime(),
						':Topic_ID' => $_POST['new_thread_topic_ID'])
				)
			)
			{
				throw new exception("Errr saving thread: " . print_r( $statement->errorInfo(), true));
			}
				
			$thread_ID = $myPDO->lastInsertId();
			
			// make the first post in the thread
			$this->save_post($_POST['new_thread_body'], $thread_ID, $_POST['new_thread_topic_ID'], $user->get_ID());
			
			// goto the thread now it has been created created
			$thread = get_thread_from_id($thread_ID);
			header("Location: " . $thread->get_URL());
			
		}
		elseif($_POST['forum_form_name'] == "new_post")
		{
			$thread = get_thread_from_id($_POST['new_post_thread_ID']);
			
			$topic = $thread->get_parent_topic();
			
			$this->save_post($_POST['new_post_body'], $_POST['new_post_thread_ID'], $topic->get_ID(), $user->get_ID());
			
			header("Location: " . $thread->get_URL());
		}
		elseif($_POST['forum_form_name'] == "edit_post")
		{
			$this->edit_post($_POST['edit_post_text'], $_POST['edit_post_ID']);
			
			$post = get_post_from_id($_POST['edit_post_ID']);
			
			$thread = $post->get_parent_thread();
			
			header("Location: " . $thread->get_URL());
		}
		else
		{
			throw new exception("Form not found");
		}
		
	}
	
	private function save_post($text, $thread_ID, $topic_ID, $user_ID)
	{
		global $myPDO;
		
		if (!get_thread_from_id($thread_ID))
		{
			throw new exception ("Attempted to save a post for a thread which doesn't exist");
		}
			
		if (!get_topic_from_id($topic_ID))
		{
			throw new exception ("Attempted to save a post for a topic which doesn't exist");
		}
		
		if (!trim($text))
		{
			throw new exception ("No text given");
		}
		$statement = $myPDO->prepare("
		INSERT 
			INTO rdtom_forum_posts 
			(
				Text ,
				Creator_User_ID ,
				Timestamp,
				Topic_ID,
				Thread_ID
			)
			VALUES 
			(
				:Text ,
				:Creator_User_ID ,
				:Timestamp,
				:Topic_ID,
				:Thread_ID
			);
		");
		
		if(!$statement->execute(
			array(	':Text' => $text, 
					':Creator_User_ID' => $user_ID,
					':Timestamp' => gmmktime(),
					':Topic_ID' => $topic_ID,
					':Thread_ID' => $thread_ID)
			)
		)
		{
			throw new exception("Error saving post: " . print_r( $statement->errorInfo(), true));
		}
	}
	
	private function edit_post($text, $post_ID)
	{
		global $myPDO, $user;
		
		$post = get_post_from_id($post_ID);
		if (!$post)
		{
			throw new exception ("Attempted to edit a post which doesn't exist");
		}
			
		$post_author = $post->get_author();
		
		//if (($post_author->get_ID() != $user->get_ID()) && !is_admin())
		if ($post_author->get_ID() != $user->get_ID())
		{
			throw new exception ("You do not have permission to edit this post");
		}
	
		if (!trim($text))
		{
			throw new exception ("No text given");
		}
		
		
		$statement = $myPDO->prepare("
			UPDATE rdtom_forum_posts SET Text = :Text, Edited_Timestamp = :Edited_Timestamp WHERE ID = :ID;
			");
		
		if(!$statement->execute(
			array(	':Text' => $text, 
					':ID' => $post_ID,
					':Edited_Timestamp' => gmmktime())
			)
		)
		{
			throw new exception("Error editing post: " . print_r( $statement->errorInfo(), true));
		}
	}
	
}

class topic
{
	private $data;
	
	public function topic($data)
	{
		$this->data = $data;
	}
	
	public function get_head()
	{
		// get the "head" version of the topic, the one that appears on the front page
		if ($this->data['Divider'])
		{
			return "
			
				<li class=\"topicslist_divider\">
					<div class=\"topicslist_divider_wrap\">
						" . htmlentities($this->data['Title']) . "
					</div>
				</li>";
		}
		else
		{
			$latest_post = $this->get_latest_post();
			
			$out .= "<li class=\"topicslist_topic\">
				<div class=\"topicslist_topic_wrap\">
					<a href=\"" . $this->get_URL() . "\">" . htmlentities($this->data['Title']) . "</a>
					<br /><i>" . htmlentities($this->data['Blurb']) . "</i>";
					if ($latest_post)
					{
						$out .= $latest_post->get_latest_post_string();
					}
					$out .= "</li>
				</div>";
				
			return $out;
		}
	}
	
	public function get_body()
	{
		$out .= "<h3><a href=\"" . get_site_URL() . "forum\">Forum</a> > " . htmlentities($this->data['Title']) . "</h3>";
		
		// get the "body" version of the topic, the page version of the topic listing all the threads
		$threads = get_threads_from_topic_id($this->data['ID']);
		
		if ($threads)
		{
			$out .= "<ol class=\"threads\">";
			foreach($threads as $thread)
			{
				$out .= $thread->get_head(true);
			}
			$out .= "</ol>";
		}
		else
		{
			$out .= "<p></i>No threads found</i></p>";
		}
		
		$out .= "
		<h3>Start new thread</h3>";
		if (is_logged_in())
		{
			$out .= "
			<form name=\"form_new_thread\" id=\"form_new_thread\" method=\"post\" action=\"" . get_site_URL() . "forum\" >
				<input type=\"hidden\" name=\"forum_form_name\" value=\"new_thread\" />
				<input  type=\"text\"  style=\"width:500px\" name=\"new_thread_title\" />
				<textarea name=\"new_thread_body\" style=\"width:500px\" name=\"new_thread_body\" cols=\"40\" rows=\"5\"></textarea>
				<input type=\"hidden\" name=\"new_thread_topic_ID\" value=\"" . $this->data['ID'] . "\" />
				<br />
				<a class=\"button\" id=\"new_thread_button\" onclick=\"document.form_new_thread.submit();return false;\"/>Start New Thread</a>
			</form>";
		}
		else
		{
			$out .= "<i>You must be logged in to start a thread</i>";
		}
		
		
		return $out;
	}
	
	public function get_latest_post()
	{
		return get_latest_posts_from_topic_id($this->data['ID']);
	}
	
	public function get_URL()
	{
		return get_site_URL() . "forum/" . $this->data['Slug'];
	}
	
	public function get_slug()
	{
		return $this->data['Slug'];
	}
	
	public function get_title()
	{
		return $this->data['Title'];
	}
	
	public function get_ID()
	{
		return $this->data['ID'];
	}
}

class thread
{
	private $data;
	
	public function __construct($data)
	{
		$this->data = $data;
	}
	
	public function get_head($hide_title = false)
	{
		// get the "head" version of the topic, the one that appears on the front page
		
		$latest_post = $this->get_latest_post();
		
		$out .= "<li class=\"threadslist_thread\">
			<div class=\"threadslist_thread_wrap\">
				<a href=\"" . $this->get_URL() . "\">" . htmlentities(stripslashes($this->data['Title'])) . "</a>";
				if ($latest_post)
				{
					$out .= "<br />" . $latest_post->get_latest_post_string($hide_title);
				}
				$out .= "
			</div>
		</li>";
		// get the "head" version of the thread, the one that appears on the topic page & front page
		
		return $out;
	}
	
	public function get_body()
	{
		// get the "body" version of te thread, the page version of the thread listing all the posts
		$parent_topic = $this->get_parent_topic();
		
		$out .= "<h3><a href=\"" . get_site_URL() . "forum\">Forum</a> > <a href=\"" . $parent_topic->get_URL() . "\">" . htmlentities(stripslashes($parent_topic->get_title())) . "</a> > " . htmlentities(stripslashes($this->data['Title'])) . "</h3>";
		
		// get the "body" version of the topic, the page version of the topic listing all the threads
		$posts = get_posts_from_thread_id($this->data['ID']);
		
		if ($posts)
		{
			$out .="<ol class=\"posts\">";
			foreach($posts as $post)
			{
				$out .= $post->get_body();
			}
			$out .="</ol>";
		}
		else
		{
			$out .= "<p></i>No posts found</i></p>";
		}
		
		$out .= "
		<h3>Reply</h3>";
		if (is_logged_in())
		{
			$out .= "
			<form name=\"form_new_post\" id=\"form_new_post\" method=\"post\" action=\"" . get_site_URL() . "forum\" >
				<input type=\"hidden\" name=\"forum_form_name\" value=\"new_post\" />
				<textarea name=\"new_post_body\" style=\"width:500px\" name=\"new_post_body\" cols=\"40\" rows=\"10\"></textarea>
				<input type=\"hidden\" name=\"new_post_thread_ID\" value=\"" . $this->data['ID'] . "\" />
				<br />
				<a class=\"button\" id=\"new_thread_button\" onclick=\"document.form_new_post.submit();return false;\"/>Post</a>
			</form>";
		}
		else
		{
			$out .= "<i>You must be logged in to leave a post</i>";
		}
		
		
		return $out;
	}
	
	public function get_URL()
	{
		
		$parent_topic = $this->get_parent_topic();
		return get_site_URL() . "forum/" . $parent_topic->get_slug() . "/" . $this->data['Slug'];
	}
	
	public function get_Title()
	{
		return $this->data['Title'];
	}
	
	public function get_parent_topic()
	{
		return get_topic_from_id($this->data['Topic_ID']);
	}
	
	public function get_latest_post()
	{
		return get_latest_posts_from_thread_id($this->data['ID']);
	}
}

class post
{
	private $data;
	private $author;
	
	public function __construct($data)
	{
		$this->data = $data;
	}
	public function get_body()
	{
		global $user;
		
		$author = $this->get_author();
		
		$out .= "
		<li class=\"forum_post\">
			<div class=\"forum_post_meta\">
				<div class=\"forum_post_meta_wrap\">
					<img style=\"height:40px;width:40px\"src=\"" . get_gravatar($author->get_Email(), 40) . "\" /><br />
					<span class=\"forum_post_meta_name\">" . htmlentities($author->get_Name()) . "</span><br />
					<span class=\"forum_post_meta_posts\">Posts: " . number_format(get_post_count_from_user_id($this->data['Creator_User_ID'])) . "</span>
				</div>
			</div>
			<div class=\"forum_post_content\">
				<div class=\"forum_post_content_wrap\">
				
					<p id=\"forum_post_" . $this->data['ID'] . "\">" . nl2br(make_links_clickable(htmlentities(stripslashes($this->data['Text'])))) . "</p>
					";
		if (($user && ($user->get_ID() == $author->get_ID())) || is_admin())
		{
			$out .= "
		
					<span id=\"forum_post_edit_" . $this->data['ID'] . "\" style=\"display:none\">
						<form name=\"form_edit_post_" . $this->data['ID'] . "\" id=\"form_edit_post\" method=\"post\" action=\"" . get_site_URL() . "forum\" >
							<input type=\"hidden\" name=\"forum_form_name\" value=\"edit_post\" />
							<textarea name=\"edit_post_text\" style=\"width:800px\" name=\"edit_post_text\" cols=\"40\" rows=\"10\">" . htmlentities(stripslashes($this->data['Text'])) . "</textarea>
							<input type=\"hidden\" name=\"edit_post_ID\" value=\"" . $this->data['ID'] . "\" />
							<br />
							<a class=\"button\" id=\"new_thread_button\" onclick=\"document.form_edit_post_" . $this->data['ID'] . ".submit();return false;\"/>Edit</a> 
							<a class=\"button\" id=\"new_thread_button\" onclick=\"$('#forum_post_edit_" . $this->data['ID'] . "').hide();$('#forum_post_" . $this->data['ID'] . "').fadeIn();\"/>Cancel</a>
						</form>
					</span>";
		}
			$out .= "		
					<div class=\"forum_post_content_meta\">";
		
		$out .= "Posted " . $this->get_freshness_html();
		if ($this->data['Edited_Timestamp'] > 0)
		{
			$out .= " (edited " . $this->get_edited_freshness_html() . ")";
		
		}
		if (($user && ($user->get_ID() == $author->get_ID())) || is_admin())
		{
			$out .= " <a onclick=\"$('#forum_post_" . $this->data['ID'] . "').hide();$('#forum_post_edit_" . $this->data['ID'] . "').fadeIn();\">Edit</a>";
		}
		
		$out .= "</div>
				</div>
			</div>
		</li>
		";
				
		return $out;
	}
	
	public function get_latest_post_string($hide_title = false)
	{
		$parent_thread = $this->get_parent_thread();
		if ($hide_title)
		{
			return "<div class=\"latest_post_string\">Latest post by <strong>" . htmlentities($this->get_author_name()) . "</strong>, " . $this->get_freshness_html() . "</div>";
		}
		else
		{
			return "<div class=\"latest_post_string\">Latest post by <strong>" . htmlentities($this->get_author_name()) . "</strong> in <a href=\"" . $parent_thread->get_URL() . "\">" . htmlentities(stripslashes($parent_thread->get_Title())) . "</a>, " . $this->get_freshness_html() . "</div>";
		}
		
	}
	
	public function get_author()
	{
		global $mydb;
		if (!$this->author)
		{
			$this->author = $mydb->get_user_from_ID($this->data['Creator_User_ID']);
		}
		return $this->author;
		
	}
	
	public function get_author_name()
	{
		$author = $this->get_author();
		return $author->get_Name();
		
	}
	
	public function get_parent_thread()
	{
		return get_thread_from_id($this->data['Thread_ID']);
	}
	
	public function get_freshness_html()
	{
		return "<span title=\"" . date("r", $this->data['Timestamp']) . "\">" . time_elapsed_string($this->data['Timestamp']) . "</span>";
	}
	
	public function get_edited_freshness_html()
	{
		return "<span title=\"" . date("r", $this->data['Edited_Timestamp']) . "\">" . time_elapsed_string($this->data['Edited_Timestamp']) . "</span>";
	}
}

/*
 * Forum support functions
 */


function get_latest_threads($limit = 15)
{
	global $myPDO;
	
	if ($limit == 1)
	{
		return get_latest_thread();
	}
	
	$statement = $myPDO->prepare("
		SELECT rdtom_forum_threads.*, thread_id, MAX(rdtom_forum_posts.Timestamp) AS NewestDate
		FROM rdtom_forum_posts
		JOIN rdtom_forum_threads
		ON thread_id = rdtom_forum_threads.ID
		GROUP BY thread_id
		ORDER BY `NewestDate` Desc
		LIMIT 0, :limit
	");
	$statement->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
	$statement->execute();
	$results = $statement->fetchAll();
	
	$out = Array();
	
	foreach ($results as $result)
	{
		$out[] = new thread($result);
	}
	
	return $out;
}

function get_latest_thread()
{
	global $myPDO;
	$statement = $myPDO->prepare("
		SELECT rdtom_forum_threads.*, thread_id, MAX(rdtom_forum_posts.Timestamp) AS NewestDate
		FROM rdtom_forum_posts
		JOIN rdtom_forum_threads
		ON thread_id = rdtom_forum_threads.ID
		GROUP BY thread_id
		ORDER BY `NewestDate` Desc
		LIMIT 0, 1
	");
	$statement->execute();
	$result = $statement->fetch();

	return new thread($result);

}

function get_topics()
{
	global $myPDO;
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_topics ORDER BY rdtom_forum_topics.Order Asc");
	$statement->execute();
	$results = $statement->fetchAll();
	
	if (!$results)
	{
		throw new exception("Topics not found");
	}
	else
	{
		foreach ($results as $result)
		{
			$out[] = new topic($result);
		}
		
		return $out;
	}
}

function get_thread_from_slug($slug)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_threads WHERE Slug = :Slug");
	$statement->execute(array(':Slug' => $slug));
	$results = $statement->fetch();
	
	if (!$results)
	{
		return false;
	}
	
	return new thread($results);
}

function get_thread_from_id($ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_threads WHERE ID = :ID");
	$statement->execute(array(':ID' => $ID));
	$results = $statement->fetch();
	
	if (!$results)
	{
		return false;
	}
	
	return new thread($results);
}

function get_thread_from_random()
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_threads ORDER BY RAND() LIMIT 0,1");
	$statement->execute();
	$results = $statement->fetch();
	
	if (!$results)
	{
		return false;
	}
	
	return new thread($results);
}
	
function get_topic_from_slug($slug)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_topics WHERE Slug = :Slug");
	$statement->execute(array(':Slug' => $slug));
	$results = $statement->fetch();
	
	if (!$results)
	{
		return false;
	}
	
	return new topic($results);
}
	
function get_topic_from_id($ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_topics WHERE ID = :ID");
	$statement->execute(array(':ID' => $ID));
	$results = $statement->fetch();
	
	if (!$results)
	{
		return false;
	}
	
	return new topic($results);
}

function get_threads_from_topic_id($ID)
{
	global $myPDO;
	$statement = $myPDO->prepare("
		SELECT rdtom_forum_threads.*, thread_id, MAX(rdtom_forum_posts.Timestamp) AS NewestDate
		FROM rdtom_forum_posts
		JOIN rdtom_forum_threads
		ON thread_id = rdtom_forum_threads.ID
		WHERE rdtom_forum_threads.Topic_ID = :Topic_ID
		GROUP BY thread_id
		ORDER BY `NewestDate` Desc
	");
	$statement->execute(array(':Topic_ID' => $ID));
	$results = $statement->fetchAll();
	
	$out = Array();
	
	foreach ($results as $result)
	{
		$out[] = new thread($result);
	}
	
	return $out;
}

function get_posts_from_thread_id($ID)
{
	global $myPDO;
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_posts WHERE Thread_ID = :Thread_ID ORDER BY Timestamp Asc");
	$statement->execute(array(':Thread_ID' => $ID));
	$results = $statement->fetchAll();
	
	$out = Array();
	
	foreach ($results as $result)
	{
		$out[] = new post($result);
	}
	
	return $out;
}


function get_post_from_id($ID)
{
	global $myPDO;
	
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_posts WHERE ID = :ID");
	$statement->execute(array(':ID' => $ID));
	$results = $statement->fetch();
	
	if (!$results)
	{
		return false;
	}
	
	return new post($results);
}

function get_latest_posts_from_thread_id($ID)
{
	global $myPDO;
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_posts WHERE Thread_ID = :Thread_ID ORDER BY Timestamp Desc Limit 1");
	$statement->execute(array(':Thread_ID' => $ID));
	$results = $statement->fetch();
	
	if($results)
	{
		return new post($results);
	}
	else
	{
		return false;
	}
}

function get_latest_posts_from_topic_id($ID)
{
	global $myPDO;
	$statement = $myPDO->prepare("SELECT * FROM rdtom_forum_posts WHERE Topic_ID = :Topic_ID ORDER BY Timestamp Desc Limit 1");
	$statement->execute(array(':Topic_ID' => $ID));
	$results = $statement->fetch();
	
	if($results)
	{
		return new post($results);
	}
	else
	{
		return false;
	}
}


function get_post_count_from_user_id($ID)
{
	global $myPDO;
	$statement = $myPDO->prepare("SELECT count(*) as count FROM rdtom_forum_posts WHERE Creator_User_ID = :Creator_User_ID");
	$statement->execute(array(':Creator_User_ID' => $ID));
	$results = $statement->fetch();
	
	return $results['count'];
}

/* Formatting Functions
 * 
 */

// from http://stackoverflow.com/questions/5341168/best-way-to-make-links-clickable-in-block-of-text
function make_links_clickable($text){
    return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Z()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" rel="nofollow">$1</a>', $text);
}

function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array()) {
	$url = get_http_or_https() . '://www.gravatar.com/avatar/';
	$url.= md5(strtolower(trim($email)));
	$url.= "?s=$s&d=$d&r=$r";
	if ($img) {
		$url = '<img src="' . $url . '"';
		foreach ($atts as $key => $val) $url.= ' ' . $key . '="' . $val . '"';
		$url.= ' />';
	}
	return $url;
}