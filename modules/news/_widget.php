<?php
if(App::db()->query("SELECT * FROM `news` ORDER by time DESC LIMIT 1")->rowCount() == 0)
{
	echo Site::div('content', _t('No articles yet', 'news'));
}
else
{
	$article = App::db()->query("SELECT * FROM `news` ORDER by time DESC LIMIT 1")->fetch();
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
		'.(mb_strlen($article['text']) >= 500 ? mb_substr($article['text'], 0, 500).'...' : $article['text']).'<br/>
		# '.$linkedTags.'
		<span class="float-rt"><a href="/news/comments/'. $article['id'] .'">'._t('Comments') .' ('.Site::counter('comments', ['object_name' => 'news', 'object_id' => $article['id']]).')</a></span>
		</div>';
}
