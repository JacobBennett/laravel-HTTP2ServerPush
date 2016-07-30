<?php

namespace JacobBennett\Http2ServerPush\Test;

// TODO: test for invalid file types like .svg

use Illuminate\Http\Request;
use JacobBennett\Http2ServerPush\Middleware\AddHttp2ServerPush;
use Symfony\Component\HttpFoundation\Response;

class AddHttp2ServerPushTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->middleware = new AddHttp2ServerPush();
    }

//    /** @test */
    public function it_will_not_modify_a_response_with_no_server_push_assets()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext("pageWithoutAssets"));

        $this->assertFalse($this->isServerPushResponse($response));
    }

    /** @test */
    public function it_will_return_a_css_link_header_for_css()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext('pageWithCss'));

        $this->assertTrue($this->isServerPushResponse($response));
        $this->assertStringEndsWith("as=style", $response->headers->get('link'));
    }

    /** @test */
    public function it_will_return_a_js_link_header_for_js()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext('pageWithJs'));

        $this->assertTrue($this->isServerPushResponse($response));
        $this->assertStringEndsWith("as=script", $response->headers->get('link'));
    }

    /** @test */
    public function it_returns_well_formatted_link_headers()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext('pageWithCss'));

        $this->assertEquals("<css/test.css>; rel=preload; as=style", $response->headers->get('link'));
    }

    /** @test */
    public function it_will_return_correct_push_headers_for_multiple_assets()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext('pageWithCssAndJs'));

        $this->assertTrue($this->isServerPushResponse($response));
        $this->assertTrue(str_contains($response->headers, 'style'));
        $this->assertTrue(str_contains($response->headers, 'script'));
        $this->assertCount(2, explode(",", $response->headers));
    }

    /** @test */
    public function it_will_not_return_a_push_header_for_inline_js()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext('pageWithJsInline'));

        $this->assertFalse($this->isServerPushResponse($response));
    }

    /** @test */
    public function it_will_not_return_a_push_header_for_external_resources()
    {
        // TODO: check that double slash isn't OUR sites url

        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext('pageWithExternalAssets'));

        $this->assertFalse($this->isServerPushResponse($response));
    }

    /**
     * @param string $pageName
     *
     * @return \Closure
     */
    protected function getNext($pageName)
    {
        $html = $this->getHtml($pageName);

        $response = (new \Illuminate\Http\Response($html));

        return function ($request) use ($response) {

            return $response;
        };
    }

    /**
     * @param string $pageName
     *
     * @return string
     */
    protected function getHtml($pageName)
    {
        return file_get_contents(__DIR__."/fixtures/{$pageName}.html");
    }

    private function isServerPushResponse($response)
    {
        return $response->headers->has('Link');
    }
}
