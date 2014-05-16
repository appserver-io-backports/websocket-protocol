<?php

use TechDivision\WebSocketProtocol;

use Guzzle\Http\Message\RequestInterface;

interface Request
{

	public function injectRequest(RequestInterface $request);

	public function getRequest();

	public function getHandlerPath();

	public function getPath();
}