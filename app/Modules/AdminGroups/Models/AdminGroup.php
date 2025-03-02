<?php

namespace App\Modules\AdminGroups\Models;

use App\Modules\Countries\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class AdminGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'admin_groups';

    protected $fillable = [
        'code',
        'english',
        'arabic',
        'bengali',
        'country_id',
        'is_default',
        'is_draft',
        'is_active',
        'is_deleted',
        'drafted_at',
        'flag',
    ];

    public static function rules($adminGroupId = null)
    {
        return [
            'code' => [
                'required',
                'string',
                'max:191',
                Rule::unique('admin_groups', 'code')
                    ->ignore($adminGroupId)
                    ->whereNull('deleted_at'),
            ],
            'english' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'bengali' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'is_default' => 'boolean',
            'is_draft' => 'boolean',
            'is_active' => 'boolean',
            'is_deleted' => 'boolean',
            'drafted_at' => 'nullable|date',
            'flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
    public static function bulkRules()
    {
        return [
            'adminGroups' => 'required|array|min:1',
            'adminGroups.*.id' => [
                'required',
                Rule::exists('admin_groups', 'id')->whereNull('deleted_at')
            ],
            'adminGroups.*.code' => [
                'required',
                'string',
                'max:45',
                function ($attribute, $value, $fail) {
                    $adminGroupId = request()->input(str_replace('.code', '.id', $attribute));
                    $exists = AdminGroup::where('code', $value)
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $adminGroupId)
                        ->exists();

                    if ($exists) {
                        $fail('The code "' . $value . '" has already been taken.');
                    }
                },
                // New rule: Check for duplicates within the request
                function ($attribute, $value, $fail) {
                    $adminGroups = request()->input('adminGroups', []);
                    $codes = array_column($adminGroups, 'code');

                    $matches = array_keys(array_filter($codes, fn($num) => $num === $value)); // Get all indexes
                    if (count($matches) > 1) {
                        // Check if this is not the only occurrence for this id
                        $currentIndex = (int) str_replace('adminGroups.', '', explode('.', $attribute)[1]); // Get current index
                        $otherMatches = array_filter($matches, fn($index) => $index !== $currentIndex); // Get all other indexes
                        if (!empty($otherMatches)) {
                            $fail('The code "' . $value . '" is duplicated within the request.');
                        }
                    }
                },
            ],
            'adminGroups.*.english' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'adminGroups.*.bengali' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'adminGroups.*.arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'adminGroups.*.is_default' => 'boolean',
            'adminGroups.*.draft' => 'boolean',
            'adminGroups.*.drafted_at' => 'nullable|date',
            'adminGroups.*.is_active' => 'boolean',
            'adminGroups.*.flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'adminGroups.*.country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function listRules()
    {
        return [
            'draft' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
            'is_updated' => 'nullable|boolean',
            'country_id' => [
                'nullable',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public function country() : belongsTo
    {
        return $this->belongsTo(Country::class,'country_id');
    }
}
