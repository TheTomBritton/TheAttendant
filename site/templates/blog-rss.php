<?php namespace ProcessWire;

/**
 * Template: blog-rss.php
 * RSS 2.0 feed for blog posts.
 * No fields needed — this is a utility template.
 *
 * Template settings: disable prepend (_init.php) and append (_main.php)
 * Content type: application/rss+xml
 */

header('Content-Type: application/rss+xml; charset=UTF-8');

$posts = $pages->find("template=blog-post, sort=-date, limit=20");
$blogUrl = $pages->get('template=blog-index')->httpUrl;
$siteTitle = $pages->get('/')->title;
$blogDescription = $pages->get('template=blog-index')->get('summary|body|');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title><?= $sanitizer->entities($siteTitle) ?> Blog</title>
    <link><?= $blogUrl ?></link>
    <description><?= $sanitizer->entities($blogDescription) ?></description>
    <language>en-gb</language>
    <atom:link href="<?= $page->httpUrl ?>" rel="self" type="application/rss+xml" />
    <?php foreach ($posts as $post): ?>
    <item>
        <title><?= $sanitizer->entities($post->title) ?></title>
        <link><?= $post->httpUrl ?></link>
        <guid isPermaLink="true"><?= $post->httpUrl ?></guid>
        <pubDate><?= date('r', $post->getUnformatted('date')) ?></pubDate>
        <description><?= $sanitizer->entities($post->get('summary|')) ?></description>
    </item>
    <?php endforeach; ?>
</channel>
</rss>
