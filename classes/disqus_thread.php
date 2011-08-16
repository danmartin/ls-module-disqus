<?

	class Disqus_Thread {
		public $identifier;
		public $user;
		public $forum;
		
		public function __construct(Disqus_User $user, Disqus_Forum $forum, $identifier) {
			$this->user = $user;
			$this->forum = $forum;
			$this->identifier = $identifier;
		}
		
		public function sync_blog_post($post) {
			$updated = $post->updated_at ? $post->updated_at : $post->created_at;
		
			if((Phpr_DateTime::now()->getInteger() - $updated->getInteger()) / 10000000 < 60 * 60) {
				return $post;
			}
			
			$endpoint = "http://disqus.com/api/thread_by_identifier/";
			$fields = array(
				'api_version' => "1.1",
				'forum_api_key' => $this->forum->api_key,
				'identifier' => $this->identifier,
				'title' => "Unknown"
			);
			
			$c1 = curl_init();
			curl_setopt($c1, CURLOPT_URL, $endpoint);
			curl_setopt($c1, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c1, CURLOPT_TIMEOUT, 40);
			curl_setopt($c1, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($c1, CURLOPT_SSL_VERIFYPEER, FALSE);
			$response = curl_exec($c1);
			
			if(curl_errno($c1) || curl_getinfo($c1, CURLINFO_HTTP_CODE) != 200) {
				return $post;
			}
			
			$d1 = json_decode($response);
			
			$thread_id = $d1->message->thread->id;
			
			$endpoint = "http://disqus.com/api/get_num_posts/?api_version=1.1&user_api_key=" . $this->user->api_key . "&thread_ids=" . $thread_id;
			
			curl_setopt($c1, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($c1, CURLOPT_POST, false);
			curl_setopt($c1, CURLOPT_POSTFIELDS, '');				
			curl_setopt($c1, CURLOPT_URL, $endpoint);
			
			$response = curl_exec($c1);
		
			if(curl_errno($c1) || curl_getinfo($c1, CURLINFO_HTTP_CODE) != 200) {
				return $post;
			}
		
			$d2 = json_decode($response);
			
			$message = $d2->message->$thread_id;
		
			$comment_count = (int)$message[1];
			
			if($comment_count == $post->approved_comment_num) {
				return $post;
			}
			
			$endpoint = "http://disqus.com/api/get_thread_posts/?api_version=1.1&user_api_key=" . $this->user->api_key . "&thread_id=" . $thread_id;
			
			curl_setopt($c1, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($c1, CURLOPT_POST, false);
			curl_setopt($c1, CURLOPT_POSTFIELDS, '');
			curl_setopt($c1, CURLOPT_URL, $endpoint);
			
			$response = curl_exec($c1);
		
			if(curl_errno($c1) || curl_getinfo($c1, CURLINFO_HTTP_CODE) != 200) {
				return $post;
			}
			
			$list = json_decode($response);
			
			foreach($list->message as $item) {
				if($item->status !== 'approved')
					continue;
				
				$author = isset($item->author) ? $item->author : $item->anonymous_author;
				
				$comment = Blog_Comment::create()->find_by_author_url($item->id);
				
				$author_name = isset($item->author) ? $author->display_name ? $author->display_name : $author->username : $author->name;
				
				if(!$comment) {
					$comment = Blog_Comment::create();
					$comment->init_columns_info('front_end');
					$comment->post_id = $post->id;
					$comment->author_ip = $item->ip_address;
					$comment->content = $item->message;
					$comment->author_url = $item->id; // hacky
					$comment->author_email = $author->email;
					$comment->author_name = $author_name;
					$comment->save();
					$comment->set_status(Blog_Comment_Status::status_approved);
					$comment->save();
				}
			}
			
			$post->updated_at = Phpr_DateTime::now();
			$post->save();
			$post = Blog_Post::create()->find($post->id);
			
			return $post;
		}
	}