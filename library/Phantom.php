<?php namespace Monogram;

use JonnyW\PhantomJs\Client;

class Phantom
{
	private $client = null;
	private $phantomExecutablePath = '/usr/local/bin/phantomjs';
	private $url = '';
	private $request = null;
	private $response = null;
	private $status = 0;

	public function __construct ($url = '')
	{
		$this->client = Client::getInstance();
		$this->client->getEngine()
					 ->setPath($this->phantomExecutablePath);
		if ( !empty( $url ) ) {
			$this->setUrl($url);
		}
	}

	public function setUrl ($url)
	{
		$this->url = $url;
	}

	private function getUrl ()
	{
		return $this->url;
	}

	public function request ()
	{
		$url = $this->getUrl();
		if ( empty( $url ) ) {
			throw new \Exception("Phantom Exception: URL is not set.");
		}
		$this->request = $this->client->getMessageFactory()
									  ->createRequest($this->getUrl(), "GET");

		return $this;
	}

	public function getResponse ()
	{
		$this->response = $this->client->getMessageFactory()
									   ->createResponse();
		$this->sendHttpRequest();
		return $this->response->getContent();
	}

	private function sendHttpRequest ()
	{
		$this->client->send($this->request, $this->response);
		$this->setStatus($this->response->getStatus());
	}

	private function setStatus ($status)
	{
		$this->status = $status;
	}

	public function getStatus ()
	{
		return $this->status;
	}

}