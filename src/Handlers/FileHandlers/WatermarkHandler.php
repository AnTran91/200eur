<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers\FileHandlers;

use App\Exception\FileSystemException;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use FFMpeg;

/**
 * ThumbHandler Provides basic utility to handle pictures thumbnails.
 */
class WatermarkHandler extends BaseFileHandler
{
    /**
     * Create picture with watermark using GD lib.
     *
     * @param string $fileFullPath
     * @param string $imgDir
     * @param string $fileName
     * @return string
     *
     */
    public function doGenerateWatermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {
        $watermarkedImgPath = $this->generateWatermarked($fileFullPath, $imgDir, $fileName);
        
        if ($this->storage->doExiste($watermarkedImgPath)) {
            return substr($watermarkedImgPath, strpos($watermarkedImgPath, $this->uriPrefix));
        }

        throw new FileSystemException($watermarkedImgPath);
    }

    /**
     * Create picture with watermark using GD lib.
     *
     * @param string $fileFullPath
     * @param string $imgDir
     * @param string $fileName
     * @return string
     *
     */
    public function doGenerateGifWatermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {
        $watermarkedImgPath = $this->generateGifWatermarked($fileFullPath, $imgDir, $fileName);
        
        if ($this->storage->doExiste($watermarkedImgPath)) {
            return substr($watermarkedImgPath, strpos($watermarkedImgPath, $this->uriPrefix));
        }

        throw new FileSystemException($watermarkedImgPath);
    }

    /**
     * Create picture with watermark using FFMpeg lib.
     *
     * @param string $fileFullPath
     * @param string $imgDir
     * @param string $fileName
     * @return string
     *
     */
    public function doGenerateMP4Watermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {
        $watermarkedImgPath = $this->generateMP4Watermarked($fileFullPath, $imgDir, $fileName);
        
        if ($this->storage->doExiste($watermarkedImgPath)) {
            return substr($watermarkedImgPath, strpos($watermarkedImgPath, $this->uriPrefix));
        }

        throw new FileSystemException($watermarkedImgPath);
    }

    /**
     * Create picture with watermark usig GD lib.
     *
     * @param string $fileFullPath
     * @param string $imgDir
     * @param  string $fileName The image name
     * @return string
     *
     */
    private function generateWatermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {
	    $image = new \SplFileInfo($fileFullPath);
	    $watermarkedImgPath = join(DIRECTORY_SEPARATOR, [$imgDir, $this->watermarkDir, sprintf('%s.%s',basename($fileName, '.'.$image->getExtension()), $this->extension)]);

	    try {
		    $this->storage->doCreateDirIfNotExists(join(DIRECTORY_SEPARATOR, [$imgDir, $this->watermarkDir]));
		
		    if ($image->getSize() > $this->maxSize || in_array(mb_strtoupper($image->getExtension()), $this->webExtensions)){
			    throw new \Imagine\Exception\NotSupportedException('Not valid image');
		    }
		    
            $originImg = $this->imagineGD->open($image->getRealPath());
            
            $watermarkImg = $this->imagineGD->open($this->watermarkImgPath);
            $scale = $watermarkImg->getSize()->getWidth()/$watermarkImg->getSize()->getHeight();
            $watermax_box = $this->resizeWatermarkImage($originImg->getSize()->getWidth(), $originImg->getSize()->getHeight(), $scale);
            $watermarkImg = $watermarkImg->resize($watermax_box);
            // $watermarkImg = $this->imagineGD->open($this->watermarkImgPath)->resize($originImg->getSize());


            // set x, y position to overlay watermark image
            $x_pos = ($originImg->getSize()->getWidth() - $watermax_box->getWidth()) / 2;
            $y_pos = ($originImg->getSize()->getHeight() - $watermax_box->getHeight()) / 2;
            $position = new \Imagine\Image\Point($x_pos, $y_pos);

	        $originImg->paste($watermarkImg, $position)->save($watermarkedImgPath);
        } catch (\Imagine\Exception\Exception $e) {
            $this->drawTextOnImg(mb_strtoupper($image->getExtension()), $watermarkedImgPath);
        } catch (\Throwable $e) {
            throw new FileSystemException($e->getMessage());
        } finally {
            return $watermarkedImgPath ?? $image;
        }
    }

