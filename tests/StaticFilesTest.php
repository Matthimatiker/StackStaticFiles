<?php

namespace Matthimatiker\Stack;

use Stack\Builder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class StaticFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var StaticFiles
     */
    protected $middleware = null;

    /**
     * @var HttpKernelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $innerKernel = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->innerKernel = $this->createInnerKernel();
        $this->middleware  = new StaticFiles($this->innerKernel, __DIR__ . '/_files');
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->middleware  = null;
        $this->innerKernel = null;
        parent::tearDown();
    }

    public function testMiddlewareServesFileContentIfFileExists()
    {
        $request = $this->createRequest('test.txt');

        $response = $this->middleware->handle($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertStringEqualsFile(__DIR__ . '/_files/test.txt', $response->getContent());
    }

    public function testMiddlewareDoesNotDelegateToInnerKernelIfFileExists()
    {
        $this->innerKernel->expects($this->never())
            ->method('handle');

        $request = $this->createRequest('test.txt');
        $this->middleware->handle($request);
    }

    public function testMiddlewareDelegatesToInnerKernelIfFileDoesNotExist()
    {
        $innerResponse = new Response('Inner kernel response');
        $this->innerKernel->expects($this->once())
            ->method('handle')
            ->willReturn($innerResponse);

        $request = $this->createRequest('missing.txt');
        $response = $this->middleware->handle($request);

        $this->assertSame($innerResponse, $response);
    }

    /**
     * Checks if the stack builder can be used to create the middleware.
     *
     * @see https://github.com/stackphp/builder
     */
    public function testMiddlewareCanBeCreatedByBuilder()
    {
        $stack = (new Builder())
            ->push(StaticFiles::class, __DIR__ . '/_files');

        $app = $stack->resolve($this->innerKernel);

        $this->assertInstanceOf(HttpKernelInterface::class, $app);
    }

    /**
     * Creates a request that requests the given URL path.
     *
     * @param string $path
     * @return Request
     */
    protected function createRequest($path)
    {
        return Request::create('http://localhost/' . ltrim($path, '/'));
    }

    /**
     * Creates a mocked inner kernel for testing.
     *
     * @return HttpKernelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createInnerKernel()
    {
        return $this->getMock(HttpKernelInterface::class);
    }
}
