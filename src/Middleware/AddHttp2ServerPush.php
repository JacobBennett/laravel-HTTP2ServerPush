<?php

namespace JacobBennett\Http2ServerPush\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class AddHttp2ServerPush
{

    /**
     * The DomCrawler instance.
     *
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->isRedirection()) {
            return $response;
        }

        // use the crawler to fetch the nodes we want from the response
        $this->fetchLinkableNodes($response);

        return $response;
    }

    /**
     * @param \Illuminate\Http\Response $response
     *
     * @return $this
     */
    protected function fetchLinkableNodes(Response $response)
    {
        $crawler = $this->getCrawler($response);

        // look to see if link can have any type other than css or a font
        // distinguish font vs css by looking at extension, css vs eot or svg etc...

        // include images??

        $nodes = $crawler->filter('link, script[src]')->extract(['src', 'href']);

        // this is giving me a listing of LOCAL urls for any links or scripts
        // now I just need to determine which type, make sure you account for query strings on end
        $headers = collect($nodes)->flatten(1)
            ->filter()
            ->reject(function ($url) {
                return $this->isExternalUrl($url);
            })->map(function ($url) {
                return $this->buildLinkHeaderString($url);
            })->filter()
            ->implode(',');

        if (! empty(trim($headers))) {
            $this->addLinkHeader($response, $headers);
        }

        return $this;
    }

    /**
     * Get the DomCrawler instance.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function getCrawler(Response $response)
    {
        if ($this->crawler) {
            return $this->crawler;
        }

        return $this->crawler = new Crawler($response->getContent());
    }

    /**
     * @param $url
     * @return bool
     */
    private function isExternalUrl($url)
    {
        // is this a local fully qualified url
        return str_contains($url, "//");
    }

    /**
     * @param String $url
     * @return string
     */
    private function buildLinkHeaderString($url)
    {
        $linkTypeMap = [
            'css' => 'style',
            'js'  => 'script',
        ];

        $type = collect($linkTypeMap)->first(function($extension) use($url) {
           return  str_contains($url, $extension);
        });

        return is_null($type) ? null : "<{$url}>; rel=preload; as={$type}";

    }

    /**
     * @param Response $response
     * @param $link
     */
    private function addLinkHeader(Response $response, $link)
    {
        $response->header('Link', $link);
    }

}
