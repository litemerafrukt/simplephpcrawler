<?php

/**
 * Assignment 7.4
 */

/**
 * Make a get request for a url
 * The error callback gets called and after that the curl handle closes and return false.
 *
 * @param string   $url
 * @param function $error_callback - a error callback, gets url and curl_handle as param.
 *
 * @return mixed - the result string or false.
 */
function curl_get($url, $error_callback)
{
    /* echo "Curl: $url<br>"; */
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 7);
    curl_setopt($curl, CURLOPT_LOW_SPEED_TIME, 7);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    $curl_result = curl_exec($curl);

    //check for error and get it if error
    if ($curl_result === false) {
        $error_callback($url, $curl);
        curl_close($curl);
        return false;
    }

    curl_close($curl);
    return $curl_result;
}

/**
 * Return an array of associative arrays of all links in the document.
 * Subarrays has ['href'] and ['description']
 *
 * OBS! - not used in assignment 7.4
 *
 * @param string $html
 * @param string $url - root url to add if no root is present
 *
 * @return array
 */
function link_dump($html, $url = '')
{
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($html);

    $link_dump = [];

    foreach ($doc->getElementsByTagName('a') as $link) {
        $href = $link->getAttribute('href');
        if ($href == '#') {
            continue;
        }
        $href = add_root($href, $url);
        $description = trim($link->nodeValue);
        $description = $description == '' ? '?' : $description;

        $link_dump[] = compact('href', 'description');
    }

    return $link_dump;
}

/**
 * Check if url is pressent in href. If not
 * then add it.
 *
 * @param string $href
 * @param  string $url
 *
 * @return array
 */
function add_root($href, $url)
{
    if (!strstr($href, $url)) {
        $href = $url . $href;
    }
    return $href;
}

/**
 * Function where we try to make more of the links to work.
 *
 * Links come in all shapes and sizes. Especially internal links. Sometimes they are just query strings to
 * a php-script, sometimes they are internal links with a path from host root and
 * sometimes they are a relative path. This function tries to clean things up.
 *
 * @param string $href - the href from an a-tag to clean up.
 * @param array $parsed_url - the root url to the page parsed with parse_url
 *
 * @return string - url from $href
 */
function form_url($href, $parsed_url)
{
    if (strpos($href, 'http') === 0) {
        return $href;
    }

    // If href is relative or query use path
    if(strpos($href, '/') === 0) {
        $path = '/';
    } else {
        $path = isset($parsed_url['path']) ? $parsed_url['path'] . '/' : '/';
    }
    $path .= ltrim($href, '/');
    $href = $parsed_url['scheme'] . '://';
    if (isset($parsed_url['user']) && isset($parsed_url['pass'])) {
        $href .= $parsed_url['user'] . ':' . $parsed_url['pass'] . '@';
    }
    $href .= $parsed_url['host'];
    if (isset($parsed_url['port'])) {
        $href .= ':' . $parsed_url['port'];
    }
    $href .= $path;

    return $href;
}

/**
 * Crawl pages.
 *
 * @param calleble $page_processor - a callback that gets the url and html document to process
 * @param calleble $curl_error_callback - a callback that is passed to curl_get that collect any curl errors
 *
 * @return callable - a function that recursively crawls pages from $url to $depth. OBS - often timesout with dept > 2 due to normal php time limit.
 */
function init_crawler($page_processor, $curl_error_callback)
{
    libxml_use_internal_errors(true);
    $processed = [];

    $crawl = function ($url, $depth) use (&$crawl, $processed, $page_processor, $curl_error_callback) {
        // check if this branch is done
        if (isset($processed[$url]) || $depth <= 0) {
            return;
        }
        // Set this url to processed
        $processed[$url] = true;

        // Get the document or return
        $html_document = curl_get($url, $curl_error_callback);
        if (!$html_document) {
            return;
        }

        // Let the callback suplied in init do something with the page
        $page_processor($url, $html_document);

        // Return if we are on final depth
        if ($depth - 1 <= 0) {
            return;
        }
        // Use DOMDocument for parsing
        $dom = new DOMDocument();
        $dom->loadHTML($html_document);

        // Get fragments from url to maybe get some more hrefs to work
        $url_parts = parse_url($url);

        // Get urls from page and crawl them all
        foreach ($dom->getElementsByTagName('a') as $anchor) {
            $href = $anchor->getAttribute('href');
            if (strpos($href, '#') === 0) {
                continue;
            }
            // Do some simple processing to get more links working
            $next_target_url = form_url($href, $url_parts);

            /* echo $next_target_url . '<br/>'; */
            $crawl($next_target_url, $depth - 1);
        }
    };

    // init_crawler() returns the crawler function
    return $crawl;
}
