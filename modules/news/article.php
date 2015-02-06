<?php

$article_id = App::filter('int', $_GET['id']);
$db = App::db();

# if no article go home
if($db->query("SELECT * FROM `news` WHERE `id` = '". $article_id ."' LIMIT 1")->rowCount() == 0) 
{
	Site::notFound();
}
# fetch article
$article = $db->query("SELECT * FROM `news` WHERE `id` = '". $article_id ."' LIMIT 1")->fetch();
# include header
$title = $article['name'] .' - '._t('News', 'news');
Site::header($title);
echo Site::div('title', Site::breadcrumbs($article['name']));
# show article
echo '<div class="content">
'.($article['picture'] != '' ? '<img class="article_img" src="/files/articles/'.$article['id'].'/'.$article['picture'].'" alt="'.$article['picture'].'" />' : '').'
'.Site::output($article['text']) .'<br/>';

$linkedTags = '';
$tags = explode(',', $article['tags']);
foreach($tags as $tag)
{
	if(end($tags) == $tag)
		$linkedTags .= '<a href="/news/tag/'.str_replace(array("\t", "\s", "\n", " "), '-', trim($tag)).'">'.$tag.'</a>';
	else
		$linkedTags .= '<a href="/news/tag/'.str_replace(array("\t", "\s", "\n", " "), '-', trim($tag)).'">'.$tag.'</a>, ';
}
echo '# '.$linkedTags.'<br/>
 <small>'.user()->nick($article['user_id'], App::date($article['time'])).'</small>
 </div>';
# navigation
echo '<div class="action_list"><a href="/news/comments/'. $article_id .'">'.Site::icon('comments').' '._t('Comments') .' ('.Site::counter('comments', ['object_name' => 'news', 'object_id' => $article_id]).')</a>';
if(user()->level() >= 4)
	echo '<a href="/news/edit_article/'.$article_id.'">'. Site::icon('arrow-left').' '._t('Edit').'</a>';
echo '</div>';
Site::footer();