    /**
     * Create picture with watermark usig GD lib.
     *
     * @param string $fileFullPath
     * @param string $imgDir
     * @param  string $fileName The image name
     * @return string
     *
     */
    private function generateGifWatermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {
        $image = new \SplFileInfo($fileFullPath);
        $watermarkedImgPath = join(DIRECTORY_SEPARATOR, [$imgDir, $this->watermarkDir, sprintf('%s.%s',basename($fileName, '.'.$image->getExtension()), $image->getExtension())]);

        try {
            $this->storage->doCreateDirIfNotExists(join(DIRECTORY_SEPARATOR, [$imgDir, $this->watermarkDir]));

            if ($image->getSize() > $this->maxSize || in_array(mb_strtoupper($image->getExtension()), $this->webExtensions)) {
                throw new \Imagine\Exception\NotSupportedException('Not valid image');
            }
            
//            $originImg = $this->imagick->open($image->getRealPath());
            // Get gif size command
            $cmd = 'convert "' . $image->getRealPath() . '[0]" -format "%w-%h" info:';
            $output = shell_exec($cmd);
            list($originImgWidth, $originImgHeight) = explode('-', $output);

            $watermarkImg = $this->imagick->open($this->watermarkImgPath);
            $scale = $watermarkImg->getSize()->getWidth() / $watermarkImg->getSize()->getHeight();
//            $watermax_box = $this->resizeWatermarkImage($originImg->getSize()->getWidth(), $originImg->getSize()->getHeight(), $scale);
            $watermax_box = $this->resizeWatermarkImage($originImgWidth, $originImgHeight, $scale);
            $watermarkImg = $watermarkImg->resize($watermax_box);
            $watermarkImgFilename = 'water_mark_tmp_' . md5(time()) . '.png';
            $watermarkImg->save($watermarkImgFilename);

            $cmd = 'convert "' . $image->getRealPath() . '" -coalesce -gravity center -geometry +0+0 null: "' . $watermarkImgFilename . '" -layers composite -layers OptimizePlus "' . $watermarkedImgPath . '"';
            
            shell_exec($cmd);
            unlink($watermarkImgFilename);
//            shell_exec('service php-fpm reload');
//            shell_exec('service nginx reload');


//            // $watermarkImg = $this->imagick->open($this->watermarkImgPath)->resize($originImg->getSize());
//
//            // set center position to overlay watermark image
//            $x_pos = ($originImg->getSize()->getWidth() - $watermax_box->getWidth()) / 2;
//            $y_pos = ($originImg->getSize()->getHeight() - $watermax_box->getHeight()) / 2;
//            $position = new \Imagine\Image\Point($x_pos, $y_pos);
//            // $position = new \Imagine\Image\Point(0, 0);
//
//            $tempWatermarkImage = $this->imagick->create($originImg->getSize());
//            foreach ($originImg->layers() as $frame) {
//                $frame->resize($originImg->getSize());
//                $frame->paste($watermarkImg, $position);
//                $tempWatermarkImage->layers()->add($frame);
//                gc_collect_cycles();
//
//            }
//
//            $tempWatermarkImage->save($watermarkedImgPath, array('animated' => TRUE));

        } catch (\Imagine\Exception\Exception $e) {
            $this->drawTextOnImg(mb_strtoupper($image->getExtension()), $watermarkedImgPath);
        } catch (\Throwable $e) {
            throw new FileSystemException($e->getMessage());
        }
        finally {
            return $watermarkedImgPath ?? $image;
        }

//        $image = new \SplFileInfo($fileFullPath);
//        $watermarkedImgPath = join(DIRECTORY_SEPARATOR, [$imgDir, $this->watermarkDir, sprintf('%s.%s',basename($fileName, '.'.$image->getExtension()), $image->getExtension())]);
//
//        try {
//            $this->storage->doCreateDirIfNotExists(join(DIRECTORY_SEPARATOR, [$imgDir, $this->watermarkDir]));
//
//            if ($image->getSize() > $this->maxSize || in_array(mb_strtoupper($image->getExtension()), $this->webExtensions)){
//                throw new \Imagine\Exception\NotSupportedException('Not valid image');
//            }
//
//            $originImg = $this->imagick->open($image->getRealPath());
//
//            $watermarkImg = $this->imagick->open($this->watermarkImgPath);
//            $scale = $watermarkImg->getSize()->getWidth()/$watermarkImg->getSize()->getHeight();
//            $watermax_box = $this->resizeWatermarkImage($originImg->getSize()->getWidth(), $originImg->getSize()->getHeight(), $scale);
//            $watermarkImg = $watermarkImg->resize($watermax_box);
//            // $watermarkImg = $this->imagick->open($this->watermarkImgPath)->resize($originImg->getSize());
//
//            // set center position to overlay watermark image
//            $x_pos = ($originImg->getSize()->getWidth() - $watermax_box->getWidth()) / 2;
//            $y_pos = ($originImg->getSize()->getHeight() - $watermax_box->getHeight()) / 2;
//            $position = new \Imagine\Image\Point($x_pos, $y_pos);
//            // $position = new \Imagine\Image\Point(0, 0);
//
//            $tempWatermarkImage = $this->imagick->create($originImg->getSize());
//            foreach ($originImg->layers() as $frame) {
//                $frame->resize($originImg->getSize());
//                $frame->paste($watermarkImg, $position);
//                $tempWatermarkImage->layers()->add($frame);
//                gc_collect_cycles();
//
//            }
//
//            $tempWatermarkImage->save($watermarkedImgPath, array('animated' => TRUE));
//
//        } catch (\Imagine\Exception\Exception $e) {
//            $this->drawTextOnImg(mb_strtoupper($image->getExtension()), $watermarkedImgPath);
//        } catch (\Throwable $e) {
//            throw new FileSystemException($e->getMessage());
//        } finally {
//            return $watermarkedImgPath ?? $image;
//        }
    }

