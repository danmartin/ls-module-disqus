# ls-module-disqus
Provides Disqus comment synchronization for your store.

## Installation
1. Download [Disqus](https://github.com/limewheel/ls-module-disqus/zipball/master)
1. Create a folder named `disqus` in the `modules` directory.
1. Extract all files into the `modules/disqus` directory (`modules/disqus/readme.md` should exist).
1. Setup your code as described in the `Usage` section.
1. Done!

## Usage
Create the user/forum objects and pass them to the thread object. Then call the `get_comment_count` method. For example, on the `Blog Post` page:

```php
$user = new Disqus_User('9VqCQcUlmxSgGdGUDNtUIYaLddRFFwiqCDNjgW49mPvVXpZpO8GypTN5fU8mTTm6');
$forum = new Disqus_Forum('F3KZp68bf3XJs2wUzX4VyaKcV7lXldcpr6q5KPFvCDNKZibOU1QekpFlzILCqo3E');
$thread = new Disqus_Thread($user, $forum, 'blog_post_' . $post->id);

echo $thread->sync_blog_comment($comment)->approved_comment_num;
```