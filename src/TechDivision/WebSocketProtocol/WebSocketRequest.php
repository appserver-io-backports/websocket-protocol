<?php

namespace TechDivision\WebSocketProtocol;

use Guzzle\Http\Message\RequestInterface;
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;

class WebSocketRequest implements Request
{

	protected $request;

	protected $context;

	protected $contextPath;

	protected $handlerPath;

	public function injectRequest(RequestInterface $request)
	{
		$this->request = $request;
	}

	public function injectContext(ApplicationInterface $context)
	{
		$this->context = $context;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getContext()
	{
		return $this->context;
	}

	public function setHandlerPath($handlerPath)
	{
		$this->handlerPath = $handlerPath;
	}

	public function getHandlerPath()
	{
		return $this->handlerPath;
	}

	public function setContextPath($contextPath)
	{
		$this->contextPath = $contextPath;
	}

	public function getContextPath()
	{
		return $this->contextPath;
	}

	public function getHost()
	{
		return $this->getRequest()->getHost();
	}

	public function getPath()
	{
		return $this->getRequest()->getPath();
	}
}