<?php
/**
 * Assignment 7.4
 */

require 'vendor/autoload.php';
require 'app/crawler.php';

/**
 * Landing page/start page.
 */
Flight::route('/', function () {
    Flight::render('layout', [
        'result' => '',
        'scheme' => 'http://',
        'url' => '',
        'searchPhrase' => '',
        'depth' => 2,
    ]);
});

/**
 * The crawler page.
 * Crawl and display results
 */
Flight::route('/crawl', function () {
    $scheme = $_GET['scheme'] ?? 'http://';
    $url = $_GET['crawl-url'] ?? '';
    $searchPhrase = $_GET['search-phrase'] ?? '';
    $depth = $_GET['depth'] ?? 1;

    $start_url = $scheme . $url;

    $error_links = [];
    $crawled_urls = [];
    $search_results = [];
    $page_nr = 0;

    // Collect errors
    $curl_error_callback = function ($url, $curl) use (&$error_links) {
        $error_links[] = [
            'url' => $url,
            'description' => 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl)
        ];
    };

    // Page processor. Preform search for string on each crawled page and collect the results
    $page_processor = function ($url, $html_document) use (&$crawled_urls, &$page_nr, $searchPhrase, &$search_results) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html_document);

        $title = $dom->getElementsByTagName('title')[0]->nodeValue ?? '<no title>';

        $page_nr += 1;

        if (strstr($html_document, $searchPhrase)) {
            $search_results[] = [
                'page_nr' => $page_nr,
                'url' => $url,
                'title' => $title,
            ];
        }
        $crawled_urls[] = [
            'page_nr' => $page_nr,
            'url' => $url,
            'title' => $title,
        ];
    };

    // Setup crawler with callbacks
    $crawler = init_crawler($page_processor, $curl_error_callback);

    // Start to crawl
    $crawler($start_url, $depth);

    // Render result to 'result'
    Flight::render('crawlresult',
        compact('search_results', 'crawled_urls', 'error_links', 'scheme', 'url', 'searchPhrase', 'depth'),
        'result' // render target, for layout
    );
    // Render layout with 'result'
    Flight::render('layout');
});

Flight::start();
