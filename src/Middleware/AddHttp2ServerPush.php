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

        $nodes = $crawler->filter('link, script[src]')->extract(['src', 'href']);

        $headers = collect($nodes)->flatten(1)
            ->filter()
            ->map(function ($url) {
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
