<?php

namespace Formation\DataTable;

use Exception;
use Formation\DataTable\WithDataTable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Events\AfterChunk;

class WithImport extends Component implements ToModel, SkipsOnFailure, SkipsOnError, WithEvents, WithHeadingRow, SkipsEmptyRows, WithChunkReading, ShouldQueue
{
    use SkipsFailures, SkipsErrors, RegistersEventListeners, RemembersChunkOffset, RemembersRowNumber;
    use WithDataTable;

    public $moduleSection, $moduleGroup, $module, $model, $mappedColumns, $withImportType, $chunkSize, $form, $import, $user, $importTable, $importErrorTable, $importIdColumn;

    public function __construct($moduleSection, $moduleGroup, $module, $model, $mappedColumns, $withImportType, $chunkSize, $import, $user)
    {
        $this->moduleSection = $moduleSection;
        $this->moduleGroup = $moduleGroup;
        $this->module = $module;
        $this->model = $model;
        $this->mappedColumns = $mappedColumns;
        $this->withImportType = $withImportType;
        $this->chunkSize = $chunkSize;
        $this->import = $import;
        $this->user = $user;

        if ($withImportType ==  'import'){
            $this->importTable = 'App\Models\Import';
            $this->importErrorTable = 'App\Models\ImportError';
            $this->importIdColumn = 'import_id';
            $this->type = 'create';
        }

        if ($withImportType ==  'bulkEdit'){
            $this->importTable = 'App\Models\BulkEdit';
            $this->importErrorTable = 'App\Models\BulkEditError';
            $this->importIdColumn = 'bulk_edit_id';
            $this->type = 'edit';
        }
    }

    // public function rules(): array
    // {
    //     return $this->rules;
    // }

    public function transform($row): array
    {
        $mappedRow = [];

        if ($this->type == 'edit'){
            Auth::login($this->user);

            if (!array_key_exists("id", $row)) {
                throw new Exception("Column 'id' not found.");
                
            }

            $this->formId = $row['id'];
            $this->executeEdit();
        }
        
        if ($this->type == 'create'){
            $this->editing = $this->model::make();
        }
        
        foreach ($this->mappedColumns as $key => $value) {
            $mappedRow[$key] = $row[$value];
        }

        $this->prepareEditing($mappedRow);

        return $mappedRow;
    }

    public function prepareEditing($mappedRow)
    {
        foreach ($this->form->items as $tab){
            foreach ($tab->items as $card){
                foreach ($card->items as $section){
                    foreach ($section->items as $column)
                    {
                        foreach ($column->items as $field)
                        {
                            $fieldName = $field->name;

                            if ($field->type === "preset")
                            {
                                $this->editing->$fieldName = $field->default;
                            } else {
                                if (isset($mappedRow[$fieldName])){
                                    $this->editing->$fieldName = $mappedRow[$fieldName];
                                }
                            }
                        } 
                    }
                }
            }
        }
    }

    public function model(array $row)
    {
        $currentRowNumber = $this->getRowNumber();

        try {
            Auth::login($this->user);
            $this->form = $this->getFormProperty();
            
            $this->transform($row);

            $this->executeSave();

            $import = $this->importTable::find($this->import->id);
            $totalInserted = $import->total_inserted;
            $import->update([
                'total_inserted' => $totalInserted + 1,
            ]);
        } catch (\Exception $e) {
            if ($e instanceof AuthorizationException) {
                $this->importErrorTable::create([
                    $this->importIdColumn => $this->import->id,
                    'row' => $currentRowNumber,
                    'status' => 'failed',
                    'remark' => 'This action is not authorized.',
                    'created_by' => $this->user->id,
                    'updated_by' => $this->user->id,
                ]);
            }else {
                $this->importErrorTable::create([
                    $this->importIdColumn => $this->import->id,
                    'row' => $currentRowNumber,
                    'status' => 'failed',
                    'remark' => $e->getMessage(),
                    'created_by' => $this->user->id,
                    'updated_by' => $this->user->id,
                ]);
            }
        } catch (\Throwable $t) {
            if ($t instanceof UniqueConstraintViolationException) {
                $this->importErrorTable::create([
                    $this->importIdColumn => $this->import->id,
                    'row' => $currentRowNumber,
                    'status' => 'failed',
                    'remark' => 'Integrity constraint violation. Duplicate entry detected.',
                    'created_by' => $this->user->id,
                    'updated_by' => $this->user->id,
                ]);
            } else {
                $this->importErrorTable::create([
                    $this->importIdColumn => $this->import->id,
                    'row' => $currentRowNumber,
                    'status' => 'failed',
                    'remark' => $t->getMessage(),
                    'created_by' => $this->user->id,
                    'updated_by' => $this->user->id,
                ]);
            }
        }
    }

    public function beforeImport(BeforeImport $event)
    {
        $getTotalRows = $event->getReader()->getTotalRows();
        $totalRows = $getTotalRows[array_key_first($getTotalRows)] - 1;
        $totalChunks = ceil($totalRows / $this->chunkSize());
        $totalChunks = $totalChunks == 0 ? 1 : $totalChunks;

        $this->import->update([
            'status' => 'progressing',
            'started_at' => now(),
            'total_inserted' => 0,
            'total_failed' => 0,
            'total_rows' => $totalRows,
            'total_completed_chunk' => 0,
            'total_chunk' => $totalChunks,
        ]);
    }

    public function afterChunk(AfterChunk $event)
    {
        $import = $this->importTable::find($this->import->id);
        $totalCompletedChunk = $import->total_completed_chunk;

        $import->update([
            'total_completed_chunk' => $totalCompletedChunk+1,
        ]);

        if ($import->total_completed_chunk == $import->total_chunk) {
            $totalFailed = $import->total_rows - $import->total_inserted;

            $import->update([
                'status' => 'completed',
                'total_failed' => $totalFailed,
                'ended_at' => now(),
            ]);
        }
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        $failureRecords = collect($failures)->jsonSerialize();

        foreach ($failureRecords as $failureRecord) {
            foreach ($failureRecord['errors'] as $error) {
                $this->importErrorTable::create([
                    $this->importIdColumn => $this->import->id,
                    'row' => $failureRecord['row'],
                    'status' => 'failed',
                    'remark' => $error,
                    'created_by' => $this->user->id,
                    'updated_by' => $this->user->id,
                ]);
            }
        }
    }

    /**
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        $currentRowNumber = $this->getRowNumber();

        $this->importErrorTable::create([
            $this->importIdColumn => $this->import->id,
            'row' => $currentRowNumber,
            'status' => 'failed',
            'remark' => $e->getMessage(),
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);
    }

    public function chunkSize(): int
    {
        return $this->chunkSize;
    }
}
