<?php
	
	namespace App\Http\Controllers;
	
	use Cloudinary\Utils\SignatureVerifier;
	use Illuminate\Http\Request;
	use Illuminate\Notifications\Messages\MailMessage;
	
	class WebhookController extends Controller {
		public function cloudinary(Request $request) {
			//Verification
			$verified = SignatureVerifier::verifyNotificationSignature(json_encode($request), $request->header('X-Cld-Timestamp'), $request->header('X-Cld-Signature'));
			
			
			// if it is a charge event, verify and confirm it is a successful transaction
			if ($verified) {
				$secureUrl = $request->secure_url;
				
				return view('livewire.view-slideshow', ['slideshow_url' => $secureUrl]);
			}
			
			return response('Unverified', 401);
		}
	}
