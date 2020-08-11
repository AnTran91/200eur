<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\FileHandlers;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Exception\FileSystemException;

class ParamHandler  extends BaseFileHandler
{
    /**
     * Upload picture params exp: Logo, watermaker, docs in the temp directory
     *
     * @param   UploadedFile    $file
     * @param   null|string     $filename
     * @param   string          $userDir
     * @param   string          $pitureDir
     *
     * @param null|string $orderDir
     * @return string
     */
    public function uploadParamFile(UploadedFile $file, ?string $filename, string $userDir, string $pitureDir, ?string $orderDir = null): string
    {
        $target = join(DIRECTORY_SEPARATOR, [$this->uploadDirectory, $userDir, $this->tempDir, $pitureDir,  $this->paramDir]);

        if (!is_null($orderDir)) {
            $target = join(DIRECTORY_SEPARATOR, array($this->uploadDirectory, $userDir, $orderDir, $pitureDir,  $this->paramDir));
        }

	    $filename =  $filename ?? $file->getClientOriginalName();

        if (!is_null($file->guessClientExtension())) {
            $fileName = sprintf("%s.%s", $filename, $file->getClientOriginalExtension());
        }

        if ($this->storage->doUpload($file, $target, $fileName, true)) {
            return $this->storage->doGetUriPath(join(DIRECTORY_SEPARATOR, array($target, $fileName)), $this->uriPrefix);
        }

        throw new FileSystemException($this->translator->trans("uploader.upload.msg.error"));
    }
}
