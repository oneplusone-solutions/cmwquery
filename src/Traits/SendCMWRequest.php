<?php

namespace OnePlusOne\CMWQuery\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use Filament\Notifications\Notification;

trait SendCMWRequest
{
    protected static function bootSendCMWRequest(): void
    {
//        static::beforeBooted();
        static::eventsToBeRecorded()->each(function ($eventName) {
//            echo '<pre style="display:none;">';
//            var_dump($eventName);
//            echo '</pre>';

            static::$eventName(function (Model $model) use ($eventName) {
//                echo '<pre style="display:none;">';
//                var_dump($model);
//                echo '</pre>';
//                Mail::send([], [], function ($message) {
//                    $message->to('galina.bublik@oneplusone.solutions')
//                    ->subject('Test Query')
//                    ->setBody('<pre>'.$model.'</pre>', 'text/html'); // for HTML rich messages
//                });
//
//                die('1111111111111111111111111111');

                static::makeRequest($model->toArray());

            });
        });


//        exit('1111111111111111');
//        die();

//        $this->afterBooted();

    }

    protected static function beforeBooted(): void
    {
    }

    protected static function afterBooted(): void
    {
    }
    /**
     * Get the event names that should be recorded.
     **/
    protected static function eventsToBeRecorded(): Collection
    {

        return  collect(config('cmwquery.events'));

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
     * @param  array  $inputData
     *
     * @throws \Exception
     */
    public static function makeRequest(array $data): void
    {
        $client = new Client();
        $files_key = config('cmwquery.fields.files');
        //if request have files - try to upload before create deals
        // after uploaded files work with them flowIdentifier and titles
        if (isset($data[$files_key])) {
            if( is_string( $data[$files_key]) ){
//                echo 'here1111111111111';
                $data[$files_key] = static::uploadFile($data[$files_key]);
            } else {
//                echo 'here 222222222222';
                $data[$files_key] = static::uploadFiles($data[$files_key]);

            }
        }

        $inputData = static::prepareData($data);
//        SendCMWQuery::dispatch($inputData);

        $api_url = config('cmwquery.api_url');
        $auth_code = config('cmwquery.auth_code'); // add var to .env
//        echo '<pre>';
//        var_dump($inputData);
//        echo '</pre>';

        try {
            $response = $client->post($api_url, [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Authorization' => $auth_code,
                ],
                'json' => [$inputData],
            ]);

            if ($response->getStatusCode() !== 200) {
                Notification::make()
                    ->title( 'Failed to send data to third-party API: '.$response->getBody()->getContents() )
                    ->icon('heroicon-o-emoji-sad')
                    ->iconColor('danger')
                    ->send();
//                throw new \Exception('Failed to send data to third-party API');
            } else {
                Notification::make()
                    ->title('Created Lead successfull')
                    ->success()
                    ->send();
            }
//            echo '<pre>';
//            var_dump($response->getBody()->getContents());
//            echo '</pre>';
//            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            Notification::make()
                ->title( 'Failed to send data to third-party API: '.$e->getMessage() )
                ->icon('heroicon-o-emoji-sad')
                ->iconColor('danger')
                ->send();
        }

    }
    public static function uploadFiles(array $files): array
    {
//        $project_id = config('cmwquery.project_id');
        $ids = [];

        // prepare data for each file
        foreach ($files as $file) {
            $ids = merge($ids, static::uploadFile($file));
        }
//        echo '<pre style="display:none;">';
//        var_dump($ids);
//        echo '</pre>';
        return $ids;


    }

