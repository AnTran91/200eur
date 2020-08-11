<?php

namespace App\Utils\Traits;


trait FileGuesserTrait
{
    /**
     * @var array
     */
    private static $mimeTypes;

    /**
     * Guess the file name from the given URL
     *
     * @param string $url
     * @return string
     */
    public function doGuessFileName(string $url): string
    {
        // Clear the url param and Remove all illegal characters from a url
        $file = new \SplFileInfo(filter_var(preg_replace('/\\?.*/', '', $url), FILTER_SANITIZE_URL));

        if (!empty($file->getExtension())){
            return $file->getFilename();
        }

        $size = @getimagesize($url);
        return sprintf("no_image_title_found.%s", $this->getExtension($size['mime']));
    }

    /**
     * Get the mime type of the extension
     * else return null
     *
     * @param $extension
     * @return null|string
     */
    public function getMimeType($extension): ?string
    {
        $extension = $this->cleanInput($extension);

        if (empty(self::$mimeTypes)){
            self::getBuiltIn();
        }
        return self::$mimeTypes['mimes'][$extension][0] ?? [];
    }

    /**
     * Get the extension of the mime type
     * else return null
     *
     * @param $mime_type
     * @return null|string
     */
    public function getExtension($mime_type): ?string
    {
        $mime_type = $this->cleanInput($mime_type);
        if (empty(self::$mimeTypes)){
            self::getBuiltIn();
        }
        return self::$mimeTypes['extensions'][$mime_type][0] ?? [];
    }

    /**
     * Get the built-in mapping.
     *
     * @return void The built-in mapping.
     */
    protected static function getBuiltIn(): void
    {
        if (self::$mimeTypes === null) {
            self::$mimeTypes = require(__DIR__ . '/mime.types.php');
        }
    }

    /**
     * Normalize the input string using lowercase/trim.
     *
     * @param string $input The string to normalize.
     *
     * @return string The normalized string.
     */
    private function cleanInput($input)
    {
        return strtolower(trim($input));
    }
}