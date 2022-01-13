<?php

namespace Horde\Http\Test;

use Phpunit\Framework\TestCase;
use Horde\Http\RequestFactory;
use Horde\Http\ServerRequest;
use Horde\Http\Stream;
use Horde\Http\Uri;
use Horde\Http\RequestImplementation;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class UriTest extends TestCase
{
    public function setUp(): void
    {
        $this->requestFactory = new RequestFactory();
    }

    public function testWithPathRemovesQueryString()
    {
        $path = '/hello/world';
        $queryStr = '?test=query';
        $uri = new Uri();
        $uri = $uri->withPath($path . $queryStr);
        $this->assertEquals($uri->getPath(), $path);
    }

    public function testWithPathWorksWithEmptyString()
    {
        $path = '';
        $uri = new Uri();
        $uri = $uri->withPath($path);
        $this->assertEquals($uri->getPath(), $path);
    }

    public function testWithPathRemovesHash()
    {
        $path = '/hello/world';
        $hash = '#test';
        $uri = new Uri();
        $uri = $uri->withPath($path . $hash);
        $this->assertEquals($uri->getPath(), $path);
    }

    public function testToString()
    {
        $url = 'http://www.testsite.com/testpath?q=test#hashtest';
        $uri = new Uri($url);
        $this->assertEquals($url, (string) $uri);
    }

    public function testWithValidPort()
    {
        $port = '56564';
        $uri = new Uri();
        $uri = $uri->withPort($port);
        $this->assertEquals($uri->getPort(),$port);
    }

    /** Port outside port range 1-65535 InvalidArgumentException expected.
     * No error is thrown?
     */ 

    public function testWithInvalidPort()
    {
        $port = '65536';
        $uri = new Uri();
        $uri = $uri->withPort($port);
        $this->assertEquals($uri->getPort(), $port);
    }

    public function testWithNullPort()
    {
        $port = '';
        $uri = new Uri();
        $uri = $uri->withPort($port);
        $this->assertEquals($uri->getPort(), null);
    }

    public function testWithValidLowerHost()
    {
        $host = 'groupware';
        $uri = new Uri();
        $uri = $uri->withHost($host);
        $this->assertEquals($uri->getHost(), $host);
    }

    public function testWithValidUpperHost()
    {
        $host = 'GroupWare';
        $uri = new Uri();
        $uri = $uri->withHost($host);
        $this->assertEquals($uri->getHost(), 'groupware');
    }

    //  no exception thrown?
    //  @throws \InvalidArgumentException for invalid hostnames.
    public function testWithInvalidHost()
    {
        $host = 'group@ware';
        $uri = new Uri();
        $uri = $uri->withHost($host);
        $this->assertEquals($uri->getHost(), $host);
    }

    public function testWithNullHost()
    {
        $host = '';
        $uri = new Uri();
        $uri = $uri->withHost($host);
        $this->assertEquals($uri->getHost(), null);
    }

    public function testWithFragment()
    {
        $path = '/hello/world';
        $fragment = '#print';
        $uri = new Uri();
        $uri = $uri->withPath($path . $fragment);
        $this->assertEquals($fragment->getFragment(),$fragment);
    }
}