    public static function uploadFile(string $file): array
    {
        $ids = [];

        $project_id = config('cmwquery.project_id');
        if( $file instanceof \Illuminate\Http\UploadedFile ){

            $title = $file->getClientOriginalName();
            $size= $file->getSize();
            $id = $project_id.'-'.$size.'-'.str_replace([' ', '.'], '_', $title).'-'. floor(microtime(true) * 1000); //Str::uuid()
            $ids[$id] = $title;
            $mime = $file->getmimeType();
            $path =$file->getPathname();
        } else if(is_string($file)){
            $dir = config('filament.default_filesystem_disk') ?? 'public';
            $storage = Storage::disk($dir);
            $mime = $storage->mimeType($file);

            if ( $storage->exists($file) ) {
                //        $image = convertToWebp($image);

                $title = basename($file);
                $size= $storage->size($file);
//                $id = $project_id.'-'.$size.'-'.str_replace([' ', '.'], '_', $title).'-'. floor(microtime(true) * 1000);
                $id = Str::uuid() . '-' . $size . '-' . str_replace([' ', '.'], '_', $title) . '-' . floor(microtime(true) * 1000);
                $ids[$id] = $title;
                $path = $storage->path($file);



            } else {
                Notification::make()
                    ->title( 'Can\'t find file' )
                    ->icon('heroicon-o-emoji-sad')
                    ->iconColor('danger')
                    ->send();
            }

        } else {
            Notification::make()
                ->title( 'Can\'t find file' )
                ->icon('heroicon-o-emoji-sad')
                ->iconColor('danger')
                ->send();
        }
        $request = [
            'flowChunkNumber' => 1,
            'flowChunkSize' => 1048576,
            'flowCurrentChunkSize' => $size,
            'flowTotalSize' => $size,
            'flowIdentifier' => $id,
            'flowFilename' => $title,
            'flowRelativePath' => $title,
            'flowTotalChunks' => 1,
            //                'file' => file_get_contents($file->getRealPath()),
//            'file' => fopen($path, 'r'),
        ];
        $multipart = [
            'name' => 'file',
            'filename' => $title,
            'Mime-Type' => $mime,
            'contents' => fopen($path, 'r'),
        ];
        $multipart_form = [$multipart];
        foreach ($request as $key => $value) {
            $multipart_form[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }
//        echo '<pre style="display:none;">';
//        var_dump($request);
//        echo '</pre>';
        static::uploadRequest( $multipart_form);
        return $ids;
    }
    public static function uploadRequest(array $request): void
    {
        $client = new Client();
        $api_url = 'https://api.comindwork.com/api/upload';
        $auth_code = config('cmwquery.auth_code');
        $multipart = new \GuzzleHttp\Psr7\MultipartStream($request);

        try {
            $response = $client->post(
                $api_url, [
                'headers' => [
                    'Authorization' => $auth_code,
                ],
                'multipart' => $request,
            ],
            );
            if( $response->getStatusCode() != 200 ){
                Notification::make()
                    ->title( $response->getStatusCode().$response->getBody()->getContents() )
                    ->icon('heroicon-o-emoji-sad')
                    ->iconColor('danger')
                    ->send();
            } else{
                Notification::make()
                    ->title('Upload file successfull')
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title( $e->getMessage() )
                ->icon('heroicon-o-emoji-sad')
                ->iconColor('danger')
                ->send();
            return;
        }
    }




    public static function prepareData(array $data): array
    {
        $fields = config('cmwquery.fields');
        $domain = $data[$fields['domain']] ?? config('cmwquery.domain');
        $process_template_id = config('cmwquery.process_template_id');
        $state = config('cmwquery.state');
        $project_id = config('cmwquery.project_id');

        $cmwData = [
            'title' => $data[$fields['name']] ?? '',
            'description' => $data[$fields['message']] ?? '',
            'c_domain' => $domain,
            'c_primaryemail' => $data[$fields['email']] ?? '',
            'c_workphone' => $data[$fields['phone']] ?? '',
            'c_source' => 'site', //Can be site, email, phone
//
            'c_org' => $data[$fields['company']] ?? '',
            'c_country' => $data[$fields['country']] ?? '',
            'c_position' => $data[$fields['position']] ?? '',
            'c_empl_role' => $data[$fields['role']] ?? '',
            'c_orgsize' => $data[$fields['employer']] ?? '',
            'c_sector' => $data[$fields['sector']] ?? '',
            'c_facebook' => $data[$fields['facebook']] ?? '',
            'c_instagram' => $data[$fields['instagram']] ?? '',
            'c_twitter' => $data[$fields['twitter']] ?? '',
            'c_youtube' => $data[$fields['youtube']] ?? '',
            'c_linkedinprofile' => $data[$fields['linkedin']] ?? '',

            'process_template_id' => $process_template_id,
            'state' => $state,
            'project_id' => $project_id,
        ];

        // attach uploaded files to futures deals
        if (isset($data[$fields['files']])) {
            foreach ($data[$fields['files']] as $id => $title) {

                $cmwData['attachments'][] =
                    [
                        'file_uid' => $id,
                        'id' => $id,
                        'pending' => true,
                        'title' => $title,
                    ];
            }

        }

        return $cmwData;
    }
}
