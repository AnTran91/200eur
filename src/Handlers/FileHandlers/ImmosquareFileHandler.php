<?php

namespace App\Handlers\FileHandlers;

use App\Utils\Traits\FileGuesserTrait;

class ImmosquareFileHandler extends BaseFileHandler
{
    use FileGuesserTrait;

    /**
     * @param array $body
     * @param string $userDir
     *
     * @return array
     */
    public function createOrderStructure(array $body, string $userDir): array
    {
        $data = array_replace($body, ['images' => []]);
        $orderDir = $this->storage->doGetUniqueTargetPath(join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir]));

        foreach ($body['images'] as $url){
            $filename = $this->doGuessFileName($url['url']);
            $pictureDir = $this->storage->doGetUniqueTargetPath(join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $orderDir]));

            $pictureFilePath = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $orderDir, $pictureDir, $filename]);
            $this->storage->doCopy($url['url'], $pictureFilePath);
            //$this->doGenerateThumbnailFromPath($pictureFilePath);

            foreach ($url['services'] as $service){
                $data['images']['retouchs'][$pictureDir][] = $service['service'];
                $data['images']['params'][$pictureDir][$service['service']->getId()] = $service['settings'];
            }
        }

        return ['orderDir' => $orderDir, 'data' => $data];
    }

    /**
     * Get unique path on the root upload Directory.
     *
     * @param string $userDir
     * @return string
     */
    public function getOrderUniqueDir(string $userDir): string
    {
        return $this->storage->doGetUniqueTargetPath(join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir]));
    }
}