<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Entity\Picture;
use App\Entity\PictureDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\Retouch;
use App\Entity\User;

use App\Utils\Tools;

class PictureHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FileHandler
     */
    private $dirHandler;

    /**
     * @var SessionInterface
     */
    protected $sessionManager;

    /**
     * Constructor
     *
     * @param SessionInterface $session
     * @param EntityManagerInterface $em
     * @param FileHandler $dirHandler
     */
    public function __construct(SessionInterface $session, EntityManagerInterface $em, FileHandler $dirHandler)
    {
        $this->em = $em;
        $this->sessionManager = $session;
        $this->dirHandler = $dirHandler;
    }

    /**
     * Return the number of ordered images
     *
     * @param User $user
     * @return int
     */
    public function getOrderedFilesNumber(User $user): int
    {
        $sessionData = ['retouchs' => $this->sessionManager->get('retouchs', []), 'params' => $this->sessionManager->get('params', [])];

        $orderedFileNumber = 0;
        foreach ($this->dirHandler->initTmpFiles($sessionData, $user->getUserDirectory()) as $orderedFile) {
          $orderedFileNumber += count($orderedFile['retouch']);
        }
        return $orderedFileNumber;
    }

    /**
     * Return all  tmp the Uploaded Files by the current locale
     *
     * @param string $userDir The current user directory.
     * @param string $locale The current locale
     * @param null|string $orderFolder
     * @param array|null $sessionData
     *
     * @return array
     */
    public function getFilesByCurrentLocale(string $userDir, ?string $locale = null, ?string $orderFolder = null, ?array $sessionData = array()): array
    {
        if (empty($sessionData)){
            $sessionData = ['retouchs' => $this->sessionManager->get('retouchs', []), 'params' => $this->sessionManager->get('params', [])];
            
            $pictureList = !is_null($orderFolder) ? $this->dirHandler->getDirFiles($sessionData, $userDir, $orderFolder) : $this->dirHandler->initTmpFiles($sessionData, $userDir);
            
            $retouchObjects = $this->getAllRetouchByTheCurrentLocale(Tools::arrayFlatten($this->sessionManager->get('retouchs', [])), $locale);
            $uploadedFiles = [];

            foreach ($pictureList as $i => $file) {
                $uploadedFiles[$i] = $file;
                foreach ($file['retouch'] as $key => $id) {
                	if (isset($retouchObjects[$id]) && !is_null($retouchObjects[$id])){
		                $uploadedFiles[$i]['retouch'][$key] = $retouchObjects[$id];
	                }
                }
            }

            return $uploadedFiles;
        }else{
            return $this->dirHandler->getDirFiles($sessionData, $userDir, $orderFolder);
        }
    }

    /**
     * Return all  tmp the Uploaded Files by the current locale
     *
     * @param Picture[] $pictures
     * @return array
     */
    public function getFormattedPictures($pictures): array
    {
        $uploadedFiles = [];
        foreach ($pictures as $picture) {
            $file = new \SplFileInfo($this->dirHandler->doGetRealPath($picture->getPicturePath()));

            if (!$file->isFile() || !$file->isReadable()){
                continue;
            }

            $uploadedFile['uuid'] = $picture->getId();
            $uploadedFile['name'] =  $picture->getPictureName();
            $uploadedFile['thumbnailUrl'] = $picture->getPicturePathThumb();
            $uploadedFile['size'] = $file->getSize();
            $uploadedFile['retouch'] = $picture->getPictureDetail()->map(function (PictureDetails $pictureDetail) {
                return $pictureDetail->getRetouch();
            })->toArray();
            array_push($uploadedFiles, $uploadedFile);
        }
        return $uploadedFiles;
    }

    /**
     * Return all  tmp the Uploaded Files
     *
     * @param string $uuid The current user directory.
     *
     * @return array
     */
    public function getTmpFiles(string $uuid): array
    {
        $sessionData = ['retouchs' => $this->sessionManager->get('retouchs', []), 'params' => $this->sessionManager->get('params', [])];

        return $this->dirHandler->initTmpFiles($sessionData, $uuid);
    }

    /**
     * Return one tmp the Uploaded File
     *
     * @param string $uuid The current user directory.
     * @param string $dir The current file directory.
     *
     * @return array
     */
    public function getTmpFile(string $uuid, string $dir): ?array
    {
        $sessionData = ['retouchs' => $this->sessionManager->get('retouchs', []), 'params' => $this->sessionManager->get('params', [])];

        return $this->dirHandler->initTmpFile($sessionData, $uuid, $dir);
    }

    /**
     * Get all Retouch by the current local
     *
     * @param array         $arrayOfIds
     * @param string|null   $locale
     *
     * @return array|null
     */
    private function getAllRetouchByTheCurrentLocale(array $arrayOfIds, ?string $locale = null): ?array
    {
        $result = array();
        foreach ($this->em->getRepository(Retouch::class)->findByArrayOfIdsAndLocale($arrayOfIds, $locale) as $retouch) {
            $result[$retouch->getId()] = $retouch;
        }
        return $result;
    }

    /**
     * Change params upload dir from temporary to order dir
     *
     * @param array $params
     * @param string $orderFolder
     *
     * @return array
     */
    public function moveParamsFromTmpFolderToOrderFolder(array $params, ?string $orderFolder): array
    {
        foreach ($params as $key => $param) {
          if (is_string($param) && !is_null($param) && $this->dirHandler->isFile($param)) {
              $params[$key] = str_replace($this->dirHandler->getTmpDir(), $orderFolder, $param);
          }
        }
        return $params;
    }
}
