<?php namespace Monogram;

use App\Http\Requests\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\MessageBag;
use Mockery\CountValidator\Exception;
use GuzzleHttp\Psr7\Request as GRequest;

class ApiClient
{
	private $httpClient;
	private $orderIds = [ ], $store, $neededApi;
	private $options = [ ];
	private $store_contact_token = [
		// store online id
		'yhst-132060549835833' => '1.0_j5CYYOls_wGIuB5b9mbRpyCRCzKOnau6a8Ec4YthB7wf77afyyOxYa7irnQFLy0Hgy6a9yNPqWwGVpz4egruypuO4nvyyPprCtHde1vXdl.7YKaGwOBrmUuKpwrS3pqXiEU.5o3InQ--',
		// monogram online id
		'yhst-128796189915726' => '1.0_.YQdYOls_wEHLw9D_0A7OcXXf38FJPBVSap2q6duSebDcQqYVeWVI.L2Yf1Kg8ofzdzP_dYs520oj_bvO1PQtsLmTEhwwHw2zVX5hWZD1jChETkYdQeAHFcd08dEgVBRnDw2aPcONQ--',
	];
	private $user_agents = [
		'Mozilla/4.0 (compatible; MSIE 7.0; AOL 8.0; Windows NT 5.1; GTB5; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
		'Mozilla/5.0 (compatible; U; ABrowse 0.6; Syllable) AppleWebKit/420+ (KHTML, like Gecko)',
		'Mozilla/5.0 (compatible; MSIE 9.0; AOL 9.1; AOLBuild 4334.5012; Windows NT 6.0; WOW64; Trident/5.0)',
		'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
		'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.5) Gecko/20031016 K-Meleon/0.8.2',
		'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
		'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
		'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.10 Safari/537.36',
	];

	private function getOrderIds ()
	{
		return $this->orderIds;
	}

	private function setOrderIds ($orderIds)
	{
		if ( !empty( $orderIds ) ) {
			if ( is_array($orderIds) ) {
				$this->orderIds = array_merge($this->orderIds, $orderIds);
			} else {
				$this->orderIds = [ $orderIds ];
			}

			return;
		}

		throw new \Exception("Order id cannot be null");
	}

	private function getNeededApi ()
	{
		return $this->neededApi;
	}

	private function setNeededApi ($neededApi)
	{
		if ( !empty( $neededApi ) ) {
			$this->neededApi = $neededApi;

			return;
		}

		throw new \Exception("API to use cannot be null");
	}

	private function getStore ()
	{
		return $this->store;
	}

	private function setStore ($store)
	{
		if ( !empty( $this->store = $store ) ) {
			$this->store = $store;

			return;
		}

		throw new \Exception("Store id cannot be null");
	}

	public function __construct ($order_ids, $store, $needed_api)
	{
		$this->setStore($store);
		$this->setNeededApi($needed_api);
		$this->setOrderIds($order_ids);
		$this->httpClient = new Client();
	}

	public function fetch_data ()
	{
		if ( $this->getNeededApi() == 'yahoo' ) {
			return $this->fetch_data_from_yahoo();
		}
	}

	private function setOptions ($options)
	{
		foreach ( $options as $key => $value ) {
			$this->options[$key] = $value;
		}
	}

	private function build_options ()
	{
		$headers = array();
		$headers['User-Agent'] = $this->randomizeAgent();
		$this->setOptions([
			'headers'         => $headers,
			'allow_redirects' => true,
		]);
	}

	private function randomizeAgent ()
	{
		$random = rand(0, count($this->user_agents) - 1);

		return $this->user_agents[$random];
	}

	private function fetch_data_from_yahoo ()
	{
		$placeHolderData = "<?xml version='1.0' encoding='utf-8'?><ystorewsRequest><StoreID>{$this->getStore()}</StoreID><SecurityHeader><PartnerStoreContractToken>{$this->store_contact_token[$this->getStore()]}</PartnerStoreContractToken></SecurityHeader><Version>1.0</Version><Verb>get</Verb><ResourceList><OrderListQuery><Filter><Include>all</Include></Filter><QueryParams><OrderID>PLACEHOLDERORDERID</OrderID></QueryParams></OrderListQuery></ResourceList></ystorewsRequest>";
		$url = "https://{$this->getStore()}.order.store.yahooapis.com/V1/order";
		$errors = [ ];
		$responses = [ ];
		foreach ( $this->getOrderIds() as $orderId ) {
			$data = str_replace("PLACEHOLDERORDERID", $orderId, $placeHolderData);
			$body = [
				'body' => $data,
			];
			$response = null;
			try {
				$response = $this->httpClient->request('POST', $url, $body);
				if ( $response->getStatusCode() === 200 ) {
					$responses[] = [
						$orderId,
						$response->getBody()
								 ->getContents(),
					];
				} else {
					$errors[] = "Error for order id: $orderId";
				}
			} catch ( RequestException $requestException ) {
				$errors[] = "Error for order id: $orderId";
			} catch ( Exception $exception ) {
				$errors[] = "Error for order id: $orderId";
			}
		}

		return [
			$responses,
			new MessageBag($errors),
		];
	}
}