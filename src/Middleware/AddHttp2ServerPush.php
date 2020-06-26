<?php

namespace JacobBennett\Http2ServerPush\Middleware;

use Closure;
use Illuminate\Support\Str;
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
    public function handle(Request $request, Closure $next, $limit = null, $sizeLimit = null, $excludeKeywords=null)
    {
        $response = $next($request);

        if ($response->isRedirection() || !$response instanceof Response || $request->isJson()) {
            return $response;
        }

        $this->generateAndAttachLinkHeaders($response, $limit, $sizeLimit, $excludeKeywords);

        return $response;
    }

    public function getConfig($key, $default=false) {
        if(!function_exists('config')) { // for tests..
            return $default;
        }
        return config('http2serverpush.'.$key, $default);
    }

    /**
     * @param \Illuminate\Http\Response $response
     *
     * @return $this
     */
    protected function generateAndAttachLinkHeaders(Response $response, $limit = null, $sizeLimit = null, $excludeKeywords=null)
    {
        $excludeKeywords = $excludeKeywords ?? $this->getConfig('exclude_keywords', []);
        $headers = $this->fetchLinkableNodes($response)
            ->flatten(1)
            ->map(function ($url) {
                return $this->buildLinkHeaderString($url);
            })
            ->unique()
            ->filter(function($value, $key) use ($excludeKeywords){
                if(!$value) return false;
                $exclude_keywords = collect($excludeKeywords)->map(function ($keyword) {
                    return preg_quote($keyword);
                });
                if($exclude_keywords->count() <= 0) {
                    return true;
                }
                return !preg_match('%('.$exclude_keywords->implode('|').')%i', $value);
            })
            ->take($limit);

        $sizeLimit = $sizeLimit ?? max(1, intval($this->getConfig('size_limit', 32*1024)));
        $headersText = trim($headers->implode(','));
        while(strlen($headersText) > $sizeLimit) {
            $headers->pop();
            $headersText = trim($headers->implode(','));
        }

        if (!empty($headersText)) {
            $this->addLinkHeader($response, $headersText);
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
     * Get all nodes we are interested in pushing.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fetchLinkableNodes($response)
    {
        $crawler = $this->getCrawler($response);

        return collect($crawler->filter('link:not([rel*="icon"]), script[src], img[src], object[data]')->extract(['src', 'href', 'data']));
    }

    /**
     * Build out header string based on asset extension.
     *
     * @param string $url
     *
     * @return string
     */
    private function buildLinkHeaderString($url)
    {
        $linkTypeMap = [
            '.CSS'  => 'style',
            '.JS'   => 'script',
            '.BMP'  => 'image',
            '.GIF'  => 'image',
            '.JPG'  => 'image',
            '.JPEG' => 'image',
            '.PNG'  => 'image',
            '.SVG'  => 'image',
            '.TIFF' => 'image',
        ];

        $type = collect($linkTypeMap)->first(function ($type, $extension) use ($url) {
            return Str::contains(strtoupper($url), $extension);
        });

        if ($url && !$type) {
            $type = 'fetch';
        }

        if(!preg_match('%^(https?:)?//%i', $url)) {
            $basePath = $this->getConfig('base_path', '/');
            $url = $basePath . ltrim($url, $basePath);
        }

        return is_null($type) ? null : "<{$url}>; rel=preload; as={$type}";
    }

    /**
     * Add Link Header
     *
     * @param \Illuminate\Http\Response $response
     *
     * @param $link
     */
    private function addLinkHeader(Response $response, $link)
    {
        if ($response->headers->get('Link')) {
            $link = $response->headers->get('Link') . ',' . $link;
        }

        $response->header('Link', $link);
    }
}
