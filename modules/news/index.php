<?php
Site::header(_t('News', 'news'));
echo Site::div('title', _t('News', 'news'));

$db = App::db();
# count news
$articles_r = $db->query("SELECT * FROM `news`")->rowCount();
# start paginating
$pages = new Pager($articles_r, Site::perPage());
# if not news show alert
if($articles_r == 0)
{
	echo Site::div('content', _t('No articles yet', 'news'));
} 
else # or show news
{
	# create query
	$articles = $db->query("SELECT * FROM `news` ORDER by time DESC LIMIT ".$pages->start().", ".Site::perPage()."");
	# show news
	foreach($articles as $article)
	{
		$linkedTags = '';
		$tags = explode(',', $article['tags']);
		foreach($tags as $tag)
		{
			if(end($tags) == $tag)
				$linkedTags .= '<a href="/news/tag/'.str_replace(array("\t", "\s", "\n", " "), '-', trim($tag)).'">'.$tag.'</a>';
			else
				$linkedTags .= '<a href="/news/tag/'.str_replace(array("\t", "\s", "\n", " "), '-', trim($tag)).'">'.$tag.'</a>, ';
		}

		echo '<div class="content">
		<a href="/news/article/'.$article['id'].'"><b>'.$article['name'].'</b></a><br/>
		'.($article['picture'] != '' ? '<img class="article_img_small" src="/files/articles/'.$article['id'].'/'.$article['picture'].'" alt="'.$article['picture'].'" />' : '').'
		'.(mb_strlen($article['text']) >= 300 ? mb_substr($article['text'], 0, 300).'...' : $article['text']).'<br/>
		# '.$linkedTags.'
		<span class="float-rt"><a href="/news/comments/'. $article['id'] .'">'._t('Comments') .' ('.Site::counter('comments', ['object_name' => 'news', 'object_id' => $article['id']]).')</a></span>
		</div>';
	}
	$pages->view();
}
if(user()->level() >= 4)
	echo Site::div('action_list', '<a href="/news/write_article">'. Site::icon('arrow-left').' '._t('Write article', 'news').'</a>');
Site::footer();