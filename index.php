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

$matched = false;

$validUrls = [
    'blog' => 'https://blog.pusher.com',
    'tutorial' => 'https://pusher.com/tutorials'
];

// Something was posted
if ($url !== false) {
    $urlInfo = parse_url($url);

    $urlHost = $urlInfo['host'] ?? false;
    $urlPath = $urlInfo['path'] ?? false;

    if ($urlHost !== false) {
        foreach ($validUrls as $type => $validUrl) {
            $validUrlInfo = parse_url($validUrl);
            $validUrlHost = $validUrlInfo['host'] ?? false;
            $validUrlPath = $validUrlInfo['path'] ?? false;

            if ($validUrlHost === false) {
                continue;
            }

            switch ($type) {
                case 'blog':
                    if ($validUrlHost === $urlHost) {
                        $matched = 'blog';
                    }
                    break;
                case 'tutorial':
                    if ($validUrlHost === $urlHost && strpos($urlPath, '/tutorials') !== false) {
                        $matched = 'tutorial';
                    }
                    break;
            }

            if ($matched) {
                break;
            }
        }
    }

    $handlingFormSubmission = true;

    if ($matched === false) {
        $url = false;
        $markdown = false;
        $formError = 'Invalid URL. Must be a Pusher blog or tutorial post.';
    } else {
        $client = new Client;
        $crawler = $client->request('GET', $url);
        $titleSelector = $matched == 'blog' ? 'h1.block-entry-title' : 'h1.css-1i4lwh2';
        $title = $crawler->filter($titleSelector)->first()->text();

        // Remove all nodes except the .block-text, thats where the article is...
        if ($matched === 'blog') {
            $crawler->filter('.block-standard > .block-titles-wrap > div')->each(function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    if ($node->getAttribute('class') !== 'block-text') {
                        $node->parentNode->removeChild($node);
                    }
                }
            });
        }

        $contentSelector = $matched === 'blog' ? '.block-standard > .block-titles-wrap' : '.css-1xjms4m';

        $rawArticleHtml = $crawler->filter($contentSelector)->first()->html();

        $converter = new HtmlConverter(['header_style' => 'atx', 'hard_break' => true, 'strip_tags' => true]);
        $rawMarkdown = $converter->convert($rawArticleHtml);

        $parsedArticle = preg_replace(['/```([A-Z>!#*])/', '/<!--(.*)-->/'], ["```\n\n$1", ''], $rawMarkdown);

        $markdown = "# {$title}\n\n{$parsedArticle}\n\nThis post first appeared on the [Pusher blog]({$url}).";
    }
}

// Load the page to render
require 'page.php';