    /**
     * Create picture with watermark usig FFMpeg lib.
     *
     * @param string $fileFullPath
     * @param string $imgDir
     * @param  string $fileName The image name
     * @return string
     *
     */
    private function generateMP4Watermarked(string $fileFullPath, string $imgDir, string $fileName): string
    {   
        $origin_video = new \SplFileInfo($fileFullPath);
        $watermarkedVideoPath = join(DIRECTORY_SEPARATOR, [$imgDir, $this->watermarkDir, sprintf('%s.%s',basename($fileName, '.'.$origin_video->getExtension()), $origin_video->getExtension())]);
        try {
            $this->storage->doCreateDirIfNotExists(join(DIRECTORY_SEPARATOR, [$imgDir, $this->watermarkDir]));
        
            if ($origin_video->getSize() > $this->maxSize || in_array(mb_strtoupper($origin_video->getExtension()), $this->webExtensions)){
                throw new \Imagine\Exception\NotSupportedException('Not valid video');
            }

            $ffmpeg = FFMpeg\FFMpeg::create(array(
                // 'ffmpeg.binaries'  => '/opt/local/ffmpeg/bin/ffmpeg',
                // 'ffprobe.binaries' => '/opt/local/ffmpeg/bin/ffprobe',
                'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
                'timeout'          => 3600, // The timeout for the underlying process
                'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
            ));

            $streams = $ffmpeg->getFFProbe()->streams($origin_video->getRealPath());
            $frameWidth = $streams->first()->getDimensions()->getWidth();
            $frameHeight = $streams->first()->getDimensions()->getHeight();

            $watermarkImg = $this->imagick->open($this->watermarkImgPath);
            $scale = $watermarkImg->getSize()->getWidth()/$watermarkImg->getSize()->getHeight();
            $watermarkImage_resize = $this->resizeWatermarkImage($frameWidth, $frameHeight, $scale);
            $watermarkImg->resize($watermarkImage_resize)->save($this->watermarkResizePath);
            $x_pos = ($frameWidth - $watermarkImage_resize->getWidth()) / 2;
            $y_pos = ($frameHeight - $watermarkImage_resize->getHeight()) / 2;

            $video = $ffmpeg->open($origin_video->getRealPath());
            $video
                ->filters()
                ->watermark($this->watermarkResizePath, array(
                    'position' => 'absolute',
                    'x' => $x_pos,
                    'y' => $y_pos,
                ));
            $video
                ->save(new FFMpeg\Format\Video\X264('libmp3lame', 'libx264'), $watermarkedVideoPath);
        } catch (\Imagine\Exception\Exception $e) {
            $this->drawTextOnImg(mb_strtoupper($video->getExtension()), $watermarkedVideoPath);
        } catch (\Throwable $e) {
            throw new FileSystemException($e->getMessage());
        } finally {
            return $watermarkedVideoPath ?? $video;
        }
    }

    private function resizeWatermarkImage($width, $height, $scale) {

        $w_width = 0;
        $w_height = 0;
        $origin_scale = $width/$height;

        if ($width >= $height) {
            if ($origin_scale > 2) {
                $w_height = $height;
                $w_width = $height * $scale;
            }
            else {
                $w_width = $width;
                $w_height = $width / $scale;
		if ($w_height > $height) {
                    $w_height = $height;
		}
            }
        }
        else {
            $w_width = $width;
            $w_height = $width / $scale;
        }
        return new Box($w_width, $w_height);
    }
}
