<?php

use Goutte\Client;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\DomCrawler\Crawler;

// Load composer
require 'vendor/autoload.php';

// Variables...
$formError = false;
$markdown = false;
$handlingFormSubmission = false;
$url = $_POST['pusher_url'] ?? false;

// Something was posted
if ($url !== false) {
    $handlingFormSubmission = true;

    if (strpos($url, 'https://blog.pusher.com/') === false) {
        $url = false;
        $markdown = false;
        $formError = 'Invalid URL. Must be a Pusher blog post.';
    } else {
        $client = new Client;
        $crawler = $client->request('GET', $url);
        $title = $crawler->filter('h1.block-entry-title')->first()->text();
        $permalink = $crawler->selectLink($title)->link()->getUri();

        // Remove all nodes except the .block-text, thats where the article is...
        $crawler->filter('.block-standard > .block-titles-wrap > div')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                if ($node->getAttribute('class') !== 'block-text') {
                    $node->parentNode->removeChild($node);
                }
            }
        });

        $rawArticleHtml = $crawler->filter('.block-standard > .block-titles-wrap')->first()->html();

        $converter = new HtmlConverter(['header_style' => 'atx', 'hard_break' => true, 'strip_tags' => true]);
        $rawMarkdown = $converter->convert($rawArticleHtml);

        $parsedArticle = preg_replace(['/```([A-Z>!])/', '/<!--(.*)-->/'], ["```\n\n$1", ''], $rawMarkdown);

        $markdown = "# {$title}\n\n{$parsedArticle}\n\nThis post first appeared on the [Pusher blog]({$permalink}).";
    }
}

// Load the page to render
require 'page.php';
