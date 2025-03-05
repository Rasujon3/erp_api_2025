<?php

namespace App\Modules\AdminGroups\Models;

use App\Modules\Countries\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        // 'country_id',
        'is_default',
        'is_draft',
        'is_active',
        'is_deleted',
        'drafted_at',
        'flag',
        'group_name',
        'deleted_at',
    ];

    public static function rules($adminGroupId = null)
    {
        return [
            'code' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('admin_groups', 'code')
                    ->ignore($adminGroupId)
                    ->whereNull('deleted_at'),
            ],
            'english' => 'nullable|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'bengali' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
//            'country_id' => 'required|array|min:1',
            'country_id' => [
                $adminGroupId ? 'nullable' : 'required',
                'string', // Accept as string initially
                function ($attribute, $value, $fail) {
                    // Attempt to parse the string as a JSON array
                    $countryIds = json_decode($value, true);

                    // Check if parsing failed or result is not an array
                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($countryIds) || empty($countryIds)) {
                        return $fail('The ' . $attribute . ' must be a valid JSON array of country IDs (e.g., "[219, 220]").');
                    }

                    // Validate each country ID
                    foreach ($countryIds as $id) {
                        if (!is_numeric($id) || $id <= 0) {
                            $fail('Each ' . $attribute . ' must be a positive numeric ID.');
                            return;
                        }

                        $exists = \DB::table('countries')
                            ->where('id', $id)
                            ->whereNull('deleted_at')
                            ->exists();

                        if (!$exists) {
                            $fail('The country ID ' . $id . ' does not exist or is soft-deleted.');
                            return;
                        }
                    }
                },
            ],
            'is_default' => 'nullable|boolean',
            'is_draft' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
            'drafted_at' => 'nullable|date',
            'flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'group_name' => 'nullable|string|max:191',
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
                'nullable',
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
            'adminGroups.*.english' => 'nullable|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'adminGroups.*.bengali' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'adminGroups.*.arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'adminGroups.*.is_default' => 'nullable|boolean',
            'adminGroups.*.draft' => 'nullable|boolean',
            'adminGroups.*.drafted_at' => 'nullable|date',
            'adminGroups.*.is_active' => 'nullable|boolean',
            'adminGroups.*.flag' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'adminGroups.*.country_id' => 'required|array|min:1', // Now an array
            'adminGroups.*.country_id.*' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at'),
            ],
            'adminGroups.*.group_name' => 'nullable|string|max:191',
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
    public function countries()
    {
        return $this->belongsToMany(
            Country::class,
            'group_countries',
            'admin_group_id',
            'country_id'
        )
            ->withTimestamps();
    }
}
