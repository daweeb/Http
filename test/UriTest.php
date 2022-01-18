<?php

namespace Horde\Http\Test;

use AssertionError;
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
use ReflectionMethod;
use InvalidArgumentException;

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
        $this->assertEquals($uri->getPort(), $port);
    }

    public function testWithInvalidPortUpper()
    {
        $port = '65536';
        $uri = new Uri();
        $this->expectException(InvalidArgumentException::class);
        $uri = $uri->withPort($port);
    }

    public function testWithInvalidPortLower()
    {
        $port = '-631';
        $uri = new Uri();
        $this->expectException(InvalidArgumentException::class);
        $uri = $uri->withPort($port);
    }

    public function testWithNullPort()
    {
        $port = null;
        $uri = new Uri();
        $uri = $uri->withPort($port);
        $this->assertNull($uri->getPort());
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

    public function testWithInvalidHost()
    {
        $host = 'group¹ware';
        $uri = new Uri();
        $this->expectException(InvalidArgumentException::class);
        $uri = $uri->withHost($host);
    }

    public function testWithNullHost()
    {
        $host = '';
        $uri = new Uri();
        $uri = $uri->withHost($host);
        $this->assertSame($uri->getHost(), '');
    }

    public function testWithfragmentString()
    {
        $fragment = 'print';
        $uri = new Uri();
        $uri = $uri->withFragment($fragment);
        $this->assertEquals($uri->getFragment(), $fragment);
    }

    public function testWithEmpytFragmentString()
    {
        $fragment = '';
        $uri = new Uri();
        $uri = $uri->withFragment($fragment);
        $this->assertSame($uri->getFragment(), '');
    }

    public function testWithUserInfoValid()
    {
        $uri = new Uri();
        $user = 'test';
        $pass = '1234';
        $uri = $uri->withUserInfo($user, $pass);
        $this->assertEquals($uri->getUserInfo(), 'test:1234');
    }


    public function testWithUserInfoValidEmptyPass()
    {
        $uri = new Uri();
        $user = 'test';
        $uri = $uri->withUserInfo($user);
        $this->assertEquals($uri->getUserInfo(), $user);
    }

    public function testWithSchemeValidLower()
    {
        $uri = new Uri();
        $scheme = 'feed';
        $uri = $uri->withScheme($scheme);
        $this->assertEquals($uri->getScheme(), $scheme);
    }

    public function testWithSchemeValidUpper()
    {
        $uri = new Uri();
        $scheme = 'FeEd';
        $uri = $uri->withScheme($scheme);
        $this->assertEquals($uri->getScheme(), 'feed');
    }

    public function testWithSchemeEmpty()
    {
        $uri = new Uri();
        $scheme = '';
        $uri = $uri->withScheme($scheme);
        $this->assertSame($uri->getScheme(), '');
    }

    public function testWithAuthorityValid()
    {
        $uri = new Uri();
        $host = 'groupware';
        $user = 'test';
        $port = '12345';
        $uri = $uri->withHost($host);
        $uri = $uri->withUserInfo($user);
        $uri = $uri->withPort($port);
        /**
         * The authority syntax of the URI is:
         * [user-info@]host[:port]
         */
        $this->assertEquals($uri->getAuthority(), 'test@groupware:12345');
    }

    public function testWithAuthorityHostEmpty()
    {
        $uri = new Uri();
        $host = '';
        $user = 'test';
        $port = '12345';
        $uri = $uri->withHost($host);
        $uri = $uri->withUserInfo($user);
        $uri = $uri->withPort($port);
        $this->assertSame($uri->getAuthority(), '');
    }
}
