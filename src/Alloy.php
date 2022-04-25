<?php

namespace Alloy\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class Alloy
{

    private $key;
    private $client;

    public function __construct(string $key) {
        $this->key = $key;
        $this->client = new Client([
            'base_uri' => ''
        ]);
    }

    public function event(string $workflowId, ?float $data, ?bool $returnExecutionData = false)
    {
        // send the event to quickmetrics.io's api
        $json = $data;
        $uuid = Str::uuid()->toString();
        try {
            $res = $this->client->request('POST', 'https://webhooks.runalloy.com/'.$workflowId, [
                'json' => $json,
                'headers' => [
                    'Authorization' => $this->key,
                    'X-Execution-Uuid' => $uuid
                ]
            ]);


            if($res->getStatusCode() === 200) {
                if($returnExecutionData === false){
                    return $res->getBody();
                } else {
                    $count = 0;
                    while ($count<10) {
                        $pollingData = $this->client->request('GET', 'https://api.runalloy.com/sdk/output/'.$uuid, [
                            'json' => $json,
                            'headers' => [
                                'Authorization' => $this->key,
                                'X-Execution-Uuid' => $uuid
                            ]
                        ]);
                        if($pollingData->getStatusCode() === 200){
                            return $pollingData->getBody();
                        }else if($pollingData->getStatusCode() === 401){
                            return response()->json([
                                'code' => $res->getStatusCode(),
                                'message' => $res->getReasonPhrase()
                            ]);
                        } else{
                            $count = $count + 1;
                        }
                        sleep(1);
                    }
                    return $pollingData;
                }
            } else{
                return response()->json([
                    'code' => $res->getStatusCode(),
                    'message' => $res->getReasonPhrase()
                ]);
            }
            
            // return response()->json([
            //     'data' => $res->getBody(),
            //     'code' => $res->getStatusCode(),
            //     'message' => $res->getReasonPhrase()
            // ]);
        } catch(GuzzleException $e) {
            // handle the exception
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function batch(array $items) {
        try {
            $res = $this->client->request('POST', '/list', [
                'json' => $items,
                'headers' => [
                    'x-qm-key' => $this->key
                ]
            ]);

            return response()->json([
                'code' => $res->getStatusCode(),
                'message' => $res->getReasonPhrase()
            ]);
        } catch(GuzzleException $e) {
            // handle the exception
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

}