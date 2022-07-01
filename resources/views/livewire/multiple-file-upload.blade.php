<div>
	{{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
	<div class="flex-row">
		<div class="spinner-border spinner-border-sm m-3 end-0" role="status" wire:loading wire:target="uploadFiles"></div>
	</div>
	@if (session()->has('message'))
		<div class="alert alert-success alert-dismissible fade show m-3" role="alert">
			<h4 class="alert-heading">Holy guacamole success!</h4>
			<p>{{ session('message') }}</p>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@elseif(session()->has('error'))
		<div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
			<h4 class="alert-heading">Oops!</h4>
			<p>{{ session('error') }}</p>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@endif
	<div class="flex h-screen justify-center items-center">
		<div class="row w-75">
			<div class="row mt-4">
				@foreach($this->files as $media)
					@if ($media)
						<div class="col-sm-3 col-md-3 mb-3">
							<img class="card-img-top img-thumbnail img-fluid" src="{{ $media->temporaryUrl() }}" alt="Card image cap">
						</div>
					@endif
				@endforeach
			</div>
			<div class="col-md-12">
				<form class="mb-5" wire:submit.prevent="uploadFiles">
					<div class="form-group row mt-5 mb-3">
						<div class="input-group">
							<input type="file" class="form-control @error('files'|'files.*') is-invalid @enderror" placeholder="Choose files..." wire:model="files" multiple>
							@error('files'|'files.*')
							<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<small class="text-muted text-center mt-2" wire:loading wire:target="files">
							{{ __('Uploading') }}&hellip;
						</small>
					</div>
					<div class="text-center">
						<button type="submit" class="btn btn-sm btn-primary w-25">
							<i class="fas fa-check mr-1"></i> {{ __('Generate Slideshow') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>