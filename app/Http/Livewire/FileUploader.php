<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileUploader extends Component
{
    use WithFileUploads;

    public $serializedFiles = null;
    public $files = null;
    public $importType;
    public $sampleFile;
    public $model;
    public $headers;
    public string $folderPath = '/';
    public string $name;
    public bool $required = false;
    public bool $disabled = false;
    public int $maximumNumberOfFile = 1;
    public array $rules;
    protected $validationAttributes = [];
    public $mimeTypes= [
        'application/excel',
        'application/x-msexcel',
        'application/x-ms-excel',
        'application/x-excel',
        'application/x-dos_ms_excel',
        'application/xls',
        'application/x-xls',
        '.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/vnd.ms-excel',
        'text/xsl',
        'text/plain',
        'text/csv',
        'csv',
        'txt',
        'xls',
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/vnd.msexcel',
        '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        '.docx',
        'application/powerpoint',
        'application/vnd.ms-powerpoint',
        'application/vnd.ms-office',
        'application/pdf',
        'pdf',
    ];

    public $mimeTypeIcons = [
        'application/excel' => 'default-excel-icon.png',
        'application/x-msexcel' => 'default-excel-icon.png',
        'application/x-ms-excel' => 'default-excel-icon.png',
        'application/x-excel' => 'default-excel-icon.png',
        'application/x-dos_ms_excel' => 'default-excel-icon.png',
        'application/xls' => 'default-excel-icon.png',
        'application/x-xls' => 'default-excel-icon.png',
        '.xlsx' => 'default-excel-icon.png',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'default-excel-icon.png',
        'application/vnd.ms-excel' => 'default-excel-icon.png',
        'application/vnd.ms-excel' => 'default-excel-icon.png',
        'text/xsl' => 'default-excel-icon.png',
        'text/plain' => 'default-excel-icon.png',
        'text/csv' => 'default-excel-icon.png',
        'csv' => 'default-excel-icon.png',
        'txt' => 'default-excel-icon.png',
        'xls' => 'default-excel-icon.png',
        'text/x-comma-separated-values' => 'default-excel-icon.png',
        'text/comma-separated-values' => 'default-excel-icon.png',
        'application/vnd.msexcel' => 'default-excel-icon.png',
        '.doc' => 'default-word-icon.png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'default-word-icon.png',
        '.docx' => 'default-word-icon.png',
        'application/powerpoint' => 'default-powerpoint-icon.png',
        'application/vnd.ms-powerpoint' => 'default-powerpoint-icon.png',
        'application/vnd.ms-office' => 'default-powerpoint-icon.png',
        'application/pdf' => 'default-pdf-icon.png',
        'pdf' => 'default-pdf-icon.png',
    ];

    public $mimeTypeImages = [
        'image/png',
        'png',
        'image/jpeg',
        'jpe',
        'jpeg',
        'jpg',
        'image/gif',
        'gif',
        'image/bmp',
        'bmp',
        'image/vnd.microsoft.icon',
        'ico',
        'image/tiff',
        'tiff',
        'tif',
        'image/svg+xml',
        'svg',
        'svgz',
    ];

    protected $listeners = ['updateFiles'];

    public function mount(array|string|null $files): void
    {
        if(isset($files)) {
            if(gettype($files) === "string") {
                $this->files = explode('|', $files);
            }            
        } else {
            $this->files = [];
        }
    }

    public function render()
    {
        return view('components.input.file');
    }

    public function finishUpload($name, $tmpPath, $isMultiple) 
    {
        $this->cleanupOldUploads();
 
        if($isMultiple){
            $files = collect($tmpPath)->map(function ($i) {
                return TemporaryUploadedFile::createFromLivewire($i);
            })->toArray();
            $this->emitSelf('upload:finished', $name, collect($files)->map->getFilename()->toArray());
     
            $files = array_merge($this->getPropertyValue($name), $files);
            $this->syncInput($name, $files);
        } else {
            $file = TemporaryUploadedFile::createFromLivewire($tmpPath[0]);
            $this->emit('upload:finished', $name, [$file->getFilename()])->self();

            if (is_array($value = $this->getPropertyValue($name))) {
                $file = array_merge($value, [$file]);
            }
            $this->syncInput($name, $file);
        }

        $this->emitUploadingProgressToParent(false);
    } 

    public function getFormattedFileSize(int $size): string
    {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    public function updatedFiles($value)
    {  
        try {
            if(sizeof($value) > 1){
                array_shift($this->files);
            }
            $this->validate();
            $this->emitFilesToParent($this->name);
        } catch (\Illuminate\Validation\ValidationException $e) {

            if(sizeof($this->files) > 0)
                $this->removeFile(null, sizeof($this->files) - 1);
            
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'files' => [$e->validator->errors()->first()],
            ]);

            throw $error;
        }
    }

    public function rules() {
        $rules = [];

        if($this->required) {
            $rules["files"] = ['required'];
        }
        
        foreach($this->files as $key => $file) {
            if($file instanceof TemporaryUploadedFile) {
                $this->validationAttributes["files.{$key}"] = '';
                $rules["files.{$key}"] = $this->rules;
            }else {
                $this->validationAttributes["files.{$key}"] = '';
                $rules["files.{$key}"] = $this->rules;
            }
        }

        return $rules;
    }

    public function removeFile($field, $key) {
        if($this->files[$key] instanceof TemporaryUploadedFile)
            $this->files[$key]->delete();

        $temp = $this->files;
        unset($temp[$key]);
        $temp = array_values($temp);
        $this->files = $temp;

        if(isset($field)) 
            $this->emitUp('setFiles', $field, $this->files);

        if(sizeof($this->files) > 0)
            $this->validate();
    }

    private function emitFilesToParent(): void {
        if($this->maximumNumberOfFile > 1){
        }else {
            if(sizeof($this->files) > 1){
                array_shift($this->files);
            }
        }

        $tempFiles = [];

        foreach($this->files as $key => $file) {
            $temp = [];
    
            if($file instanceof TemporaryUploadedFile) {
                $category = 'temporary';
    
                $temp['path'] = $file->getRealPath();
                $temp['position'] = $key;
                $temp['serializedFile'] = $file->serializeForLivewireResponse();
            } else {
                $category = 'existing';
    
                $temp['path'] = $file;
                $temp['position'] = $key;
                $temp['serializedFile'] = null;
            }
    
            $tempFiles[$category][] = $temp;
        }

        $this->emitUp('setFiles', $this->name, $tempFiles);
        $this->emitUp('updateFolderPath', $this->name, $this->folderPath);
        $this->emitUp('updateFileColumnName', $this->name);
    }

    private function emitUploadingProgressToParent(bool $isUploading): void {
        $this->emitUp('getUploadingProgress', $isUploading);
    }

    public function downloadFile(int $key): ?StreamedResponse {
        $response = null;
        $fileName = $this->files[$key] ?? null;
        
        if(isset($fileName)) {
            $s3 = Storage::disk('s3');

            $response = $s3->exists($this->folderPath . $fileName) ? $s3->download($this->folderPath . $fileName) : null;
        }

        return $response;
    }

    public function downloadSampleFile()
    {
        $data = $this->model::first();
        $headers = $this->headers->toArray();

        if($this->importType == 'bulkEdit'){
            array_unshift($headers, "id");
        }

        return response()->streamDownload(function() use ($headers, $data){
            echo implode(',', $headers);
            if(isset($data)){
                echo "\n";
                foreach($headers as $columnName){
                    $datas[] = isset($data->$columnName) ? $data->$columnName : '';
                }
                echo implode(',', $datas);
            }
        }, collect(explode('\\', $this->model))->last().date('YmdHis').'.csv');
    }

    public function updateFiles($newFiles)
    {
        $this->files = $newFiles;
    }

    public function previewImage(string $url)
    {
        $this->emitUp('previewImage', $url);
    }
}