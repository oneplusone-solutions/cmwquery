<?php
namespace OnePlusOne\CMWQuery\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

trait SendCMWRequest
{
    protected static function bootSendCMWRequest(array $data): void
    {
        $this->beforeBooted();

        $this->makeRequest($data);
        die('1111111111111111');

        $this->afterBooted();

    }
    protected function beforeBooted(): void
    {
    }

    protected function afterBooted(): void
    {
    }
//    protected static function boot(){
//        parent::boot();
//        self::creating(function($model){
//            $model->user_id = auth()->id();
//        });
//        self::addGlobalScope(function(Builder $builder){
//           $builder->where('user_id', auth()->id());
//        });
//    }

    /**
     * @param array $inputData
     * @return string
     * @throws \Exception
     */
    public function makeRequest(array $data):string
    {
        $client = new Client();
        $files_key = config('cmw-config.fields.files');
        //if request have files - try to upload before create deals
        // after uploaded files work with them flowIdentifier and titles
        if( isset($data[$files_key]) ){
            $data[$files_key] = $this->uploadFiles($data[$files_key]);
        }

        $inputData = $this->prepareData($data);
//        SendCMWQuery::dispatch($inputData);

        $api_url = "https://1pls1.comindwork.com/api/apialpha.ashx/tickets/multi";
        $auth_code = "CMW_AUTH_CODE 2bmN1gdVUFj3a5SmRU7Ly9PxNBdjkpIEvrahP77fByVh3b9qYi"; // add var to .env

        try {
            $response = $client->post($api_url, [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Authorization' => $auth_code
                ],
                'json' => [$inputData]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to send data to third-party API');
            }

            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to send data to third-party API: ' . $e->getMessage());
        }


    }
    /**
     * @param array $files
     * @return array
     */
    public function uploadFiles(array $files):array
    {


        $auth_code = "CMW_AUTH_CODE 2bmN1gdVUFj3a5SmRU7Ly9PxNBdjkpIEvrahP77fByVh3b9qYi"; // add var to .env
        $project_id = '7d5d6487-dc13-4016-a84a-b92ff1c9bdfb';
        $api_url = "https://1pls1.comindwork.com/api/upload.ashx";
        $ids = [];
//        echo '<pre style="display:none;">';
//        var_dump($files);
//        echo '</pre>';

        // prepare data for each file
        foreach ($files as $file){

            $title = $file->getClientOriginalName();
            $id = Str::uuid().'-'.$file->getSize().'-'.str_replace([' ', '.'], '_', $title).'-'.microtime();
            $ids[$id] = $title;
            $mime = $file->getmimeType();

            $request = [
                'flowChunkNumber'=> 1,
                'flowChunkSize'=> 1048576,
                'flowCurrentChunkSize'=> $file->getSize(),
                'flowTotalSize'=> $file->getSize(),
                'flowIdentifier'=> $id,
                'flowFilename'=> $title,
                'flowRelativePath'=> $title,
                'flowTotalChunks'=> 1,
                'file' => file_get_contents($file->getRealPath()),
//                'file' => fopen( $file->getPathname(), 'r' ) ,
            ];

            $multipart = [
                'name'     => $title,
                'filename'     => $title,
                'Mime-Type'=> $mime,
                'contents' => fopen( $file->getPathname(), 'r' ),
            ];
            $multipart_form = [];
            foreach ($request as $key => $value){
                $multipart_form[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
//            $response = $this->client->get($api_url.'?'.http_build_query($request) );
//            var_dump($response->getBody());
//
//            if ($response->getStatusCode() !== 204) {
//                throw new \Exception('Failed to send data to third-party API');
//            }

//            $request['file'] = file_get_contents($file->getRealPath());
//            var_dump($request);
            /*
            $response = $this->client->post($api_url, [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Authorization' => $auth_code
                ],
                'json' => [$request]
            ]);*/
            /*$response = $this->client
                ->attach('file', file_get_contents($file->getRealPath()), $request )
                ->withHeaders(['Content-Type' => $file->getMimeType(),
                    'Authorization' => $auth_code ])
                ->post($api_url, $request)
                ->json()
            ;
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to send data to third-party API');
            }
            var_dump($response->getStatusCode());*/

//            echo '<pre style="display:none;">';
//            var_dump($request);
//            echo '</pre>';

            // try to send request to upload file
            try {
                $boundary = '----WebKitFormBoundary'. $id;
                $response = $this->client->post(
                    $api_url, [
                    'headers' => [
                        'Accept'                => '*/*', // application/json
                        'Content-Type'          => 'multipart/form-data; charset=utf-8; boundary='.$boundary,
                        'Authorization'         => $auth_code,
                    ],
                    //                    'multipart' => [
                    //                        $multipart
                    //                    ],
                    //                    'form-data' => [
                    //                        $request
                    //                    ],
                    'form_params' => $request
//                        'body' => new \GuzzleHttp\Psr7\MultipartStream($multipart_form, $boundary),
                ],
                );
//            echo '<pre style="display:none;">';
//            var_dump($response);
//            echo '</pre>';
//                echo $response->getBody()->getContents();
            } catch(\Exception $e) {
                echo $e->getMessage();

                $response = $e->getResponse();
//                echo '<pre style="display:none;">';
//                var_dump($response);
//                echo '</pre>';
                $responseBody = $response->getBody()->getContents();

                echo $responseBody;
                exit;
            }

        }
//        echo '<pre style="display:none;">';
//        var_dump($ids);
//        echo '</pre>';
        return $ids;


        /*try {

            $response = $this->client->post($api_url, [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Authorization' => $auth_code
                ],
                'json' => [$request]
            ]);
//            $this->client->setDefaultOption(array('verify', false));
//            $response = $this->client
//                ->withHeaders()
//                ->post($api_url, $headers, json_encode($request));

//            $response = $this->client
//                ->post('https://1pls1.comindwork.com/api/upload.ashx', [
//                    'headers' => $headers,
//                    'json' => $request
//                ])
//                ->withHeaders($headers)
//            ;

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to send data to third-party API');
            }

            echo '<pre style="display:none;">';
            var_dump($response);
            echo '</pre>';
            die();

            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to send data to third-party API: ' . $e->getMessage());
        }*/
    }


    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data): array
    {
        $fields = config('cmw-config.fields');
        $domain = config('cmw-config.domain');
        $process_template_id = config('cmw-config.process_template_id');
        $state = config('cmw-config.state');
        $project_id = config('cmw-config.project_id');

        $cmwData =    [
            'title' => 'Message from ' . ($data[$fields['name']] ?? ''),
            'description' => ($data[$fields['message']]) ?? '',
            'c_primaryemail' => $data[$fields['email']] ?? '',
            'c_workphone' => $data[$fields['phone']] ?? '',
            'c_source' => $data[$fields['website']] ?? '',
            'c_domain' => $domain,
            'process_template_id' => $process_template_id,
            'state' => $state,
            'project_id' => $project_id,
        ];

        // attach uploaded files to futures deals
        if( isset($data[$fields['files']]) ) {
            foreach ( $data[$fields['files']] as $id=>$title){

                $cmwData['attachments'][] = [
                    [
                        'file_uid'=> $id,
                        'id'=> $id,
                        'pending'=> true,
                        'title'=> $title,
                    ]
                ];
            }

        }

        return $cmwData;
    }
}