<?

	class Disqus_Backups extends Backend_Controller {
		protected $required_permissions = array('disqus:manage_backups');

		public function __construct() {
			parent::__construct();
			$this->app_tab = 'disqus';
			$this->app_module_name = 'Disqus';
			$this->app_page = 'backups';
		}
		
		public function index() {
			$this->app_page_title = 'Import or Export Data';
			$this->app_page = 'backups';
		}
		
		/*
		 * Export
		 */
		
		public function export() {
			$this->app_page_title = 'Export';
		}
		
		protected function index_onExportBlogComments() {
			try {
				Phpr::$response->redirect(url('/disqus/backups/get_blog_comments/'));
			}
			catch (Exception $ex) {
				Phpr::$response->ajaxReportException($ex, true, true);
			}
		}
		
		protected function index_onExportWikiComments() {
			try {
				Phpr::$response->redirect(url('/disqus/backups/get_wiki_comments/'));
			}
			catch (Exception $ex) {
				Phpr::$response->ajaxReportException($ex, true, true);
			}
		}
		
		public function get_blog_comments($name) {
			try {
				$this->app_page_title = 'Download Blog Comments';
				
				header('Content-type: application/octet-stream');
				header('Content-Disposition: attachment; filename="blog_comments.xml"');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: pre-check=0, post-check=0, max-age=0');
				header('Accept-Ranges: bytes');
?>
<?= '<?xml version="1.0" encoding="utf-8"?>' ?>
<rss version="2.0"
     xmlns:excerpt="http://wordpress.org/export/1.0/excerpt/"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dsq="http://www.disqus.com/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:wp="http://wordpress.org/export/1.0/"
>
<?
  $list = Blog_Post::create()->find_all();
  $site_url = site_url('/', true);
?>
<channel>
     <title>Site Title</title>
     <link><?= $site_url ?></link>
     <description>Site Description</description>
     <pubDate>Wed, 08 Dec 2008 19:01:52 +0000</pubDate>
     <generator>http://wordpress.org/?v=3.0.3</generator>
     <language>en</language>
     <wp:wxr_version>1.0</wp:wxr_version>
     <wp:base_site_url><?= $site_url ?></wp:base_site_url>
     <wp:base_blog_url><?= $site_url ?>blog/</wp:base_blog_url>
     <wp:category><wp:category_nicename>uncategorized</wp:category_nicename><wp:category_parent></wp:category_parent><wp:cat_name><![CDATA[Uncategorized]] > </wp:cat_name></wp:category>
  <? foreach($list as $post): ?>
    <item>
      <title><?= h($post->title) ?></title>
      <link><?= $site_url ?>blog/post/<?= $post->url_title ?>/</link>
      <pubDate><?= date("D, j M Y G:i:s", strtotime($post->published_date)) ?> +0000</pubDate>
      <dc:creator><![CDATA[<?= Users_User::create()->find($post->created_user_id)->login ?>]] > </dc:creator>
      <category><![CDATA[<?= Blog_Category::create()->find($post->category_id)->name ?>]] > </category>
      <category domain="category" nicename="<?= Blog_Category::create()->find($post->category_id)->url_name ?>"><![CDATA[<?= Blog_Category::create()->find($post->category_id)->name ?>]] > </category>
      <guid isPermaLink="false"><?= $site_url ?>blog/post/<?= $post->url_title ?>/</guid>
      <description></description>
      <content:encoded><![CDATA[<?= $post->content ?>]] > </content:encoded>
      <excerpt:encoded><![CDATA[]] > </excerpt:encoded>
      <dsq:thread_identifier>post_<?= $post->id ?></dsq:thread_identifier>
      <wp:post_id><?= $post->id ?></wp:post_id>
      <wp:post_date><?= date("D, j M Y G:i:s", strtotime($post->created_at)) ?> +0000</wp:post_date>
      <wp:post_date_gmt><?= date("D, j M Y G:i:s", strtotime($post->created_at)) ?> +0000</wp:post_date_gmt>
      <wp:comment_status>open</wp:comment_status>
      <wp:ping_status>open</wp:ping_status>
      <wp:post_name><?= $post->url_title ?></wp:post_name>
      <wp:status>publish</wp:status>
      <wp:post_parent>0</wp:post_parent>
      <wp:menu_order>0</wp:menu_order>
      <wp:post_type>post</wp:post_type>
      <? foreach($post->approved_comments as $comment): ?>
        <wp:comment>
          <wp:comment_id><?= $comment->id ?></wp:comment_id>
          <wp:comment_author><![CDATA[<?= $comment->author_name ?>]] > </wp:comment_author>
          <wp:comment_author_email><?= $comment->author_email ?></wp:comment_author_email>
          <wp:comment_author_url><?= $comment->author_url ?></wp:comment_author_url>
          <wp:comment_author_IP><?= $comment->author_ip ?></wp:comment_author_IP>
          <wp:comment_date><?= date('Y-m-d H:i:s', strtotime($comment->created_at)) ?></wp:comment_date>
          <wp:comment_date_gmt><?= date('Y-m-d H:i:s', strtotime($comment->created_at)) ?></wp:comment_date_gmt>
          <wp:comment_content><![CDATA[<?= $comment->content ?>]] > </wp:comment_content>
          <wp:comment_approved>1</wp:comment_approved>
          <wp:comment_type></wp:comment_type>
          <wp:comment_parent>0</wp:comment_parent>
          <wp:comment_user_id>1</wp:comment_user_id>
        </wp:comment>
      <? endforeach ?>
    </item>
  <? endforeach ?>
</channel>
</rss>


<?

				$this->suppressView();
			}
			catch(Exception $ex) {
				$this->handlePageError($ex);
			}
		}
		
		public function get_wiki_comments($name) {
			try {
				$this->app_page_title = 'Download Wiki Comments';
				
				header('Content-type: application/octet-stream');
				header('Content-Disposition: attachment; filename="wiki_comments.xml"');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: pre-check=0, post-check=0, max-age=0');
				header('Accept-Ranges: bytes');
?>
<?= '<?xml version="1.0" encoding="utf-8"?>' ?>
<rss version="2.0"
     xmlns:excerpt="http://wordpress.org/export/1.0/excerpt/"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dsq="http://www.disqus.com/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:wp="http://wordpress.org/export/1.0/"
>
<?
  $list = Wiki_Page::create()->find_all();
  $site_url = site_url('/', true);
?>
<channel>
     <title>Site Title</title>
     <link><?= $site_url ?></link>
     <description>Site Description</description>
     <pubDate>Wed, 08 Dec 2008 19:01:52 +0000</pubDate>
     <generator>http://wordpress.org/?v=3.0.3</generator>
     <language>en</language>
     <wp:wxr_version>1.0</wp:wxr_version>
     <wp:base_site_url><?= $site_url ?></wp:base_site_url>
     <wp:base_blog_url><?= $site_url ?>wiki/</wp:base_blog_url>
     <wp:category><wp:category_nicename>uncategorized</wp:category_nicename><wp:category_parent></wp:category_parent><wp:cat_name><![CDATA[Uncategorized]] > </wp:cat_name></wp:category>
  <? foreach($list as $post): ?>
    <item>
      <title><?= h($post->title) ?></title>
      <link><?= $site_url ?>docs/<?= $post->url_title ?>/</link>
      <pubDate><?= date("D, j M Y G:i:s", strtotime($post->published_date)) ?> +0000</pubDate>
      <dc:creator><![CDATA[<?= Users_User::create()->find($post->created_user_id)->login ?>]] > </dc:creator>
      <category>uncategorized</category>
      <category domain="category" nicename="uncategorized">Uncategorized</category>
      <guid isPermaLink="false"><?= $site_url ?>docs/<?= $post->url_title ?>/</guid>
      <description></description>
      <content:encoded><![CDATA[<?= $post->content ?>]] > </content:encoded>
      <excerpt:encoded><![CDATA[]] > </excerpt:encoded>
      <dsq:thread_identifier>post_<?= $post->id ?></dsq:thread_identifier>
      <wp:post_id><?= $post->id ?></wp:post_id>
      <wp:post_date><?= date("D, j M Y G:i:s", strtotime($post->created_at)) ?> +0000</wp:post_date>
      <wp:post_date_gmt><?= date("D, j M Y G:i:s", strtotime($post->created_at)) ?> +0000</wp:post_date_gmt>
      <wp:comment_status>open</wp:comment_status>
      <wp:ping_status>open</wp:ping_status>
      <wp:post_name><?= $post->url_title ?></wp:post_name>
      <wp:status>publish</wp:status>
      <wp:post_parent>0</wp:post_parent>
      <wp:menu_order>0</wp:menu_order>
      <wp:post_type>post</wp:post_type>
      <? foreach($post->approved_comments as $comment): ?>
        <wp:comment>
          <wp:comment_id><?= $comment->id ?></wp:comment_id>
          <wp:comment_author><![CDATA[<?= $comment->author_name ?>]] > </wp:comment_author>
          <wp:comment_author_email><?= $comment->author_email ?></wp:comment_author_email>
          <wp:comment_author_url><?= $comment->author_url ?></wp:comment_author_url>
          <wp:comment_author_IP><?= $comment->author_ip ?></wp:comment_author_IP>
          <wp:comment_date><?= date('Y-m-d H:i:s', strtotime($comment->created_at)) ?></wp:comment_date>
          <wp:comment_date_gmt><?= date('Y-m-d H:i:s', strtotime($comment->created_at)) ?></wp:comment_date_gmt>
          <wp:comment_content><![CDATA[<?= $comment->content ?>]] > </wp:comment_content>
          <wp:comment_approved>1</wp:comment_approved>
          <wp:comment_type></wp:comment_type>
          <wp:comment_parent>0</wp:comment_parent>
          <wp:comment_user_id>1</wp:comment_user_id>
        </wp:comment>
      <? endforeach ?>
    </item>
  <? endforeach ?>
</channel>
</rss>


<?

				$this->suppressView();
			}
			catch(Exception $ex) {
				$this->handlePageError($ex);
			}
		}

		/*
		 * Import
		 */
		
		public function import() {
			$this->app_page_title = 'Import CMS Objects';
			
			try {
				$this->viewData['complete'] = false;
				
				if(post('postback')) {
					try {
						Phpr_Files::validateUploadedFile($_FILES['file']);
						$fileInfo = $_FILES['file'];
						
						$pathInfo = pathinfo($fileInfo['name']);
						$ext = strtolower($pathInfo['extension']);
						
						if(!isset($pathInfo['extension']) || !($ext == 'lca' || $ext == 'zip'))
							throw new Phpr_ApplicationException('Uploaded file is not LemonStand CMS objects archive.');

						$exportMan = Cms_ExportManager::create();
						$exportMan->import($fileInfo);
						$this->viewData['complete'] = true;
						$this->viewData['exportMan'] = $exportMan;
					}
					catch(Exception $ex) {
						$this->viewData['form_error'] = $ex->getMessage();
					}
				}
			}
			catch(Exception $ex) {
				$this->handlePageError($ex);
			}
		}
	}