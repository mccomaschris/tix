<div>

    @unless($uploaded)
    <form wire:submit.prevent="import">
        <div class="py-12 flex flex-col items-center justify-center ">
            {{-- <div class="mb-4 w-full lg:w-1/3">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="exclude_ids">
                  Exlude IDs
                </label>
                <input wire:model="exclude_ids" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="exclude_ids" type="text" placeholder="Exclude IDs">
            </div> --}}
            <div class="flex items-center space-x-2 text-xl">
                <input wire:model="upload" type="file" id="upload" name="upload">
                @error('upload') <div class="mt-3 text-red-500 text-sm">{{ $message }}</div> @enderror
                <div class="my-3">
                    <input type="submit" name="Import" class="px-2 py-3 rounded text-white bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 border-indigo-600">
                </div>
            </div>
        </div>
    </form>
    <div class="w-full flex flex-col items-center justify-center" wire:loading wire:target="import">
        <div class="w-1/3 mx-auto bg-indigo-200 text-indigo-800 px-4 py-4 my-6 rounded">
            Uploading CSV...
        </div>
    </div>
    @else
        <div class="py-12 flex flex-col items-center justify-center ">
            <div class="flex items-center space-x-2 text-xl">
                <span wire:click="exportSelected" class="px-2 py-3 rounded text-white bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 border-indigo-600">Download</span>
                <span wire:click="resetAll" class="text-indigo-600 underline">Reset</span>
            </div>
        </div>
    @endunless
</div>
