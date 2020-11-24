<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\v1\BaseController;

class SendMailController extends BaseController
{
    public function sendMail($data)
    {
    	try {
	    	$to = $data['to'];
	    	$cc = $data['cc'];
	    	$bcc = $data['bcc'];
	    	$job = $data['job'];
	    	$details = $data['details'];

	    	// send mail in the queue.
	        $job = (new $job($to, $details, $cc, $bcc))
	            ->delay(
	            	now()
	            	->addSeconds(2)
	            );

	        dispatch($job)->onQueue('emails');

	        return $this->sendResponse($job);
	    } catch (\Exception $e) {
	    	return $this->sendError($e->getMessage() . $e->getLine());
	    } catch (\Throwable $e) {
	    	return $this->sendError($e->getMessage() . $e->getLine());
	    } catch (\Error $e) {
	    	return $this->sendError($e->getMessage() . $e->getLine());
	    }
    }
}
