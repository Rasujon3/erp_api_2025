<?php

namespace App\Modules\Projects\Models;

use App\Modules\Tags\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
//    use HasFactory, SoftDeletes;
    use HasFactory;

    const BILLING_TYPES = [
        '0' => 'Monthly Base ',
        '1' => 'Call Base ',
        '2' => 'Task Hours',
    ];

    const STATUS_BADGE = [
        0 => 'badge-danger',
        1 => 'badge-primary',
        2 => 'badge-warning',
        3 => 'badge-info',
        4 => 'badge-success',
    ];

    const CARD_COLOR = [
        0 => 'danger',
        1 => 'primary',
        2 => 'warning',
        3 => 'info',
        4 => 'success',
    ];

    const STATUS = [
        '3' => 'Cancelled',
        '4' => 'Finished',
        '1' => 'In Progress',
        '0' => 'Not Started',
        '2' => 'On Hold',
    ];

    const STATUS_NOT_STARTED = 0;

    const STATUS_IN_PROGRESS = 1;

    const STATUS_ON_HOLD = 2;

    const STATUS_CANCELLED = 3;

    const STATUS_FINISHED = 4;

    protected $table = 'projects';

    protected $fillable = [
        'project_name',
        'project_code',
        'calculate_progress_through_tasks',
        'progress',
        'billing_type',
        'status',
        'estimated_hours',
        'start_date',
        'deadline',
        'description',
        'send_email',
        'customer_id',
        'project_location',
        'po_number'
    ];

    public static function rules($projectId = null)
    {
        return [
            'project_name' => 'required|unique:projects,project_name,' . $projectId . ',id',
            'project_code' => 'required|unique:projects,project_code,' . $projectId . ',id',
            'customer_id' => 'required',
            'billing_type' => 'required',
            'status' => 'required',
            'start_date' => 'required',
            'project_location'=> 'nullable',
            'po_number'=> 'nullable|unique:projects,po_number,' . $projectId . ',id'
        ];
    }
    public static $messages = [
        'customer_id.required' => 'Customer field is required.',
    ];
    public function customer(): BelongsTo
    {
//        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return HasMany
     */
    public function members(): HasMany
    {
//        return $this->hasMany(ProjectMember::class, 'owner_id');
    }

    /**
     * @return belongsToMany
     */
    public function projectContacts(): BelongsToMany
    {
//        return $this->belongsToMany(
//            Contact::class,
//            'project_contacts',
//            'project_id',
//            'contact_id'
//        )->withPivot(['contact_id']);
    }

    /**
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOwnerType(): string
    {
        return self::class;
    }

    /**
     * @param  int  $id
     * @return string
     */
    public static function getBillingTypeText($id): string
    {
        return self::BILLING_TYPES[$id];
    }

    /**
     * @param  int  $id
     * @return string
     */
    public static function getStatusText($id): string
    {
        return self::STATUS[$id];
    }

    /**
     * @param $value
     * @return string
     */
    public function getDescriptionAttribute($value): string
    {
        return $this->attributes['description'] = htmlspecialchars_decode($value);
    }
    public function services(){
//        return $this->hasMany(ProjectService::class,'project_id');
    }
    public function projectServices()
    {
//        return $this->hasMany(ProjectService::class, 'project_id');
    }
    public function invoices()
    {
//        return $this->hasMany(Invoice::class, 'project_id');
    }
    public function terms()
    {
//        return $this->hasMany(ProjectTerm::class, 'project_id');
    }
}
