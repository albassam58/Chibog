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

if (!function_exists('generateNumericOTP'))
{
    /**
     * Generate numeric One-time Password (OTP)
     *
     * @param  integer  $length
     * @return integer
     */
    function generateNumericOTP($length = 6) { 
          
        // Take a generator string which consist of 
        // all numeric digits 
        $generator = "1357902468"; 
      
        // Iterate for n-times and pick a single character 
        // from generator and append it to $result 
          
        // Login for generating a random character from generator 
        //     ---generate a random number 
        //     ---take modulus of same with length of generator (say i) 
        //     ---append the character at place (i) from generator to result 
      
        $result = ""; 
      
        for ($i = 1; $i <= $length; $i++) { 
            $result .= substr($generator, (rand()%(strlen($generator))), 1); 
        }
      
        // Return result
        return $result;
    }
}

if (!function_exists('sendSMS'))
{
    /**
     * Send an SMS using itexmo
     *
     * @param  array  $params
     * @return integer
     */
    function sendSMS($params)
    {
        $ch = curl_init();
        $method = CURLOPT_POST;
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        curl_setopt($ch, CURLOPT_URL, 'https://www.itexmo.com/php_api/api.php');
        curl_setopt($ch, $method, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;

        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://www.itexmo.com/php_api/api.php',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => http_build_query($params),
        //     CURLOPT_HTTPHEADER => array(
        //         'Content-Type: application/json'
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);

        // return $response;
    }
}