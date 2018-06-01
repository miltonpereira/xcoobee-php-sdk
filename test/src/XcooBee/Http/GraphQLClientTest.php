<?php

namespace Test\XcooBee\Http;


use XcooBee\Test\TestCase;

class GraphQLClient extends TestCase
{

    /**
     * @param $returnedCode
     * @param $returnedData
     * @param $returnedErrors
     * @param $expectedCode
     * 
     * @dataProvider responseProvider
     */
    public function testRequest($returnedCode, $returnedData, $returnedErrors, $expectedCode)
    {
        $guzzleResponseMock = $this->_getMock(\GuzzleHttp\Psr7\Response::class, [
            'getBody' => json_encode([
                'data' => $returnedData,
                'errors' => $returnedErrors,
            ]),
            'getStatusCode' => $returnedCode,
        ]);
        $graphQLClientMock = $this->_getMock(\XcooBee\Http\GraphQLClient::class, [
            '_getAuthToken' => 'token',
            '_getUriFromEndpoint' => null,
            'post' => $guzzleResponseMock,
        ]);

        $response = $graphQLClientMock->request('query');

        $this->assertTrue($expectedCode == $response->code);
        $this->assertTrue($returnedData == $response->data);
        $this->assertTrue($returnedErrors == $response->errors);
    }

    public function responseProvider()
    {
        return [
            [
                200,
                'data',
                [],
                200
            ],
            [
                200,
                'data',
                (object) ['message' => 'invalid data'],
                400
            ],
            [
                404,
                null,
                (object) ['message' => 'not found'],
                404
            ],
            [
                400,
                null,
                (object) ['message' => 'invalid data'],
                400
            ]
        ];
    }
}
