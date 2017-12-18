<?php
/**
 * Copyright 2017 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */
namespace Facebook\Tests\Exception;

use Facebook\Application;
use Facebook\Request;
use Facebook\Response;
use Facebook\Exception\ResponseException;
use Facebook\Exception\AuthenticationException;
use Facebook\Exception\ServerException;
use Facebook\Exception\ThrottleException;
use Facebook\Exception\AuthorizationException;
use Facebook\Exception\ClientException;
use Facebook\Exception\OtherException;
use PHPUnit\Framework\TestCase;

class ResponseExceptionTest extends TestCase
{

    /**
     * @var Request
     */
    protected $request;

    protected function setUp()
    {
        $this->request = new Request(new Application('123', 'foo'));
    }

    public function testAuthenticationExceptions()
    {
        $params = [
            'error' => [
                'code' => 100,
                'message' => 'errmsg',
                'error_subcode' => 0,
                'type' => 'exception'
            ],
        ];

        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(100, $exception->getCode());
        $this->assertEquals(0, $exception->getSubErrorCode());
        $this->assertEquals('exception', $exception->getErrorType());
        $this->assertEquals('errmsg', $exception->getMessage());
        $this->assertEquals(json_encode($params), $exception->getRawResponse());
        $this->assertEquals(401, $exception->getHttpStatusCode());

        $params['error']['code'] = 102;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(102, $exception->getCode());

        $params['error']['code'] = 190;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(190, $exception->getCode());

        $params['error']['type'] = 'OAuthException';
        $params['error']['code'] = 0;
        $params['error']['error_subcode'] = 458;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(458, $exception->getSubErrorCode());

        $params['error']['error_subcode'] = 460;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(460, $exception->getSubErrorCode());

        $params['error']['error_subcode'] = 463;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(463, $exception->getSubErrorCode());

        $params['error']['error_subcode'] = 467;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(467, $exception->getSubErrorCode());

        $params['error']['error_subcode'] = 0;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(0, $exception->getSubErrorCode());
    }

    public function testServerExceptions()
    {
        $params = [
            'error' => [
                'code' => 1,
                'message' => 'errmsg',
                'error_subcode' => 0,
                'type' => 'exception'
            ],
        ];

        $response = new Response($this->request, json_encode($params), 500);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(ServerException::class, $exception->getPrevious());
        $this->assertEquals(1, $exception->getCode());
        $this->assertEquals(0, $exception->getSubErrorCode());
        $this->assertEquals('exception', $exception->getErrorType());
        $this->assertEquals('errmsg', $exception->getMessage());
        $this->assertEquals(json_encode($params), $exception->getRawResponse());
        $this->assertEquals(500, $exception->getHttpStatusCode());

        $params['error']['code'] = 2;
        $response = new Response($this->request, json_encode($params), 500);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(ServerException::class, $exception->getPrevious());
        $this->assertEquals(2, $exception->getCode());
    }

    public function testThrottleExceptions()
    {
        $params = [
            'error' => [
                'code' => 4,
                'message' => 'errmsg',
                'error_subcode' => 0,
                'type' => 'exception'
            ],
        ];
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(ThrottleException::class, $exception->getPrevious());
        $this->assertEquals(4, $exception->getCode());
        $this->assertEquals(0, $exception->getSubErrorCode());
        $this->assertEquals('exception', $exception->getErrorType());
        $this->assertEquals('errmsg', $exception->getMessage());
        $this->assertEquals(json_encode($params), $exception->getRawResponse());
        $this->assertEquals(401, $exception->getHttpStatusCode());

        $params['error']['code'] = 17;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(ThrottleException::class, $exception->getPrevious());
        $this->assertEquals(17, $exception->getCode());

        $params['error']['code'] = 341;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(ThrottleException::class, $exception->getPrevious());
        $this->assertEquals(341, $exception->getCode());
    }

    public function testUserIssueExceptions()
    {
        $params = [
            'error' => [
                'code' => 230,
                'message' => 'errmsg',
                'error_subcode' => 459,
                'type' => 'exception'
            ],
        ];
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(230, $exception->getCode());
        $this->assertEquals(459, $exception->getSubErrorCode());
        $this->assertEquals('exception', $exception->getErrorType());
        $this->assertEquals('errmsg', $exception->getMessage());
        $this->assertEquals(json_encode($params), $exception->getRawResponse());
        $this->assertEquals(401, $exception->getHttpStatusCode());

        $params['error']['error_subcode'] = 464;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthenticationException::class, $exception->getPrevious());
        $this->assertEquals(464, $exception->getSubErrorCode());
    }

    public function testAuthorizationExceptions()
    {
        $params = [
            'error' => [
                'code' => 10,
                'message' => 'errmsg',
                'error_subcode' => 0,
                'type' => 'exception'
            ],
        ];
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthorizationException::class, $exception->getPrevious());
        $this->assertEquals(10, $exception->getCode());
        $this->assertEquals(0, $exception->getSubErrorCode());
        $this->assertEquals('exception', $exception->getErrorType());
        $this->assertEquals('errmsg', $exception->getMessage());
        $this->assertEquals(json_encode($params), $exception->getRawResponse());
        $this->assertEquals(401, $exception->getHttpStatusCode());

        $params['error']['code'] = 200;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthorizationException::class, $exception->getPrevious());
        $this->assertEquals(200, $exception->getCode());

        $params['error']['code'] = 250;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthorizationException::class, $exception->getPrevious());
        $this->assertEquals(250, $exception->getCode());

        $params['error']['code'] = 299;
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(AuthorizationException::class, $exception->getPrevious());
        $this->assertEquals(299, $exception->getCode());
    }

    public function testClientExceptions()
    {
        $params = [
            'error' => [
                'code' => 506,
                'message' => 'errmsg',
                'error_subcode' => 0,
                'type' => 'exception'
            ],
        ];
        $response = new Response($this->request, json_encode($params), 401);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(ClientException::class, $exception->getPrevious());
        $this->assertEquals(506, $exception->getCode());
        $this->assertEquals(0, $exception->getSubErrorCode());
        $this->assertEquals('exception', $exception->getErrorType());
        $this->assertEquals('errmsg', $exception->getMessage());
        $this->assertEquals(json_encode($params), $exception->getRawResponse());
        $this->assertEquals(401, $exception->getHttpStatusCode());
    }

    public function testOtherException()
    {
        $params = [
            'error' => [
                'code' => 42,
                'message' => 'ship love',
                'error_subcode' => 0,
                'type' => 'feature'
            ],
        ];
        $response = new Response($this->request, json_encode($params), 200);
        $exception = ResponseException::create($response);
        $this->assertInstanceOf(OtherException::class, $exception->getPrevious());
        $this->assertEquals(42, $exception->getCode());
        $this->assertEquals(0, $exception->getSubErrorCode());
        $this->assertEquals('feature', $exception->getErrorType());
        $this->assertEquals('ship love', $exception->getMessage());
        $this->assertEquals(json_encode($params), $exception->getRawResponse());
        $this->assertEquals(200, $exception->getHttpStatusCode());
    }
}