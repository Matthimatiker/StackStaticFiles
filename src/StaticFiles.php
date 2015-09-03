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
        $filePath = $this->getFilePath($request);
        if ($this->canServe($filePath)) {
            return $this->serve($filePath);
        }
        return $this->innerKernel->handle($request, $type, $catch);
    }

    /**
     * Determines the file path that corresponds to the given request.
     *
     * @param Request $request
     * @return string
     */
    protected function getFilePath(Request $request)
    {
        return $this->webDir . '/' . ltrim($request->getPathInfo(), '/');
    }

    /**
     * Checks if the given file can be served.
     *
     * @param string $filePath
     * @return boolean
     */
    protected function canServe($filePath)
    {
        return is_file($filePath);
    }

    /**
     * Creates a response that is used to serve the given file.
     *
     * When this method is called it is guaranteed, that the given file exists
     * and that it may be served.
     *
     * @param string $filePath
     * @return Response
     */
    protected function serve($filePath)
    {
        return new Response(file_get_contents($filePath));
    }
}
