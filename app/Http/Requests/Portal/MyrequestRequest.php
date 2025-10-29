<?php

namespace App\Http\Requests\Portal;

use App\Models\RequestDate;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class MyrequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        switch($this->option){
            case 'cto':
                return [
                    'date_type' => 'sometimes|required',
                    'purpose' => 'sometimes|required',
                    'dates' => 'sometimes|required|array|min:1',
                    'dates.*.date' => 'required|date',
                    'dates.*.timeOfDay' => 'required|string',
                    'document' => 'nullable|mimes:pdf|max:2000'
                ];
            break;
            case 'leave':
                return [
                    'date_type' => 'sometimes|required',
                    'type_id' => 'sometimes|required',
                    'detail_id' => 'sometimes|required',
                    'details' => 'required_if:detail.others,specify illness,specify reason,specify',
                    'dates' => 'sometimes|required|array|min:1',
                    'dates.*.date' => 'required|date',
                    'dates.*.timeOfDay' => 'required|string',
                    // 'document' => 'required_if:type.required_document,1',
                ];
            break;
        }
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $this->checkDateOverlap($validator);
        });
    }

    protected function checkDateOverlap($validator)
    {
        $userId = $this->user()->id;
        $dates = $this->dates ?? [];

        if (empty($dates)) return;

        // Determine continuous or non-continuous
        if ($this->date_type != 'Multiple Dates (non-continuous)') {
            $datesOnly = array_column($dates, 'date');
            $newStart = min($datesOnly);
            $newEnd = max($datesOnly);

            $hasOverlap = RequestDate::whereHas('request', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->where(function($q) use ($newStart, $newEnd) {
                    $q->where(function($inner) use ($newStart, $newEnd) {
                        $inner->where('start', '<=', $newEnd)
                              ->where('end', '>=', $newStart);
                    });
                })
                ->exists();

            if ($hasOverlap) {
                $validator->errors()->add('dates', 'You already have a request within these dates.');
            }

        } else {
            // Non-continuous mode: check per date
            foreach ($dates as $d) {
                $date = $d['date'];
                $hasOverlap = RequestDate::whereHas('request', function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                    ->where('start', '<=', $date)
                    ->where('end', '>=', $date)
                    ->exists();

                if ($hasOverlap) {
                    $validator->errors()->add('dates', "You already have a request on {$date}.");
                    break;
                }
            }
        }
    }

    public function messages()
    {
         switch($this->option){
            case 'leave':
                return [
                    'date_type.required' => 'The date type field is required.',
                    'type_id.required' => 'The type field is required.',
                    'detail_id.required' => 'The detail field is required.',

                    'details.required_if' => 'Details are required when others is set to "Specify Illness", "Specify Reason", or "Specify".',
                    'document.required_if' => 'Document filed is require.',

                    'dates.required' => 'At least one date is required.',
                    'dates.array' => 'Dates must be in a valid list.',
                    'dates.min' => 'You must select at least one date.',

                    'dates.*.date.required' => 'Each date is required.',
                    'dates.*.date.date' => 'Each date must be a valid date.',

                    'dates.*.timeOfDay.required' => 'Each time of day is required.',
                    'dates.*.timeOfDay.string' => 'Each time of day must be a valid string.',
                    
                ];
            break; 
            case 'cto':
                return [
                    'date_type.required' => 'The date type field is required.',
                    'purpose.required' => 'The purpose field is required.',

                    'dates.required' => 'At least one date entry is required.',
                    'dates.array' => 'Dates must be provided in a valid list.',
                    'dates.min' => 'You must select at least one date.',

                    'dates.*.date.required' => 'Each date entry is required.',
                    'dates.*.date.date' => 'Each date must be a valid date format.',

                    'dates.*.timeOfDay.required' => 'The time of day is required for each date.',
                    'dates.*.timeOfDay.string' => 'The time of day must be a valid text value.',

                    'document.mimes' => 'The document must be a PDF file.',
                    'document.max' => 'The document size may not exceed 2MB.',
                ];
            break;
          
         }
    }
}
