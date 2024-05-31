<div>
	<form wire:submit="save" wire:recaptcha>
		<!-- The rest of your form -->
		<input type="text" />
		<button type="submit">Submit</button>
	</form>

	@livewireRecaptcha
</div>