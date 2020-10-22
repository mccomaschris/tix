<div>
    @unless($uploaded)
    <form wire:submit.prevent="import">
        <div class="py-12 flex flex-col items-center justify-center ">
            <div class="flex items-center space-x-2 text-xl">
                <input wire:model="upload" type="file" id="upload" name="upload">
                @error('upload') <div class="mt-3 text-red-500 text-sm">{{ $message }}</div> @enderror
                {{ $upload }}
                <div class="my-3">
                    <input type="submit" name="Import" class="px-2 py-3 rounded text-white bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 border-indigo-600">
                </div>
            </div>
        </div>
    </form>
    @else
        <div class="py-12 flex flex-col items-center justify-center ">
            <div class="flex items-center space-x-2 text-xl">

                <span wire:click="exportSelected" class="px-2 py-3 rounded text-white bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 border-indigo-600">Download</span>
            </div>
        </div>
    @endunless
</div>
