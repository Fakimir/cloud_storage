<?php
namespace Controllers\Classes;
use Core\Interfaces\AppInterface;

class ShareController
{
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }
    public function fileGetShare(array $args): array
    {
        try {
            $file_id = $args['id'];

            $share = $this->app->getRepository('ShareRepository')->fileGetShare($file_id);
            return [
                'data' => json_encode($share),
                'headers' => [
                    'Content-Type: application/json',
                ]
            ];
        }
        catch (\Exception $e) {
            return [
                'data' => $e,
                'headers' => [
                    'Content-Type: application/json',
                ]
            ];
        }
    }

    public function filePutShare(array $args): array
    {
        try {
            $user_id = $args['access_user_id'];
            $file_id = $args['file_id'];

            $this->app->getRepository('ShareRepository')->filePutShare($user_id, $file_id);
            return [
                'data' => 'You successfully shared your file',
                'headers' => [
                    'Content-Type: application/json',
                ]
            ];
        }
        catch (\Exception $e) {
            return [
                'data' => $e,
                'headers' => [
                    'Content-Type: application/json',
                ]
            ];
        }
    }

    public function fileDeleteShare(array $args): array
    {
        try {
            $user_id = $args['access_user_id'];
            $file_id = $args['file_id'];

            $this->app->getRepository('ShareRepository')->fileDeleteShare($user_id, $file_id);
            return [
                'data' => 'You successfully unshared your file',
                'headers' => [
                    'Content-Type: application/json',
                ]
            ];
        }
        catch (\Exception $e) {
            return [
                'data' => $e,
                'headers' => [
                    'Content-Type: application/json',
                ]
            ];
        }
    }
}