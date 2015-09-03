<?php

namespace Matthimatiker\Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Middleware that serves files from a provided directory.
 *
 * Delegates to a decorated kernel, if a requested file does not exist.
 */
class StaticFiles implements HttpKernelInterface
{
    /**
     * The decorated kernel.
     *
     * @var HttpKernelInterface
     */
    protected $innerKernel = null;

    /**
     * Path to the directory that is searched for files.
     *
     * @var string
     */
    protected $webDir = null;

    /**
     * @param HttpKernelInterface $innerKernel
     * @param string $webDir
     */
    public function __construct(HttpKernelInterface $innerKernel, $webDir)
    {
        $this->innerKernel = $innerKernel;
        $this->webDir      = $webDir;
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @param int $type The type of the request
     *                         (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool $catch Whether to catch exceptions or not
     * @return Response A Response instance
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $filePath = $this->webDir . '/' . ltrim($request->getPathInfo(), '/');
        if (is_file($filePath)) {
            return new Response(file_get_contents($filePath));
        }
        return $this->innerKernel->handle($request, $type, $catch);
    }
}
