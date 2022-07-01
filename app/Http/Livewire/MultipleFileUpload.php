<?php
	
	namespace App\Http\Livewire;
	
	use Carbon\Carbon;
	use Cloudinary\Api\ApiUtils;
	use Illuminate\Support\Facades\Http;
	use Livewire\Component;
	use Livewire\WithFileUploads;
	
	class MultipleFileUpload extends Component {
		use WithFileUploads;
		
		public $files = [];
		public $slides = [];
		
		// Needed to specify the type of media file for our slideshow
		public $imageExt = ['jpeg', 'jpg', 'png', 'gif',];
		
		public function uploadFiles() {
			$this->validate([
				'files'   => [
					'required',
					'max:102400'
				],
				'files.*' => 'mimes:jpeg,jpg,png,gif,avi,mp4,webm,mov,ogg,mkv,flv,m3u8,ts,3gp,wmv,3g2,m4v'
			]);
			
			foreach ($this->files as $file) {
				$media = cloudinary()->upload($file->getRealPath(), [
					'folder'         => 'video-slideshow',
					'public_id'      => $file->getClientOriginalName(),
					'transformation' => [
						'aspect_ratio' => '9:16',
						'gravity'      => 'auto', //can be face, face etc
						'crop'         => 'fill'
					]
				])->getPublicId();
				
				if (in_array($file->getClientOriginalExtension(), $this->imageExt)) {
					$this->slides[] = ['media' => 'i:'.$media];
				} else {
					$this->slides[] = ['media' => 'v:'.$media];
				}
			}
			
			$manifestJson = json_encode([
				"w"    => 540,
				"h"    => 960,
				"du"   => 60,
				"vars" => [
					"sdur"   => 3000,
					"tdur"   => 1500,
					"slides" => $this->slides,
				],
			]);
			
			$cloudName = env('CLOUDINARY_CLOUD_NAME');
			$timestamp = (string) Carbon::now()->unix();
			$signingData = [
				'timestamp'     => $timestamp,
				'manifest_json' => $manifestJson,
				'public_id'     => 'test_slideshow',
				'folder'        => 'video-slideshow',
				'notification_url' => env('CLOUDINARY_NOTIFICATION_URL')
			];
			$signature = ApiUtils::signParameters($signingData, env('CLOUDINARY_API_SECRET'));
			
			$response = Http::post("https://api.cloudinary.com/v1_1/$cloudName/video/create_slideshow", [
				'api_key'          => env('CLOUDINARY_API_KEY'),
				'signature'        => $signature,
				'timestamp'        => $timestamp,
				'manifest_json'    => $manifestJson,
				'resource_type'    => 'video',
				'public_id'        => 'test_slideshow',
				'folder'           => 'video-slideshow',
				'notification_url' => env('CLOUDINARY_NOTIFICATION_URL')
			]);
			
			// Determine if the status code is >= 200 and < 300...
			if ($response->successful()) {
				session()->flash('message', 'Slideshow generated successfully!');
			} else {
				session()->flash('error', 'Slideshow generation failed! Try again later.');
			}
		}
		
		public function render() {
			return view('livewire.multiple-file-upload');
		}
	}
