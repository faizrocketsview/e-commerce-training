<div x-data="{
    isDropping: false,
    isUploading: false,
    progress: {{ $disabled ? 1 : 0 }},
    handleFileSelect(event, id) {
        if (event.target.files.length && !this.isUploading) {
            this.uploadFiles(event.target.files[0], null);
        }
    },
    handleFileDrop(event) {
        if (event.dataTransfer.files.length && !this.isUploading) {
            this.uploadFiles(event.dataTransfer.files[0], null)
        }
    },
    async uploadFiles(files, id) {
        const $this = this;

        $this.isUploading = true;
        const name = id != null ? 'files.' + id : 'files';

        Livewire.emit('getUploadingProgress', true);

        $this.$wire.upload(name, files, (uploadedFilename) => {
            $this.isUploading = false;
            $this.progress = 0;
        }, () => {
            $this.isUploading = false;
            $this.progress = 0;
        }, (event) => {
            $this.progress = event.detail.progress;
        }, () => {
            $this.isUploading = false;
            $this.progress = 0;
        });
    }
}">

    @foreach ($files as $file)
        @php
            $url = null;
            if ($file instanceof Livewire\TemporaryUploadedFile) {
                $mimeType = $file->getMimeType();
                if (in_array($mimeType, $mimeTypes)) {
                    $url = asset('images/formation/' . $mimeTypeIcons[$mimeType]);
                } else {
                    $url = $file->temporaryUrl();
                }
            } elseif (gettype($file) === 'string') {
                $mimeType = explode('.', $file)[sizeof(explode('.', $file)) - 1];
                if (in_array($mimeType, $mimeTypes)) {
                    $url = asset('images/formation/' . $mimeTypeIcons[$mimeType]);
                } else {
                    $path = trim(($folderPath ?? '/'), '/');
                    $key = ($path ? $path.'/' : '').ltrim($file, '/');
                    $url = Storage::disk('s3')->exists($key)
                        ? Storage::disk('s3')->temporaryUrl($key, now()->addDay())
                        : (Storage::disk('s3')->exists('public/default.png')
                            ? Storage::disk('s3')->temporaryUrl('public/default.png', now()->addDay())
                            : asset('images/formation/default-file.png'));
                }
            }
        @endphp

        @if (!$disabled)
            <div class="relative flex flex-col mt-1" x-on:drop="isDropping = false"
                x-on:drop.prevent="handleFileDrop($event)" x-on:dragover.prevent="isDropping = true"
                x-on:dragleave.prevent="isDropping = false">
            @else
                <div class="relative flex flex-col mt-1">
        @endif

        @if (!$disabled)
            <div class="absolute top-0 bottom-0 left-0 right-0 z-30 flex items-center justify-center bg-slate-50 opacity-90"
                x-show="isDropping" x-cloak>
                <span class="text-xl text-gray-700">Release file to upload!</span>
            </div>
        @endif

        <div class="flex flex-col pb-2">
            <label for="file-{{ $name }}-{{ $loop->index }}"
                class="relative group flex flex-col items-center justify-center h-40 p-2 bg-white border border-gray-300 rounded-lg
                    @if ($errors->has('files.' . $loop->index)) border-red-500 @endif"
                :class="progress > 0 ? 'cursor-not-allowed' : 'cursor-pointer'">
                <img class="p-0.5 object-center rounded-lg text-center bg-center h-full delay-75 transition ease-in-out"
                    src="{{ $url }}"></img>

                @if (!$disabled)
                    <div class="rounded-sm text-center bg-center absolute top-2 right-1.5 opacity-40 hover:opacity-80"
                        :class="progress > 0 ? 'cursor-not-allowed' : 'cursor-pointer'"
                        wire:click.prevent="removeFile('{{ $name }}', '{{ $loop->index }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="absolute w-6 h-6 right-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>
                @endif

                <div id="updating-{{ $name }}-{{ $loop->index }}"
                    class="bg-gradient-to-t w-full h-16 bg-white absolute bottom-0 left-0 pt-2 px-2 hidden flex-col rounded-b-2xl opacity-40 group-hover:opacity-80"
                    style="transition: width 1s" x-cloak>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-sky-950"
                            :class="progress > 0 ? 'animate-pulse' : ''">Uploading...</span>
                        <span class="text-sm font-medium text-sky-950" x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${progress}%;`"></div>
                    </div>
                </div>

                @if (isset($mimeType) && in_array($mimeType, $mimeTypeImages))
                <div 
                    class="absolute w-6 h-6 rounded-sm text-center bg-center bottom-2 @if(!$file instanceof Livewire\TemporaryUploadedFile) {{ 'right-9' }} @else {{ 'right-1.5' }} @endif cursor-pointer opacity-40 hover:opacity-80"
                    wire:click.prevent="previewImage('{{$url}}')" x-cloak>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" width="24" height="24"   
                        stroke="currentColor"  stroke-linecap="round" stroke-linejoin="round" class="absolute w-full h-full">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                    </svg>
                </div>
                @endif

                @if (!$file instanceof Livewire\TemporaryUploadedFile)
                    <div wire:target="downloadFile" wire:loading.class.add="hidden"
                        class="absolute w-6 h-6 rounded-sm text-center bg-center bottom-2 right-1.5 cursor-pointer opacity-40 hover:opacity-80"
                        wire:click.prevent="downloadFile('{{ $loop->index }}')" x-cloak>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="absolute w-full h-full">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </div>

                    <div class="absolute bottom-2 right-1.5 hidden" wire:target="downloadFile"
                        wire:loading.class.remove="hidden" x-cloak>
                        <svg class="text-gray-300 animate-spin" viewBox="0 0 64 64" fill="none"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                            <path
                                d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
                                stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                            <path
                                d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
                                stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                                class="text-sky-950">
                            </path>
                        </svg>
                    </div>
                @endif
            </label>

            <div>
                @error('files.' . $loop->index)
                    <p class="text-red-500 text-sm mt-1 a">{{ $message }}</p>
                @enderror
            </div>

            @if (!$disabled)
                <input class="hidden" type="file" id="file-{{ $name }}-{{ $loop->index }}"
                    @change="handleFileSelect($event, {{ $loop->index }})" :disabled="progress > 0" />
            @endif
        </div>
</div>
@endforeach


@if (count($files) < $maximumNumberOfFile)
    @if (!$disabled)
        <div class="relative flex flex-col mt-1" x-on:drop="isDropping = false"
            x-on:drop.prevent="handleFileDrop($event)" x-on:dragover.prevent="isDropping = true"
            x-on:dragleave.prevent="isDropping = false">
        @else
            <div class="relative flex flex-col mt-1">
    @endif

    @if (!$disabled)
        <div class="absolute top-0 bottom-0 left-0 right-0 z-30 flex items-center justify-center bg-slate-50 opacity-90"
            x-show="isDropping" x-cloak>
            <span class="text-xl text-gray-700">Release file to upload!</span>
        </div>
    @endif

    <div class="flex pb-2">
        <div class="flex w-full">
            <label for="file-{{ $name }}"
                class="w-full relative flex flex-col items-center justify-center h-40 p-4 bg-white border border-gray-300 rounded-lg"
                :class="progress > 0 ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-slate-50'">
                <svg class="w-10 h-10 mb-2 text-current-50" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="#E5E4E2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-sm text-sky-950 text-center mb-1">Click here to select file to upload</h3>
                <span class="text-xs text-slate-400 text-center">(Or drag file to the page)</span>

                <div id="add-{{ $name }}" class="absolute bottom-4 left-2 right-2" style="transition: width 1s"
                    x-show="isUploading" x-cloak>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-sky-950"
                            :class="progress > 0 ? 'animate-pulse' : ''">Uploading...</span>
                        <span class="text-sm font-medium text-sky-950" x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-blue-600 h-2.5 rounded-full" :style="`width: ${progress}%;`"></div>
                    </div>
                </div>
            </label>
        </div>
    </div>

    <div>
        @error('files')
            <p class="text-red-500 text-sm mt-1 b">{{ $message }}</p>
        @enderror
    </div>

    @if (!$disabled)
        <input class="hidden" type="file" id="file-{{ $name }}" @change="handleFileSelect"
            :disabled="progress > 0" />
    @endif
    </div>
@endif

@if ($this->sampleFile)
    <div class="flex text-sm">
        <span>
            <a wire:click="downloadSampleFile()" wire:loading.delay.long.remove wire:target="downloadSampleFile"
                class="cursor-pointer text-blue-800">Download Sample File</a>
            <div wire:loading.delay.long wire:target="downloadSampleFile"
                class="relative top-0.5 items-center pt-1 border-gray-300 h-3 w-3 animate-spin rounded-full border-2 border-t-blue-600">
            </div>
        </span>
    </div>
@endif

</div>
