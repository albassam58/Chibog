<?php

if (!function_exists('recursiveDelete'))
{
	/**
     * Delete a file or recursively delete a directory
     *
     * @param string $str Path to file or directory
     */
	function recursiveDelete($str)
	{
        if (is_file($str)) {
            return @unlink($str);
        } else if (is_dir($str)) {
            $scan = glob(rtrim($str,'/') . '/*');
            foreach($scan as $index=>$path) {
                recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }
}

if (!function_exists('resizeAndSave'))
{
    /**
     * Resize an image
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string                    $directory
     * @param  string                    $filename
     * @param  integer                   $width
     * @param  integer                   $height
     */
    function resizeAndSave($image, $directory, $filename, $width = 150, $height = 150)
    {
        if (!file_exists($directory))
        {
            mkdir($directory, 666, true);
        }

        $img = \Image::make($image->path());

        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        })->save($directory . "/" . $filename);
    }
}